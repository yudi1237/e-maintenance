<?php

namespace App\Http\Requests;

use App\Models\FindingRecap;
use App\Rules\SafePlainText;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFindingRecapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->seesFullDashboard() === true;
    }

    public function rules(): array
    {
        /** @var FindingRecap|null $recap */
        $recap = $this->route('findingRecap');

        return [
            'due_date' => ['required', 'date', 'date_format:Y-m-d'],
            'machine_name_id' => [
                'required',
                'integer',
                Rule::exists('machine_names', 'id')->where(function ($query) use ($recap) {
                    $query->whereNotNull('machine_number')
                        ->where(function ($inner) use ($recap) {
                            $inner->where('is_active', true);
                            if ($recap?->machine_name_id) {
                                $inner->orWhere('id', $recap->machine_name_id);
                            }
                        });
                }),
            ],
            'equipment_name' => ['required', 'string', 'max:255', new SafePlainText],
        ];
    }

    public function attributes(): array
    {
        return [
            'due_date' => 'due date',
            'machine_name_id' => 'no mesin',
            'equipment_name' => 'nama equipment',
        ];
    }
}
