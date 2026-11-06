<?php

namespace App\Support;

use App\Models\PreventiveCategoryRecord;

class PreventiveCategory
{
    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return ['PM1', 'PM2', 'PM3', 'PM4', 'P1E', 'MTC', 'HOPPER', 'DEHUM', 'ROBOT', 'HOT', 'JSWEL'];
    }

    /**
     * @return list<string>
     */
    public static function machineValues(): array
    {
        return ['PM1', 'PM2', 'PM3', 'PM4', 'P1E', 'JSWEL'];
    }

    /**
     * @return list<string>
     */
    public static function injectionValues(): array
    {
        return ['JSWEL', 'P1E', 'PM1', 'PM2', 'PM3', 'PM4'];
    }

    /**
     * @return list<string>
     */
    public static function utilityValues(): array
    {
        return ['MTC', 'HOPPER', 'DEHUM', 'ROBOT', 'HOT'];
    }

    /**
     * @return array<string, string>
     */
    public static function scheduleGroupLabels(): array
    {
        return [
            'injection' => 'Injection',
            'MTC' => self::label('MTC'),
            'HOPPER' => self::label('HOPPER'),
            'DEHUM' => self::label('DEHUM'),
            'ROBOT' => self::label('ROBOT'),
            'HOT' => self::label('HOT'),
        ];
    }

    public static function isInjectionType(?string $type): bool
    {
        return in_array($type, self::injectionValues(), true);
    }

    public static function scheduleGroupKey(?string $type): ?string
    {
        if (self::isInjectionType($type)) {
            return 'injection';
        }

        return in_array($type, self::utilityValues(), true) ? $type : null;
    }

    public static function scheduleGroupLabel(?string $type): string
    {
        $group = self::scheduleGroupKey($type);

        if ($group === null) {
            return '—';
        }

        return self::scheduleGroupLabels()[$group] ?? '—';
    }

    /**
     * @return array<string, string>
     */
    public static function typesForScheduleGroup(?string $group): array
    {
        $wanted = $group === 'injection'
            ? self::injectionValues()
            : (in_array($group, self::utilityValues(), true) ? [$group] : []);

        $labels = [];
        foreach ($wanted as $value) {
            $labels[$value] = self::label($value);
        }

        return $labels;
    }

    public static function hubTitle(): string
    {
        return 'Preventive Mesin, Conveyor, Robot & Utility';
    }

    /**
     * @return list<string>
     */
    public static function picDestinations(): array
    {
        return array_merge(['menu', 'machines', 'conveyor'], self::utilityValues());
    }

    public static function isUtility(?string $category): bool
    {
        return in_array($category, self::utilityValues(), true);
    }

    /**
     * @return array<string, string>
     */
    public static function machineLabels(): array
    {
        return array_intersect_key(self::labels(), array_flip(self::machineValues()));
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::values() as $value) {
            $labels[$value] = $value;
        }

        $labels['JSWEL'] = 'JSW EL';
        $labels['MTC'] = 'Mold Temperature Control';
        $labels['HOPPER'] = 'Hopper';
        $labels['DEHUM'] = 'Dehum';
        $labels['ROBOT'] = 'Robot';
        $labels['HOT'] = 'Hot Runner';

        return $labels;
    }

    public static function formTitle(?string $category): string
    {
        $label = self::label($category);
        $default = self::labels()[$category] ?? null;
        if ($default !== null && $label !== $default) {
            return str_starts_with($label, 'Preventive ') ? $label : 'Preventive '.$label;
        }

        return match ($category) {
            'MTC' => 'Preventive Mold Temperature Control',
            'HOPPER' => 'Preventive Hopper',
            'DEHUM' => 'Preventive Dehum',
            'ROBOT' => 'Preventive Robot',
            'HOT' => 'Preventive Hot Runner',
            'P1E' => 'Preventive P1E',
            'JSWEL' => 'Preventive JSW EL',
            'PM1', 'PM2', 'PM3', 'PM4' => 'Preventive Maintenance '.substr((string) $category, 2).' ('.$category.')',
            default => 'Preventive '.(string) $category,
        };
    }

    public static function documentNumber(?string $category): ?string
    {
        return match ($category) {
            'PM1', 'PM2', 'PM3', 'PM4', 'P1E', 'JSWEL' => 'FM.002-DPP-MTC-01',
            'MTC' => 'FM.013-DPP-MTC-01',
            'HOPPER' => 'FM.014-DPP-MTC-01',
            'DEHUM' => 'FM.015-DPP-MTC-01',
            'ROBOT' => 'FM.016-DPP-MTC-01',
            'HOT' => 'FM.017-DPP-MTC-01',
            'CONVEYOR' => 'FM.018-DPP-MTC-01',
            default => null,
        };
    }

    public static function label(?string $category): string
    {
        $stored = PreventiveCategoryRecord::storedLabel($category);

        if ($stored !== null && $stored !== '') {
            return $stored;
        }

        return self::labels()[$category] ?? (string) $category;
    }

    public static function numberLabel(?string $category): string
    {
        return 'Nomor '.self::label($category);
    }

    public static function isValid(?string $category): bool
    {
        return in_array($category, self::values(), true);
    }

    public static function routePattern(): string
    {
        return implode('|', self::values());
    }
}
