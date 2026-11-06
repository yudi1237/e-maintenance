import os
import secrets
import shlex
import sys
import tempfile
import zipfile
from pathlib import Path

import paramiko

ROOT = Path(__file__).resolve().parents[1]
HOST = "192.168.50.28"
USER = "administrator"
REMOTE_APP = "/var/www/e-maintenance"
DOMAIN = "e-maintenance.dpplast.com"
SKIP_DIRS = {
    ".git",
    "node_modules",
    "vendor",
    "tests",
    ".phpunit.cache",
    "storage/logs",
}
SKIP_FILES = {
    ".env",
    ".phpunit.result.cache",
}


def should_skip(path: Path) -> bool:
    rel = path.relative_to(ROOT).as_posix()
    if path.name in SKIP_FILES or rel in SKIP_FILES:
        return True
    parts = path.relative_to(ROOT).parts
    if any(part in {".git", "node_modules", "vendor", "tests", ".phpunit.cache"} for part in parts):
        return True
    if rel.startswith("scripts/_probe"):
        return True
    if rel.startswith("storage/logs/") and path.suffix == ".log":
        return True
    return False


def make_zip(zip_path: Path) -> None:
    with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as zf:
        for path in ROOT.rglob("*"):
            if not path.is_file() or should_skip(path):
                continue
            zf.write(path, path.relative_to(ROOT).as_posix())


def ssh_connect(password: str) -> paramiko.SSHClient:
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(
        hostname=HOST,
        username=USER,
        password=password,
        timeout=30,
        allow_agent=False,
        look_for_keys=False,
    )
    return client


def run(client: paramiko.SSHClient, cmd: str, timeout: int = 120) -> str:
    print(f"\n$ {cmd}")
    stdin, stdout, stderr = client.exec_command(cmd, timeout=timeout)
    out = stdout.read().decode("utf-8", errors="replace")
    err = stderr.read().decode("utf-8", errors="replace")
    code = stdout.channel.recv_exit_status()
    text = (out + err).strip()
    if text:
        print(text[-4000:])
    if code != 0:
        raise RuntimeError(f"Command failed ({code}): {cmd}\n{text}")
    return out


def sudo(client: paramiko.SSHClient, password: str, cmd: str, timeout: int = 180) -> str:
    wrapped = "sudo -S -p '' bash -lc " + shlex.quote(cmd)
    print(f"\n# sudo {cmd}")
    stdin, stdout, stderr = client.exec_command(wrapped, timeout=timeout)
    stdin.write(password + "\n")
    stdin.flush()
    out = stdout.read().decode("utf-8", errors="replace")
    err = stderr.read().decode("utf-8", errors="replace")
    code = stdout.channel.recv_exit_status()
    text = (out + err).strip()
    if text:
        print(text[-4000:])
    if code != 0:
        raise RuntimeError(f"Sudo failed ({code}): {cmd}\n{text}")
    return out


def nginx_conf() -> str:
    return f"""server {{
    listen 80;
    listen [::]:80;
    server_name {DOMAIN};

    root {REMOTE_APP}/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;
    client_max_body_size 32M;

    location / {{
        try_files $uri $uri/ /index.php?$query_string;
    }}

    location = /favicon.ico {{ access_log off; log_not_found off; }}
    location = /robots.txt  {{ access_log off; log_not_found off; }}

    location ~ \\.php$ {{
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }}

    location ~ /\\.(?!well-known).* {{
        deny all;
    }}
}}
"""


def env_contents(app_key: str, db_password: str) -> str:
    return f"""APP_NAME="e-Maintenance"
APP_ENV=production
APP_KEY={app_key}
APP_DEBUG=false
APP_URL=http://{DOMAIN}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_maintenance
DB_USERNAME=e_maintenance
DB_PASSWORD={db_password}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@{DOMAIN}"
MAIL_FROM_NAME="${{APP_NAME}}"

VITE_APP_NAME="${{APP_NAME}}"
"""


def main() -> int:
    password = os.environ.get("DEPLOY_PASS", "")
    if not password:
        print("DEPLOY_PASS missing", file=sys.stderr)
        return 1

    db_password = secrets.token_urlsafe(18)
    zip_path = Path(tempfile.gettempdir()) / "e-maintenance-deploy.zip"
    print(f"Creating archive {zip_path}")
    make_zip(zip_path)
    print(f"Archive size: {zip_path.stat().st_size} bytes")

    client = ssh_connect(password)
    sftp = client.open_sftp()
    print("Uploading archive...")
    sftp.put(str(zip_path), "/tmp/e-maintenance-deploy.zip")

    nginx_tmp = "/tmp/e-maintenance.nginx"
    with sftp.file(nginx_tmp, "w") as fh:
        fh.write(nginx_conf())

    sudo(client, password, "command -v unzip >/dev/null || (apt-get update && apt-get install -y unzip)")
    sudo(client, password, f"mkdir -p {REMOTE_APP}")
    sudo(client, password, f"rm -rf {REMOTE_APP}/* {REMOTE_APP}/.[!.]* 2>/dev/null || true")
    sudo(client, password, f"unzip -o /tmp/e-maintenance-deploy.zip -d {REMOTE_APP}")
    sudo(client, password, f"chown -R {USER}:{USER} {REMOTE_APP}")

    run(
        client,
        f"cd {REMOTE_APP} && composer install --no-dev --optimize-autoloader --no-interaction",
        timeout=300,
    )
    app_key = run(client, "php -r \"echo 'base64:'.base64_encode(random_bytes(32));\"").strip()

    env_tmp = "/tmp/e-maintenance.env"
    with sftp.file(env_tmp, "w") as fh:
        fh.write(env_contents(app_key, db_password))
    sudo(client, password, f"cp {env_tmp} {REMOTE_APP}/.env")

    sudo(
        client,
        password,
        "mysql -e \"CREATE DATABASE IF NOT EXISTS e_maintenance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; "
        f"CREATE USER IF NOT EXISTS 'e_maintenance'@'localhost' IDENTIFIED BY '{db_password}'; "
        f"ALTER USER 'e_maintenance'@'localhost' IDENTIFIED BY '{db_password}'; "
        "GRANT ALL PRIVILEGES ON e_maintenance.* TO 'e_maintenance'@'localhost'; FLUSH PRIVILEGES;\"",
    )

    run(client, f"cd {REMOTE_APP} && php artisan migrate --force", timeout=180)
    run(client, f"cd {REMOTE_APP} && php artisan db:seed --force", timeout=180)
    run(client, f"cd {REMOTE_APP} && php artisan storage:link || true")
    run(client, f"cd {REMOTE_APP} && php artisan config:cache && php artisan route:cache && php artisan view:cache")

    sudo(client, password, f"chown -R www-data:www-data {REMOTE_APP}")
    sudo(client, password, f"chmod -R ug+rwx {REMOTE_APP}/storage {REMOTE_APP}/bootstrap/cache")
    sudo(client, password, f"cp {nginx_tmp} /etc/nginx/sites-available/e-maintenance")
    sudo(
        client,
        password,
        "ln -sfn /etc/nginx/sites-available/e-maintenance /etc/nginx/sites-enabled/e-maintenance",
    )
    sudo(
        client,
        password,
        "grep -q 'e-maintenance.dpplast.com' /etc/hosts || echo '192.168.50.28 e-maintenance.dpplast.com' >> /etc/hosts",
    )
    sudo(client, password, "nginx -t && systemctl reload nginx")
    sudo(client, password, "rm -f /tmp/e-maintenance-deploy.zip /tmp/e-maintenance.nginx /tmp/e-maintenance.env")

    print("\n===== smoke test =====")
    print(run(client, f"curl -sI -H 'Host: {DOMAIN}' http://127.0.0.1/login | head -n 20"))
    print(run(client, f"curl -s -H 'Host: {DOMAIN}' http://127.0.0.1/login | grep -E 'e-Maintenance|NPK|login' | head"))

    sftp.close()
    client.close()
    zip_path.unlink(missing_ok=True)
    print("\nDeploy finished.")
    print(f"URL: http://{DOMAIN}")
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except Exception as exc:
        print(f"DEPLOY ERROR: {exc}", file=sys.stderr)
        raise
