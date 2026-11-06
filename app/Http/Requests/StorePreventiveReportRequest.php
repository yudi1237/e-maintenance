<?php

namespace App\Http\Requests;

use App\Models\MachineName;
use App\Models\PreventiveReport;
use App\Rules\SafePlainText;
use App\Support\EquipmentCategory;
use App\Support\PreventiveCategory;
use App\Support\PreventiveEvidence;
use App\Support\PreventiveMachineChecklist;
use App\Support\SparepartUsage;
use App\Support\WorkShift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePreventiveReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTechnician() === true;
    }

    protected function prepareForValidation(): void
    {
        $report = $this->route('preventiveReport');
        if ($report instanceof PreventiveReport) {
            $this->merge([
                'machine_name_id' => $report->machine_name_id,
                'category' => $report->category,
            ]);
        }

        SparepartUsage::prepare($this);
    }

    public function rules(): array
    {
        return [
            'machine_name_id' => ['required', 'integer', 'exists:machine_names,id'],
            'category' => ['required', 'string', Rule::in(PreventiveCategory::values())],
            'work_shift' => ['required', Rule::in(array_keys(WorkShift::labels()))],
            'technician_ids' => ['nullable', 'array'],
            'technician_ids.*' => ['integer', 'distinct', Rule::exists('technicians', 'id')->where('is_active', true)],
            'items' => ['required', 'array'],
            'items.*' => ['required', Rule::in(PreventiveMachineChecklist::allowedValues($this->input('category')))],
            'readings' => ['nullable', 'array'],
            'readings.*' => ['nullable', 'string', 'max:50', new SafePlainText],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:500', new SafePlainText],
            'notes' => ['nullable', 'string', 'max:2000'],
            ...SparepartUsage::rules($this),
            ...PreventiveEvidence::rules(),
            'equipment_number' => [
                Rule::requiredIf(fn () => PreventiveCategory::isUtility($this->input('category'))
                    && $this->input('category') !== 'ROBOT'
                    && ! EquipmentCategory::usesMasterDropdown($this->input('category'))),
                'nullable',
                'string',
                'max:50',
                new SafePlainText,
            ],
            'equipment_id' => [
                Rule::requiredIf(fn () => EquipmentCategory::usesMasterDropdown($this->input('category'))),
                'nullable',
                'integer',
                Rule::exists('equipments', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                    $mapped = EquipmentCategory::fromPreventive($this->input('category'));
                    if ($mapped) {
                        $query->where('category', $mapped);
                    }
                }),
            ],
            'robot_id' => [
                Rule::requiredIf(fn () => $this->input('category') === 'ROBOT'),
                'nullable',
                'integer',
                Rule::exists('robots', 'id')->where('is_active', true),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $category = $this->string('category')->toString();

            foreach (array_keys(PreventiveMachineChecklist::items($category)) as $key) {
                if (! $this->filled('items.'.$key)) {
                    $validator->errors()->add('items.'.$key, 'Item checklist wajib diisi.');
                }
            }

            foreach (PreventiveMachineChecklist::readingKeys($category) as $key) {
                if (! $this->filled('readings.'.$key)) {
                    $validator->errors()->add('readings.'.$key, 'Isian manual wajib diisi.');
                }
            }

            $machineId = $this->integer('machine_name_id');
            if (! $machineId) {
                return;
            }

            $machine = MachineName::query()->active()->find($machineId);
            if (! $machine?->machine_number) {
                $validator->errors()->add('machine_name_id', 'Mesin tidak valid atau tidak aktif.');
            }
        });
    }

    public function messages(): array
    {
        return [
            ...SparepartUsage::messages(),
            'machine_name_id.required' => 'Pilih mesin terlebih dahulu.',
            'category.required' => 'Pilih kategori preventive terlebih dahulu.',
            'category.in' => 'Kategori preventive tidak valid.',
            'robot_id.required' => 'Pilih '.lcfirst(PreventiveCategory::numberLabel($this->input('category'))).' terlebih dahulu.',
            'robot_id.exists' => PreventiveCategory::numberLabel($this->input('category')).' tidak valid atau tidak aktif.',
            'equipment_id.required' => 'Pilih '.lcfirst(PreventiveCategory::numberLabel($this->input('category'))).' terlebih dahulu.',
            'equipment_id.exists' => PreventiveCategory::numberLabel($this->input('category')).' tidak valid atau tidak aktif.',
            'equipment_number.required' => PreventiveCategory::numberLabel($this->input('category')).' wajib diisi.',
            'work_shift.required' => 'Pilih shift kerja.',
        ];
    }
}
