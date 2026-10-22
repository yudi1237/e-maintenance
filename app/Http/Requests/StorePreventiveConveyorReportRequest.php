<?php

namespace App\Http\Requests;

use App\Rules\SafePlainText;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveEvidence;
use App\Support\WorkShift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePreventiveConveyorReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTechnician() === true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('conveyor_name')) {
            $this->merge([
                'conveyor_name' => trim((string) $this->input('conveyor_name')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'conveyor_name' => ['required', 'string', 'max:200', new SafePlainText],
            'work_shift' => ['required', Rule::in(array_keys(WorkShift::labels()))],
            'items' => ['required', 'array'],
            'items.*' => ['required', Rule::in(array_keys(PreventiveConveyorChecklist::valueLabels()))],
            'notes' => ['nullable', 'string', 'max:2000', new SafePlainText(true)],
            ...PreventiveEvidence::rules(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach (array_keys(PreventiveConveyorChecklist::items()) as $key) {
                if (! $this->filled('items.'.$key)) {
                    $validator->errors()->add('items.'.$key, 'Item checklist wajib diisi.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'conveyor_name.required' => 'Isi nama conveyor terlebih dahulu.',
        ];
    }
}
