<?php

namespace App\Models;

use App\Support\PreventiveCategory;
use App\Support\PreventiveChecklist;
use App\Support\PreventivePm1Checklist;
use Illuminate\Database\Eloquent\Model;

class PreventiveChecklistItem extends Model
{
    protected $fillable = [
        'category',
        'group_key',
        'group_label',
        'item_type',
        'layout',
        'item_key',
        'label',
        'standard',
        'has_input',
        'input_placeholder',
        'input_suffix',
        'group_sort_order',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'has_input' => 'boolean',
            'is_active' => 'boolean',
            'group_sort_order' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return list<string>
     */
    public static function scheduleTypeValues(): array
    {
        $values = static::query()
            ->where('is_active', true)
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter(fn (string $category): bool => PreventiveCategory::isValid($category))
            ->values()
            ->all();

        return $values !== [] ? $values : PreventiveCategory::values();
    }

    /**
     * @return array<string, string>
     */
    public static function scheduleTypeLabels(): array
    {
        $labels = [];
        foreach (self::scheduleTypeValues() as $value) {
            $labels[$value] = PreventiveCategory::label($value);
        }

        return $labels;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function groupsForCategory(string $category): array
    {
        $rows = static::query()
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('group_sort_order')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $groups = [];

        foreach ($rows as $row) {
            $groupKey = $row->group_key;
            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'key' => $groupKey,
                    'label' => $row->group_label,
                    'answers' => self::answersForType($row->item_type),
                    'has_input' => false,
                    'items' => [],
                ];
                if ($row->layout) {
                    $groups[$groupKey]['layout'] = $row->layout;
                }
            }

            $item = [
                'label' => $row->label,
                'standard' => (string) ($row->standard ?? ''),
                'type' => $row->item_type,
            ];
            if ($row->has_input) {
                $item['input'] = [
                    'placeholder' => (string) ($row->input_placeholder ?? ''),
                    'suffix' => (string) ($row->input_suffix ?? ''),
                ];
                $groups[$groupKey]['has_input'] = true;
            }

            $groups[$groupKey]['items'][$row->item_key] = $item;
        }

        return array_values($groups);
    }

    /** @return array<string, string> */
    public static function answersForType(string $type): array
    {
        if ($type === PreventivePm1Checklist::TYPE_MEASUREMENT) {
            return [];
        }

        if ($type === PreventivePm1Checklist::TYPE_ACTION) {
            return PreventivePm1Checklist::valueLabels(PreventivePm1Checklist::TYPE_ACTION);
        }

        if ($type === PreventivePm1Checklist::TYPE_CONDITION) {
            return PreventivePm1Checklist::valueLabels(PreventivePm1Checklist::TYPE_CONDITION);
        }

        return PreventiveChecklist::valueLabels();
    }
}
