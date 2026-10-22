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

    public const ROLE_TECHNICIAN = 'technician';

    protected $fillable = [
        'name',
        'npk',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** @return array<string, string> */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_TECHNICIAN => 'Teknisi Maintenance (Login /home)',
            self::ROLE_PRODUKSI_INJECTION => 'Produksi Injection',
            self::ROLE_QUALITY => 'Quality',
            self::ROLE_MANAGEMENT => 'Management View Dashboard',
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

    public function canAccessAdmin(): bool
    {
        return in_array($this->role, self::adminAccessRoles(), true);
    }

    public function seesFullDashboard(): bool
    {
        return $this->isAdmin();
    }

    public function seesDashboardOverview(): bool
    {
        return $this->isAdmin() || $this->isManagement();
    }

    public function seesPreventiveResults(): bool
    {
        return $this->isAdmin() || $this->isProduksiInjection() || $this->isManagement();
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
