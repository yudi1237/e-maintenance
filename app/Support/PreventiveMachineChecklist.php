<?php

namespace App\Support;

use App\Models\PreventiveChecklistItem;

class PreventiveMachineChecklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type?: string}>}>
     */
    public static function groups(?string $category): array
    {
        if (is_string($category) && $category !== '') {
            $fromDb = PreventiveChecklistItem::groupsForCategory($category);
            if ($fromDb !== []) {
                return $fromDb;
            }
        }

        return self::builtinGroups($category);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function builtinGroups(?string $category): array
    {
        $sheet = self::sheetClass($category);

        return $sheet ? $sheet::groups() : self::genericGroups();
    }

    /**
     * @param  array<string, string>  $storedItems
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type?: string}>}>
     */
    public static function groupsForReport(?string $category, array $storedItems): array
    {
        $groups = self::groups($category);
        $expectedKeys = array_keys(self::items($category));
        $overlap = array_intersect($expectedKeys, array_keys($storedItems));

        if ($expectedKeys === [] || $overlap !== []) {
            return $groups;
        }

        return self::genericGroupsFromStored($storedItems);
    }

    /**
     * @return array<string, string>
     */
    public static function items(?string $category): array
    {
        $items = [];

        foreach (self::groups($category) as $group) {
            if (($group['answers'] ?? []) === []) {
                continue;
            }

            foreach ($group['items'] as $key => $item) {
                $items[$key] = $item['label'];
            }
        }

        return $items !== [] ? $items : PreventiveChecklist::items();
    }

    /**
     * @return list<string>
     */
    public static function allItemKeys(?string $category): array
    {
        $keys = [];

        foreach (self::groups($category) as $group) {
            foreach (array_keys($group['items'] ?? []) as $key) {
                $keys[] = (string) $key;
            }
        }

        return $keys;
    }

    /**
     * @return array<string, string>
     */
    public static function filledOk(?string $category): array
    {
        return array_fill_keys(array_keys(self::items($category)), PreventiveChecklist::OK);
    }

    /**
     * @return list<string>
     */
    public static function readingKeys(?string $category): array
    {
        $keys = [];

        foreach (self::groups($category) as $group) {
            foreach ($group['items'] as $key => $item) {
                if (! empty($item['input'])) {
                    $keys[] = $key;
                }
            }
        }

        return $keys;
    }

    /**
     * @return array<string, string>
     */
    public static function filledReadings(?string $category): array
    {
        $readings = [];

        foreach (self::groups($category) as $group) {
            foreach ($group['items'] as $key => $item) {
                if (empty($item['input'])) {
                    continue;
                }

                $readings[$key] = $item['input']['placeholder'] ?? '1';
            }
        }

        return $readings;
    }

    /**
     * @return list<string>
     */
    public static function allowedValues(?string $category): array
    {
        if (self::sheetClass($category) !== null) {
            return [PreventiveChecklist::OK, PreventiveChecklist::NOT_OK, PreventiveChecklist::NA];
        }

        return array_keys(PreventiveChecklist::valueLabels());
    }

    public static function itemLabel(?string $category, string $key): string
    {
        return self::items($category)[$key]
            ?? PreventiveChecklist::items()[$key]
            ?? $key;
    }

    public static function itemValueLabel(?string $category, string $key, ?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        foreach (self::groups($category) as $group) {
            if (! isset($group['items'][$key])) {
                continue;
            }

            return $group['answers'][$value]
                ?? PreventiveChecklist::valueLabels()[$value]
                ?? '-';
        }

        return PreventiveChecklist::valueLabels()[$value] ?? '-';
    }

    /**
     * @param  array<string, string>  $submitted
     */
    public static function result(?string $category, array $submitted): string
    {
        foreach (array_keys(self::items($category)) as $key) {
            if (($submitted[$key] ?? null) === PreventiveChecklist::NOT_OK) {
                return 'follow_up';
            }
        }

        return 'ok';
    }

    /**
     * @return class-string<PreventivePm1Checklist>|null
     */
    private static function sheetClass(?string $category): ?string
    {
        return match ($category) {
            'PM1' => PreventivePm1Checklist::class,
            'PM2' => PreventivePm2Checklist::class,
            'PM3' => PreventivePm3Checklist::class,
            'PM4' => PreventivePm4Checklist::class,
            'P1E' => PreventiveP1eChecklist::class,
            'MTC' => PreventiveMtcChecklist::class,
            'HOPPER' => PreventiveHopperChecklist::class,
            'DEHUM' => PreventiveDehumChecklist::class,
            'ROBOT' => PreventiveRobotChecklist::class,
            'HOT' => PreventiveHotChecklist::class,
            'JSWEL' => PreventiveJswElChecklist::class,
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $readings
     * @return array<string, string>
     */
    public static function sanitizeReadings(?string $category, array $readings): array
    {
        $clean = [];

        foreach (self::readingKeys($category) as $key) {
            $value = trim((string) ($readings[$key] ?? ''));
            if ($value === '') {
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }

    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string}>}>
     */
    private static function genericGroups(): array
    {
        $items = [];

        foreach (PreventiveChecklist::items() as $key => $label) {
            $items[$key] = [
                'label' => $label,
                'standard' => '',
            ];
        }

        return [[
            'key' => 'general',
            'label' => 'Checklist Preventive',
            'answers' => PreventiveChecklist::valueLabels(),
            'has_input' => false,
            'items' => $items,
        ]];
    }

    /**
     * @param  array<string, string>  $storedItems
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string}>}>
     */
    private static function genericGroupsFromStored(array $storedItems): array
    {
        $items = [];

        foreach ($storedItems as $key => $value) {
            $items[$key] = [
                'label' => PreventiveChecklist::items()[$key] ?? $key,
                'standard' => '',
            ];
        }

        return [[
            'key' => 'stored',
            'label' => 'Checklist Preventive',
            'answers' => PreventiveChecklist::valueLabels(),
            'has_input' => false,
            'items' => $items,
        ]];
    }
}
