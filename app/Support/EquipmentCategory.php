<?php

namespace App\Support;

class EquipmentCategory
{
    public const ROBOT = 'robot';

    public const DEHUM = 'dehum';

    public const HOPPER = 'hopper';

    public const MTC = 'mtc';

    public const MTC_MOLD = 'mtc_mold';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::ROBOT => 'Robot',
            self::DEHUM => 'Dehum',
            self::HOPPER => 'Hopper',
            self::MTC => 'Mold Temperature Control',
            self::MTC_MOLD => 'Mold',
        ];
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_keys(self::labels());
    }

    public static function label(?string $category): string
    {
        return self::labels()[$category] ?? (string) $category;
    }

    public static function formTitle(string $category): string
    {
        return 'Input Master Data '.self::label($category);
    }

    public static function isRobot(string $category): bool
    {
        return $category === self::ROBOT;
    }

    public static function fromPreventive(?string $category): ?string
    {
        return match ($category) {
            'ROBOT' => self::ROBOT,
            'DEHUM' => self::DEHUM,
            'HOPPER' => self::HOPPER,
            'MTC' => self::MTC,
            default => null,
        };
    }

    public static function usesMasterDropdown(?string $category): bool
    {
        $mapped = self::fromPreventive($category);

        return $mapped !== null && $mapped !== self::ROBOT;
    }
}
