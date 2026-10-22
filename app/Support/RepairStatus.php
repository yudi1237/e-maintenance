<?php

namespace App\Support;

use App\Models\User;

class RepairStatus
{
    public const OPEN = 'open';

    public const ON_PROGRESS = 'on_progress';

    public const CLOSE_BY_TECHNICIAN = 'close_by_technician';

    public const APPROVE_PRODUKSI_INJECTION = 'approve_produksi_injection';

    public const APPROVE_QUALITY = 'approve_quality';

    public const CLOSE = 'close';

    public static function labels(): array
    {
        return [
            self::OPEN => 'Open',
            self::ON_PROGRESS => 'On Progress',
            self::CLOSE_BY_TECHNICIAN => 'Close by teknisi',
            self::APPROVE_PRODUKSI_INJECTION => 'Approve Produksi Injection',
            self::APPROVE_QUALITY => 'Approve Quality',
            self::CLOSE => 'Maintenance',
        ];
    }

    public static function technicianLabels(): array
    {
        return [
            self::OPEN => 'Open',
            self::ON_PROGRESS => 'On Progress',
            self::CLOSE_BY_TECHNICIAN => 'Close by teknisi',
        ];
    }

    public static function badgeClass(string $status): string
    {
        return match ($status) {
            self::CLOSE => 'bg-green-100 text-green-800',
            self::APPROVE_QUALITY => 'bg-violet-100 text-violet-800',
            self::APPROVE_PRODUKSI_INJECTION => 'bg-orange-100 text-orange-800',
            self::CLOSE_BY_TECHNICIAN => 'bg-cyan-100 text-cyan-800',
            self::ON_PROGRESS => 'bg-yellow-100 text-yellow-800',
            self::OPEN => 'bg-red-100 text-red-800',
            default => 'bg-red-100 text-red-800',
        };
    }

    public static function badgeClassDark(string $status): string
    {
        return match ($status) {
            self::CLOSE => 'bg-green-500/20 text-green-300 border-green-500/40',
            self::APPROVE_QUALITY => 'bg-violet-500/20 text-violet-300 border-violet-500/40',
            self::APPROVE_PRODUKSI_INJECTION => 'bg-orange-500/20 text-orange-300 border-orange-500/40',
            self::CLOSE_BY_TECHNICIAN => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40',
            self::ON_PROGRESS => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/40',
            self::OPEN => 'bg-red-500/20 text-red-300 border-red-500/40',
            default => 'bg-red-500/20 text-red-300 border-red-500/40',
        };
    }

    public static function legendColor(string $status): string
    {
        return match ($status) {
            self::CLOSE => '#22c55e',
            self::APPROVE_QUALITY => '#a855f7',
            self::APPROVE_PRODUKSI_INJECTION => '#f97316',
            self::CLOSE_BY_TECHNICIAN => '#22d3ee',
            self::ON_PROGRESS => '#facc15',
            self::OPEN => '#ef4444',
            default => '#ef4444',
        };
    }

    /** @return array<string, string> */
    public static function legendColors(): array
    {
        $colors = [];
        foreach (self::values() as $status) {
            $colors[$status] = self::legendColor($status);
        }

        return $colors;
    }

    public static function selectClassDark(string $status, bool $compact = false): string
    {
        $size = $compact ? '!py-1.5 !text-xs min-w-[12.5rem]' : 'min-w-[14rem]';

        return 'em-input !mt-0 border font-semibold '.$size.' '.self::badgeClassDark($status);
    }

    /** @return list<string> */
    public static function values(): array
    {
        return [
            self::OPEN,
            self::ON_PROGRESS,
            self::CLOSE_BY_TECHNICIAN,
            self::APPROVE_PRODUKSI_INJECTION,
            self::APPROVE_QUALITY,
            self::CLOSE,
        ];
    }

    public static function boardOrderSql(): string
    {
        $sql = 'CASE status';
        foreach (self::values() as $index => $status) {
            $sql .= " WHEN '".$status."' THEN ".($index + 1);
        }

        return $sql.' ELSE 9 END';
    }

    /** @return list<string> */
    public static function technicianValues(): array
    {
        return [self::OPEN, self::ON_PROGRESS, self::CLOSE_BY_TECHNICIAN];
    }

    /** @return list<string> */
    public static function activeValues(): array
    {
        return [
            self::OPEN,
            self::ON_PROGRESS,
            self::CLOSE_BY_TECHNICIAN,
            self::APPROVE_PRODUKSI_INJECTION,
            self::APPROVE_QUALITY,
        ];
    }

    /** @return list<string> */
    public static function machineStopValues(): array
    {
        return [self::OPEN, self::ON_PROGRESS];
    }

    /** @return list<string> */
    public static function pendingMaintenanceCloseValues(): array
    {
        return [
            self::CLOSE_BY_TECHNICIAN,
            self::APPROVE_PRODUKSI_INJECTION,
            self::APPROVE_QUALITY,
        ];
    }

    public static function machineCardAlert(int $activeOpCount, int $pendingCloseOpCount, int $openFindingCount): string
    {
        if ($activeOpCount > 0) {
            return 'active';
        }

        if ($pendingCloseOpCount > 0 || $openFindingCount > 0) {
            return 'pending';
        }

        return 'idle';
    }

    /** @return list<string> */
    public static function technicianWorkDoneValues(): array
    {
        return [
            self::CLOSE_BY_TECHNICIAN,
            self::APPROVE_PRODUKSI_INJECTION,
            self::APPROVE_QUALITY,
            self::CLOSE,
        ];
    }

    /** @return list<string> */
    public static function closedLikeValues(): array
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
            self::OPEN => self::ON_PROGRESS,
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

        if ($user === null || ! $user->canAccessAdmin()) {
            return false;
        }

        if (self::nextStatus($current) !== $next) {
            return false;
        }

        return match ($current) {
            self::OPEN => true,
            self::CLOSE_BY_TECHNICIAN => $user->isProduksiInjection(),
            self::APPROVE_PRODUKSI_INJECTION => $user->isQuality(),
            self::APPROVE_QUALITY => $user->isAdmin(),
            default => false,
        };
    }

    public static function transitionDeniedMessage(string $current, string $next): string
    {
        if ($current === self::CLOSE_BY_TECHNICIAN && $next !== self::APPROVE_PRODUKSI_INJECTION) {
            return 'Setelah Close by teknisi, tiket harus di-Approve Produksi Injection terlebih dahulu.';
        }

        if ($current === self::APPROVE_PRODUKSI_INJECTION && $next !== self::APPROVE_QUALITY) {
            return 'Setelah Approve Produksi Injection, tiket harus di-Approve Quality terlebih dahulu.';
        }

        if ($current === self::APPROVE_QUALITY && $next !== self::CLOSE) {
            return 'Setelah Approve Quality, tiket harus di-Approve Admin Maintenance.';
        }

        return match ($current) {
            self::CLOSE_BY_TECHNICIAN => 'Hanya peran Produksi Injection yang dapat Approve Produksi Injection.',
            self::APPROVE_PRODUKSI_INJECTION => 'Hanya peran Quality yang dapat Approve Quality.',
            self::APPROVE_QUALITY => 'Hanya Admin Maintenance yang dapat Approve Admin Maintenance.',
            default => 'Status tidak dapat diubah ke langkah tersebut.',
        };
    }

    public static function adminLabels(?string $current = null, ?User $user = null): array
    {
        $current = $current ?? self::OPEN;
        $user = $user ?? auth()->user();
        $labels = self::labels();
        $options = [
            $current => $labels[$current] ?? $current,
        ];

        $next = self::nextStatus($current);
        if ($next !== null && self::canTransition($user, $current, $next)) {
            $options[$next] = $labels[$next];
        }

        return $options;
    }

    public static function labelsWithoutClose(): array
    {
        return self::technicianLabels();
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

    public static function pendingApprovalStatusFor(?User $user): ?string
    {
        return match (true) {
            $user?->isProduksiInjection() => self::CLOSE_BY_TECHNICIAN,
            $user?->isQuality() => self::APPROVE_PRODUKSI_INJECTION,
            $user?->isAdmin() => self::APPROVE_QUALITY,
            default => null,
        };
    }

    public static function approvalQueueStatusFor(?User $user): ?string
    {
        $statuses = self::approvalQueueStatusesFor($user);

        return $statuses[0] ?? null;
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

    public static function dashboardTicketOrderSql(?User $user): ?string
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

    /** @return list<string> */
    public static function approvedStatusesFor(?User $user): array
    {
        return match (true) {
            $user?->isProduksiInjection() => [
                self::APPROVE_PRODUKSI_INJECTION,
                self::APPROVE_QUALITY,
                self::CLOSE,
            ],
            $user?->isQuality() => [
                self::APPROVE_QUALITY,
                self::CLOSE,
            ],
            default => [self::CLOSE],
        };
    }

    public static function approvalRoleCaption(?User $user): string
    {
        return match (true) {
            $user?->isProduksiInjection() => 'Produksi Injection',
            $user?->isQuality() => 'Quality',
            $user?->isManagement() => 'Semua proses',
            default => 'Admin Maintenance',
        };
    }
}
