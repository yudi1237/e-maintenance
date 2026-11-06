<?php

namespace App\Support;

use Carbon\Carbon;

class WorkShift
{
    public const SHIFT_1 = 'shift_1';

    public const SHIFT_2 = 'shift_2';

    public const SHIFT_3 = 'shift_3';

    public static function labels(): array
    {
        return [
            self::SHIFT_1 => 'Shift 1 (00:00 - 08:00)',
            self::SHIFT_2 => 'Shift 2 (08:00 - 16:00)',
            self::SHIFT_3 => 'Shift 3 (16:00 - 24:00)',
        ];
    }

    public static function detect(?Carbon $time = null): string
    {
        $time = ($time ?? now())->timezone('Asia/Jakarta');
        $hour = (int) $time->format('H');

        if ($hour >= 0 && $hour < 8) {
            return self::SHIFT_1;
        }

        if ($hour >= 8 && $hour < 16) {
            return self::SHIFT_2;
        }

        return self::SHIFT_3;
    }

    public static function currentNumber(?Carbon $time = null): int
    {
        return match (self::detect($time)) {
            self::SHIFT_1 => 1,
            self::SHIFT_2 => 2,
            default => 3,
        };
    }

    public static function nextNumber(int $shift): int
    {
        return $shift === 3 ? 1 : $shift + 1;
    }

    public static function previousNumber(int $shift): int
    {
        return $shift === 1 ? 3 : $shift - 1;
    }
}
