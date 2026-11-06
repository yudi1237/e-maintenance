<?php

namespace App\Http\Requests\Admin;

use App\Models\MaintenanceReport;
use App\Support\DamageCauseCategory;
use App\Support\RepairStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateReportStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('line_stop_mesin') === '') {
            $this->merge(['line_stop_mesin' => null]);
        }

        if ($this->input('sub_damage_category_id') === '') {
            $this->merge(['sub_damage_category_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'status_perbaikan' => ['required', Rule::in(RepairStatus::values())],
            'tindakan_perbaikan' => ['required_if:status_perbaikan,'.RepairStatus::CLOSE, 'nullable', 'string'],
            'damage_cause_category' => [
                'required_if:status_perbaikan,'.RepairStatus::CLOSE,
                'nullable',
                'string',
                Rule::in(DamageCauseCategory::values()),
            ],
            'line_stop_mesin' => [
                'required_if:status_perbaikan,'.RepairStatus::CLOSE,
                'nullable',
                Rule::in(['yes', 'no']),
            ],
            'sub_damage_category_id' => [
                'required_if:status_perbaikan,'.RepairStatus::CLOSE,
                'nullable',
                'integer',
                Rule::exists('damage_categories', 'id')->where('is_active', true),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'status_perbaikan' => 'status perbaikan',
            'tindakan_perbaikan' => 'Tindakan Perbaikan',
            'damage_cause_category' => 'Kategori Penyebab Kerusakan',
            'line_stop_mesin' => 'Line Stop Mesin',
            'sub_damage_category_id' => 'Sub Kerusakan',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var MaintenanceReport|null $report */
            $report = $this->route('report');
            if (! $report) {
                return;
            }

            $report->loadMissing('operatorReport');
            $current = $report->status_perbaikan ?? RepairStatus::OPEN;
            $next = (string) $this->input('status_perbaikan');
            $ticket = $report->operatorReport;

            if ($next === RepairStatus::CLOSE_BY_TECHNICIAN && $current !== RepairStatus::CLOSE_BY_TECHNICIAN) {
                $validator->errors()->add('status_perbaikan', 'Close by teknisi hanya dapat diubah oleh teknisi yang mengambil tiket OP.');

                return;
            }

            $isOpen = $current === RepairStatus::OPEN || $ticket?->isOpen();
            if ($isOpen && in_array($next, RepairStatus::closedLikeValues(), true)) {
                $validator->errors()->add('status_perbaikan', 'Tiket Open tidak bisa langsung Close. Ubah ke On Progress terlebih dahulu.');

                return;
            }

            if (! RepairStatus::canTransition($this->user(), $current, $next)) {
                $validator->errors()->add(
                    'status_perbaikan',
                    RepairStatus::transitionDeniedMessage($current, $next)
                );
            }
        });
    }
}
