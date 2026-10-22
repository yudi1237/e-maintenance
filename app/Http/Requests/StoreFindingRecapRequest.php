<?php

namespace App\Http\Requests;

use App\Rules\SafePlainText;
use App\Support\FindingRecapStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFindingRecapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->seesFullDashboard() === true;
    }

    public function rules(): array
    {
        return [
            'found_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'due_date' => ['required', 'date', 'date_format:Y-m-d'],
            'machine_name_id' => [
                'required',
                'integer',
                Rule::exists('machine_names', 'id')->where(fn ($query) => $query
                    ->where('is_active', true)
                    ->whereNotNull('machine_number')),
            ],
            'equipment_name' => ['required', 'string', 'max:255', new SafePlainText],
            'status' => ['required', 'string', Rule::in(FindingRecapStatus::values())],
            'note' => ['required', 'string', 'max:2000', new SafePlainText(true)],
        ];
    }

    public function attributes(): array
    {
        return [
            'found_date' => 'tanggal',
            'due_date' => 'due date',
            'machine_name_id' => 'no mesin',
            'equipment_name' => 'nama equipment',
            'status' => 'status',
            'note' => 'catatan',
        ];
    }
}
