<?php

namespace App\Http\Requests;

use App\Models\PreventiveChecklistItem;
use App\Rules\SafePlainText;
use App\Support\PreventiveCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePreventiveScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->seesFullDashboard() === true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            return;
        }

        $rows = collect($this->input('rows', []))
            ->filter(function ($row): bool {
                if (! is_array($row)) {
                    return false;
                }

                return filled($row['machine_name_id'] ?? null)
                    || filled($row['plan_date'] ?? null)
                    || filled($row['preventive_type'] ?? null)
                    || filled($row['notes'] ?? null);
            })
            ->values()
            ->all();

        $this->merge(['rows' => $rows]);
    }

    public function rules(): array
    {
        $machineRule = [
            'required',
            'integer',
            Rule::exists('machine_names', 'id')->where(fn ($query) => $query
                ->where('is_active', true)
                ->whereNotNull('machine_number')),
        ];
        $notesRule = ['nullable', 'string', 'max:2000', new SafePlainText(true)];
        $typeRule = ['required', 'string', Rule::in($this->allowedPreventiveTypes())];
        $groupRule = ['nullable', 'string', Rule::in(array_keys(PreventiveCategory::scheduleGroupLabels()))];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            return [
                'schedule_group' => $groupRule,
                'machine_name_id' => $machineRule,
                'plan_date' => ['required', 'date', 'date_format:Y-m-d'],
                'preventive_type' => $typeRule,
                'notes' => $notesRule,
            ];
        }

        return [
            'schedule_group' => $groupRule,
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.machine_name_id' => $machineRule,
            'rows.*.plan_date' => ['required', 'date', 'date_format:Y-m-d'],
            'rows.*.preventive_type' => $typeRule,
            'rows.*.notes' => $notesRule,
        ];
    }

    /**
     * @return list<string>
     */
    private function allowedPreventiveTypes(): array
    {
        $groupTypes = PreventiveCategory::typesForScheduleGroup((string) $this->input('schedule_group'));
        if ($groupTypes !== []) {
            return array_keys($groupTypes);
        }

        return PreventiveChecklistItem::scheduleTypeValues();
    }

    public function attributes(): array
    {
        return [
            'schedule_group' => 'kategori preventive',
            'machine_name_id' => 'nomer mesin',
            'plan_date' => 'plan tanggal PM',
            'preventive_type' => 'tipe preventive',
            'notes' => 'catatan',
            'rows' => 'jadwal preventive',
            'rows.*.machine_name_id' => 'nomer mesin',
            'rows.*.plan_date' => 'plan tanggal PM',
            'rows.*.preventive_type' => 'tipe preventive',
            'rows.*.notes' => 'catatan',
        ];
    }
}
