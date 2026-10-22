<?php

namespace App\Support;

class InjectionMachine
{
    public static function count(): int
    {
        return config('maintenance.machine_count', 60);
    }

    public static function numbers(): array
    {
        return range(1, self::count());
    }

    public static function isValid(int $number): bool
    {
        return $number >= 1 && $number <= self::count();
    }
}
