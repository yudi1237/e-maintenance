<?php

namespace App\Support;

class FindingRecapStatus
{
    public const OPEN = 'open';

    public const ON_PROGRESS = 'on_progress';

    public const MONITORING = 'monitoring';

    public const CLOSE = 'close';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::OPEN => 'Open',
            self::ON_PROGRESS => 'On Progress',
            self::MONITORING => 'Monitoring',
            self::CLOSE => 'Close',
        ];
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_keys(self::labels());
    }

    public static function label(?string $status): string
    {
        return self::labels()[$status] ?? (string) $status;
    }

    public static function badgeClass(?string $status): string
    {
        return match ($status) {
            self::CLOSE => 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300 dark:border-green-500/40',
            self::MONITORING => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300 dark:border-sky-500/40',
            self::ON_PROGRESS => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-300 dark:border-yellow-500/40',
            self::OPEN => 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300 dark:border-red-500/40',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
        };
    }

    public static function legendColor(?string $status): string
    {
        return match ($status) {
            self::CLOSE => '#22c55e',
            self::MONITORING => '#0ea5e9',
            self::ON_PROGRESS => '#eab308',
            self::OPEN => '#ef4444',
            default => '#64748b',
        };
    }
}
