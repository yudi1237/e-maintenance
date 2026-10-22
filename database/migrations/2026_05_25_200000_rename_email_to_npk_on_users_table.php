<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'email')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE users RENAME COLUMN email TO npk');
        } else {
            DB::statement('ALTER TABLE users CHANGE email npk VARCHAR(255) NOT NULL');
        }

        $this->migrateLegacyNpkValues();
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'npk')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE users RENAME COLUMN npk TO email');
        } else {
            DB::statement('ALTER TABLE users CHANGE npk email VARCHAR(255) NOT NULL');
        }
    }

    private function migrateLegacyNpkValues(): void
    {
        $map = [
            'admin@dharma-poliplast.com' => '100001',
            'teknisi@dharma-poliplast.com' => '200001',
        ];

        foreach ($map as $legacy => $npk) {
            DB::table('users')->where('npk', $legacy)->update(['npk' => $npk]);
        }
    }
};
