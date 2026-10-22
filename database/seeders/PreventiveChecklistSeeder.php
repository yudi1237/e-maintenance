<?php

namespace Database\Seeders;

use App\Models\PreventiveChecklistItem;
use App\Support\PreventiveCategory;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventivePm1Checklist;
use Illuminate\Database\Seeder;

class PreventiveChecklistSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PreventiveCategory::values() as $category) {
            $this->importCategory($category, PreventiveMachineChecklist::builtinGroups($category));
        }

        $this->importCategory('CONVEYOR', PreventiveConveyorChecklist::builtinGroups());
    }

    /**
     * @param  list<array<string, mixed>>  $groups
     */
    public function importCategory(string $category, array $groups): void
    {
        foreach ($groups as $groupIndex => $group) {
            $itemIndex = 0;
            foreach ($group['items'] as $itemKey => $item) {
                $type = $item['type']
                    ?? (($group['answers'] ?? []) === [] && isset($group['layout'])
                        ? PreventivePm1Checklist::TYPE_MEASUREMENT
                        : PreventivePm1Checklist::TYPE_CONDITION);

                PreventiveChecklistItem::query()->updateOrCreate(
                    [
                        'category' => $category,
                        'item_key' => (string) $itemKey,
                    ],
                    [
                        'group_key' => $group['key'],
                        'group_label' => $group['label'],
                        'item_type' => $type,
                        'layout' => $group['layout'] ?? null,
                        'label' => $item['label'],
                        'standard' => $item['standard'] ?? '',
                        'has_input' => ! empty($item['input']),
                        'input_placeholder' => $item['input']['placeholder'] ?? null,
                        'input_suffix' => $item['input']['suffix'] ?? null,
                        'group_sort_order' => $groupIndex,
                        'sort_order' => $itemIndex,
                        'is_active' => true,
                    ]
                );
                $itemIndex++;
            }
        }
    }
}
