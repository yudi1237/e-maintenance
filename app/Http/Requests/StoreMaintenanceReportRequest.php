<?php

namespace App\Http\Requests;

use App\Models\DamageCategory;
use App\Models\MachineName;
use App\Models\OperatorReport;
use App\Support\InjectionMachine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMaintenanceReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'machine_name_id' => $this->filled('machine_name_id') ? $this->machine_name_id : null,
            'mold_assy_utility' => $this->filled('mold_assy_utility') ? trim($this->mold_assy_utility) : null,
            'damage_category_other' => $this->filled('damage_category_other') ? trim($this->damage_category_other) : null,
        ]);

        if ($this->filled('machine_name_id')) {
            $machine = MachineName::find($this->integer('machine_name_id'));
            if ($machine?->machine_number) {
                $this->merge([
                    'machine_number' => $machine->machine_number,
                    'machine_name_text' => null,
                ]);
            }
        }

        if ($this->filled('operator_report_id')) {
            $ticket = OperatorReport::query()->find($this->integer('operator_report_id'));
            if ($ticket?->machine_name_id) {
                $this->merge([
                    'machine_name_id' => $ticket->machine_name_id,
                    'machine_number' => $ticket->machine_number,
                    'machine_name_text' => null,
                    'mold_assy_utility' => $this->filled('mold_assy_utility')
                        ? trim((string) $this->mold_assy_utility)
                        : $ticket->item_name,
                ]);
            }
        }

        if ($this->filled('repair_started_date') && $this->has('repair_started_hour') && $this->has('repair_started_minute')) {
            $startedTime = sprintf('%02d:%02d', (int) $this->repair_started_hour, (int) $this->repair_started_minute);
            $this->merge([
                'repair_started_time' => $startedTime,
                'repair_started_at' => $this->repair_started_date.' '.$startedTime.':00',
            ]);
        }

        if ($this->filled('repair_finished_date') && $this->has('repair_finished_hour') && $this->has('repair_finished_minute')) {
            $finishedTime = sprintf('%02d:%02d', (int) $this->repair_finished_hour, (int) $this->repair_finished_minute);
            $this->merge([
                'repair_finished_time' => $finishedTime,
                'repair_finished_at' => $this->repair_finished_date.' '.$finishedTime.':00',
            ]);
        }
    }

    public function rules(): array
    {
        $category = DamageCategory::find($this->integer('damage_category_id'));

        return [
            'operator_report_id' => ['required', 'integer', 'exists:operator_reports,id'],
            'machine_number' => ['required', 'integer', 'min:1', 'max:'.InjectionMachine::count()],
            'machine_name_id' => ['required', 'integer', 'exists:machine_names,id'],
            'damage_category_id' => ['required', 'integer', 'exists:damage_categories,id'],
            'damage_category_other' => [
                $category?->allows_other ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'mold_assy_utility' => ['nullable', 'string', 'max:255'],
            'work_shift' => ['required', Rule::in(['shift_1', 'shift_2', 'shift_3'])],
            'repair_started_date' => ['required', 'date'],
            'repair_started_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'repair_started_minute' => ['required', 'integer', 'min:0', 'max:59'],
            'repair_finished_date' => ['required', 'date'],
            'repair_finished_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'repair_finished_minute' => ['required', 'integer', 'min:0', 'max:59'],
            'repair_started_at' => ['required', 'date'],
            'repair_finished_at' => ['required', 'date', 'after_or_equal:repair_started_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'operator_report_id.required' => 'Pilih tiket On Progress yang akan dilaporkan.',
            'operator_report_id.exists' => 'Tiket Input OP tidak valid.',
            'machine_number.required' => 'Pilih nomor mesin.',
            'machine_name_id.required' => 'Pilih mesin dari master.',
            'machine_name_id.exists' => 'Mesin dari master tidak valid.',
            'damage_category_id.required' => 'Pilih kategori kerusakan.',
            'damage_category_other.required' => 'Isi keterangan untuk kategori Other.',
            'work_shift.required' => 'Pilih shift kerja.',
            'repair_started_date.required' => 'Isi tanggal mulai perbaikan.',
            'repair_started_hour.required' => 'Pilih jam mulai (format 24 jam).',
            'repair_started_minute.required' => 'Pilih menit mulai.',
            'repair_finished_date.required' => 'Isi tanggal selesai perbaikan.',
            'repair_finished_hour.required' => 'Pilih jam selesai (format 24 jam).',
            'repair_finished_minute.required' => 'Pilih menit selesai.',
            'repair_finished_at.after_or_equal' => 'Waktu selesai harus sama atau setelah waktu mulai.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            $ticketId = $this->integer('operator_report_id');
            if (! $ticketId) {
                return;
            }

            $ticket = OperatorReport::query()->find($ticketId);
            if (! $ticket) {
                return;
            }

            if (! $ticket->canCloseByTechnician($this->user()) || ! $ticket->isOnProgress()) {
                $validator->errors()->add('operator_report_id', 'Pilih Close by teknisi dan masukkan PIN PIC yang mengambil tiket ini.');
            }

            $existing = $ticket->maintenanceReport;
            if ($existing?->isStep2Completed()) {
                $validator->errors()->add('operator_report_id', 'Laporan Problem untuk tiket ini sudah dibuat.');
            }
        });
    }
}
