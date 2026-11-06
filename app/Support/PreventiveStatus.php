<?php

namespace App\Support;

use App\Models\User;

class PreventiveStatus
{
    public const CLOSE_BY_TECHNICIAN = 'close_by_technician';

    public const APPROVE_PRODUKSI_INJECTION = 'approve_produksi_injection';

    public const APPROVE_QUALITY = 'approve_quality';

    public const CLOSE = 'close';

    public static function labels(): array
    {
        return [
            self::CLOSE_BY_TECHNICIAN => 'Close by teknisi',
            self::APPROVE_PRODUKSI_INJECTION => 'Approve Produksi Injection',
            self::APPROVE_QUALITY => 'Approve Quality',
            self::CLOSE => 'Maintenance',
        ];
    }

    public static function badgeClassDark(string $status): string
    {
        return match ($status) {
            self::CLOSE => 'bg-green-500/20 text-green-300 border-green-500/40',
            self::APPROVE_QUALITY => 'bg-violet-500/20 text-violet-300 border-violet-500/40',
            self::APPROVE_PRODUKSI_INJECTION => 'bg-orange-500/20 text-orange-300 border-orange-500/40',
            self::CLOSE_BY_TECHNICIAN => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40',
            default => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40',
        };
    }

    public static function legendColor(string $status): string
    {
        return match ($status) {
            self::CLOSE => '#22c55e',
            self::APPROVE_QUALITY => '#a855f7',
            self::APPROVE_PRODUKSI_INJECTION => '#f97316',
            self::CLOSE_BY_TECHNICIAN => '#22d3ee',
            default => '#22d3ee',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return [
            self::CLOSE_BY_TECHNICIAN,
            self::APPROVE_PRODUKSI_INJECTION,
            self::APPROVE_QUALITY,
            self::CLOSE,
        ];
    }

    public static function nextStatus(?string $current): ?string
    {
        return match ($current) {
            self::CLOSE_BY_TECHNICIAN => self::APPROVE_PRODUKSI_INJECTION,
            self::APPROVE_PRODUKSI_INJECTION => self::APPROVE_QUALITY,
            self::APPROVE_QUALITY => self::CLOSE,
            default => null,
        };
    }

    public static function canTransition(?User $user, string $current, string $next): bool
    {
        if ($current === $next) {
            return true;
        }

        if ($user === null || ! $user->seesPreventiveResults()) {
            return false;
        }

        if (self::nextStatus($current) !== $next) {
            return false;
        }

        return match ($current) {
            self::CLOSE_BY_TECHNICIAN => $user->isProduksiInjection(),
            self::APPROVE_PRODUKSI_INJECTION => $user->isQuality(),
            self::APPROVE_QUALITY => $user->isAdmin(),
            default => false,
        };
    }

    public static function transitionDeniedMessage(string $current, string $next): string
    {
        if ($current === self::CLOSE_BY_TECHNICIAN && $next !== self::APPROVE_PRODUKSI_INJECTION) {
            return 'Setelah teknisi menyelesaikan preventive, hasil harus di-Approve Produksi Injection terlebih dahulu.';
        }

        if ($current === self::APPROVE_PRODUKSI_INJECTION && $next !== self::APPROVE_QUALITY) {
            return 'Setelah Approve Produksi Injection, hasil harus di-Approve Quality terlebih dahulu.';
        }

        if ($current === self::APPROVE_QUALITY && $next !== self::CLOSE) {
            return 'Setelah Approve Quality, hasil harus di-Approve Admin Maintenance.';
        }

        return match ($current) {
            self::CLOSE_BY_TECHNICIAN => 'Hanya peran Produksi Injection yang dapat Approve Produksi Injection.',
            self::APPROVE_PRODUKSI_INJECTION => 'Hanya peran Quality yang dapat Approve Quality.',
            self::APPROVE_QUALITY => 'Hanya Admin Maintenance yang dapat Approve Admin Maintenance.',
            default => 'Status tidak dapat diubah ke langkah tersebut.',
        };
    }

    /**
     * @return list<array{key: string, label: string, icon: string, done: bool, current: bool, can_approve: bool, owned_by_user: bool}>
     */
    public static function approvalProgress(?string $status, ?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $doneLevel = match ($status) {
            self::APPROVE_PRODUKSI_INJECTION => 1,
            self::APPROVE_QUALITY => 2,
            self::CLOSE => 3,
            default => 0,
        };
        $isAwaitingApproval = in_array($status, [
            self::CLOSE_BY_TECHNICIAN,
            self::APPROVE_PRODUKSI_INJECTION,
            self::APPROVE_QUALITY,
        ], true);

        $steps = [
            ['key' => self::APPROVE_PRODUKSI_INJECTION, 'label' => 'Injection', 'icon' => 'injection', 'level' => 1],
            ['key' => self::APPROVE_QUALITY, 'label' => 'QC', 'icon' => 'qc', 'level' => 2],
            ['key' => self::CLOSE, 'label' => 'Maintenance', 'icon' => 'admin', 'level' => 3],
        ];

        return array_map(static function (array $step) use ($doneLevel, $isAwaitingApproval, $status, $user): array {
            $current = $isAwaitingApproval && ($doneLevel + 1) === $step['level'];
            $ownedByUser = match ($step['key']) {
                self::APPROVE_PRODUKSI_INJECTION => (bool) $user?->isProduksiInjection(),
                self::APPROVE_QUALITY => (bool) $user?->isQuality(),
                self::CLOSE => (bool) $user?->isAdmin(),
                default => false,
            };

            return [
                'key' => $step['key'],
                'label' => $step['label'],
                'icon' => $step['icon'],
                'done' => $doneLevel >= $step['level'],
                'current' => $current,
                'owned_by_user' => $ownedByUser,
                'can_approve' => $current && $user !== null && self::canTransition($user, (string) $status, $step['key']),
            ];
        }, $steps);
    }

    public static function approvalActorColumn(string $status): ?string
    {
        return match ($status) {
            self::APPROVE_PRODUKSI_INJECTION => 'injection_approved_by',
            self::APPROVE_QUALITY => 'quality_approved_by',
            self::CLOSE => 'maintenance_approved_by',
            default => null,
        };
    }

    /** @return list<string> */
    public static function approvalQueueStatusesFor(?User $user): array
    {
        return match (true) {
            $user?->isProduksiInjection() => [self::CLOSE_BY_TECHNICIAN],
            $user?->isQuality() => [self::APPROVE_PRODUKSI_INJECTION],
            $user?->isAdmin() => [self::APPROVE_QUALITY],
            default => [],
        };
    }

    public static function dashboardOrderSql(?User $user): ?string
    {
        $pending = self::approvalQueueStatusesFor($user);

        if ($pending === []) {
            return null;
        }

        $sql = 'CASE';
        foreach ($pending as $index => $status) {
            $sql .= " WHEN status = '".$status."' THEN ".$index;
        }

        return $sql.' ELSE '.count($pending).' END';
    }
}
