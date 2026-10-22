<?php

namespace App\Http\Requests\Admin;

use App\Models\PreventiveReport;
use App\Support\PreventiveStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePreventiveReportStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->seesPreventiveResults() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(PreventiveStatus::values())],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'status approval',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var PreventiveReport|null $report */
            $report = $this->route('preventiveReport');
            if (! $report) {
                return;
            }

            $current = $report->status ?? PreventiveStatus::CLOSE_BY_TECHNICIAN;
            $next = (string) $this->input('status');

            if (! PreventiveStatus::canTransition($this->user(), $current, $next)) {
                $validator->errors()->add(
                    'status',
                    PreventiveStatus::transitionDeniedMessage($current, $next)
                );
            }
        });
    }
};
