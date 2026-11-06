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

    /** @return array<string, string> */
    public static function typeOptions(): array
    {
        return [
            'assy' => 'ASSY',
            'autoloader' => 'AUTOLOADER',
            'blower' => 'BLOWER',
            'chiller' => 'CHILLER',
            'compatta' => 'COMPATTA',
            'compressor' => 'COMPRESSOR',
            self::DEHUM => 'DEHUM',
            'dryer' => 'DRYER',
            'ekstruder' => 'EKSTRUDER',
            self::HOPPER => 'HOPPER',
            'hot_runner' => 'HOT RUNNER',
            'jig' => 'JIG',
            'mesin_crusher' => 'MESIN CRUSHER',
            'motor' => 'MOTOR',
            self::MTC => 'MTC',
            'pompa' => 'POMPA',
            self::ROBOT => 'ROBOT',
            'squencer' => 'SQUENCER',
        ];
    }

    /** @return list<string> */
    public static function typeValues(): array
    {
        return array_keys(self::typeOptions());
    }

    public static function typeLabel(?string $type): string
    {
        return self::typeOptions()[$type] ?? self::label($type);
    }

    /** @return array<string, string> */
    public static function areaOptions(): array
    {
        return [
            'assembling' => 'ASSEMBLING',
            'compounding' => 'COMPOUNDING',
            'double_seat' => 'DOUBLE SEAT',
            'injection_line' => 'INJECTION LINE',
            'painting_line_1' => 'PAINTING LINE 1',
            'painting_line_2' => 'PAINTING LINE 2',
            'painting_line_3' => 'PAINTING LINE 3',
            'utility_room' => 'UTILITY ROOM',
            'lain_lain' => 'LAIN-LAIN',
        ];
    }

    /** @return list<string> */
    public static function areaValues(): array
    {
        return array_keys(self::areaOptions());
    }

    public static function areaLabel(?string $area): string
    {
        return self::areaOptions()[$area]
            ?? ['cooling_tower' => 'Cooling Tower'][$area]
            ?? (string) $area;
    }
}
