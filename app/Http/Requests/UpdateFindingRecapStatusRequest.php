<?php

namespace App\Http\Requests;

use App\Rules\SafePlainText;
use App\Support\FindingRecapStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFindingRecapStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->seesFullDashboard() === true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(FindingRecapStatus::values())],
            'note' => ['required', 'string', 'max:2000', new SafePlainText(true)],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'status',
            'note' => 'catatan',
        ];
    }
}
