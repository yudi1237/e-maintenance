<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_QUALITY = 'quality';

    public const ROLE_PRODUKSI_INJECTION = 'produksi_injection';

    public const ROLE_MANAGEMENT = 'management';

    public const ROLE_PPIC = 'ppic';

    public const ROLE_TECHNICIAN = 'technician';

    public const ROLE_OPERATOR = 'operator';

    protected $fillable = [
        'name',
        'npk',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return filled($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    public function resetTwoFactorAuthentication(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /** @return array<string, string> */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_TECHNICIAN => 'Teknisi Maintenance (Login /home)',
            self::ROLE_OPERATOR => 'Operator',
            self::ROLE_PRODUKSI_INJECTION => 'Produksi Injection',
            self::ROLE_QUALITY => 'Quality',
            self::ROLE_MANAGEMENT => 'Management View Dashboard',
            self::ROLE_PPIC => 'PPIC',
            self::ROLE_ADMIN => 'Admin Maintenance',
        ];
    }

    /** @return list<string> */
    public static function adminAccessRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_QUALITY,
            self::ROLE_PRODUKSI_INJECTION,
            self::ROLE_MANAGEMENT,
            self::ROLE_PPIC,
        ];
    }

    /** @return list<string> */
    public static function roleValues(): array
    {
        return array_keys(self::roleOptions());
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function preventiveReports(): HasMany
    {
        return $this->hasMany(PreventiveReport::class);
    }

    public function findingRecaps(): HasMany
    {
        return $this->hasMany(FindingRecap::class);
    }

    public function preventiveSchedules(): HasMany
    {
        return $this->hasMany(PreventiveSchedule::class);
    }

    public function preventiveConveyorReports(): HasMany
    {
        return $this->hasMany(PreventiveConveyorReport::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isQuality(): bool
    {
        return $this->role === self::ROLE_QUALITY;
    }

    public function isProduksiInjection(): bool
    {
        return $this->role === self::ROLE_PRODUKSI_INJECTION;
    }

    public function isTechnician(): bool
    {
        return $this->role === self::ROLE_TECHNICIAN;
    }

    public function isManagement(): bool
    {
        return $this->role === self::ROLE_MANAGEMENT;
    }

    public function isPpic(): bool
    {
        return $this->role === self::ROLE_PPIC;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function homeRouteName(): string
    {
        return match (true) {
            $this->isOperator() => 'operator.home',
            $this->canAccessAdmin() => 'admin.dashboard',
            default => 'home',
        };
    }

    public function seesMachineMap(): bool
    {
        return $this->seesDashboardOverview() || $this->isPpic() || $this->isOperator();
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this->role, self::adminAccessRoles(), true);
    }

    public function seesFullDashboard(): bool
    {
        return $this->isAdmin();
    }

    public function seesAdminDashboard(): bool
    {
        return $this->isAdmin() || $this->isProduksiInjection();
    }

    public function seesTechnicianCharts(): bool
    {
        return $this->isAdmin();
    }

    public function canToggleMachineRunning(): bool
    {
        return $this->isProduksiInjection();
    }

    public function seesMachineApprovalColor(): bool
    {
        return $this->isAdmin() || $this->isProduksiInjection() || $this->isQuality();
    }

    public function seesDashboardOverview(): bool
    {
        return $this->isAdmin() || $this->isProduksiInjection() || $this->isManagement() || $this->isPpic();
    }

    public function seesPreventiveResults(): bool
    {
        return $this->isAdmin() || $this->isProduksiInjection() || $this->isQuality() || $this->isManagement() || $this->isPpic();
    }

    public function canOpenPreventiveBoard(): bool
    {
        return $this->isAdmin() || $this->isQuality();
    }

    public function roleLabel(): string
    {
        return self::roleOptions()[$this->role] ?? $this->role;
    }

    public function roleBadgeClass(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-200',
            self::ROLE_QUALITY => 'bg-violet-100 text-violet-800 dark:bg-violet-500/20 dark:text-violet-200',
            self::ROLE_PRODUKSI_INJECTION => 'bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-200',
            self::ROLE_MANAGEMENT => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
            self::ROLE_PPIC => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200',
            self::ROLE_OPERATOR => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
        };
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->npk;
    }

    public function getEmailForVerification(): string
    {
        return $this->npk;
    }
}
