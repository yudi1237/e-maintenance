<?php

namespace App\Http\Requests;

use App\Models\MaintenanceReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceReportStep2Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $choice = $this->input('penggunaan_sparepart_choice');

        if ($this->input('worker_mode') !== 'team') {
            $this->merge(['technician_ids' => []]);
        }

        if ($choice !== 'sparepart') {
            $this->merge(['sparepart_items' => []]);

            return;
        }

        $items = collect($this->input('sparepart_items', []))
            ->map(function ($row) {
                $qty = $row['qty'] ?? null;

                return [
                    'name' => trim((string) ($row['name'] ?? '')),
                    'qty' => $qty === '' || $qty === null ? null : $qty,
                ];
            })
            ->filter(fn (array $row) => $row['name'] !== '' || $row['qty'] !== null)
            ->values()
            ->all();

        $this->merge(['sparepart_items' => $items]);
    }

    public function rules(): array
    {
        $sparepartChosen = $this->input('penggunaan_sparepart_choice') === 'sparepart';
        $teamChosen = $this->input('worker_mode') === 'team';

        return [
            'masalah' => ['required', 'string'],
            'analisa_penyebab_kerusakan' => ['required', 'string'],
            'tindakan_perbaikan' => ['required', 'string'],
            'penggunaan_sparepart_choice' => ['required', 'in:sparepart,none'],
            'sparepart_items' => [$sparepartChosen ? 'required' : 'nullable', 'array', $sparepartChosen ? 'min:1' : 'nullable'],
            'sparepart_items.*.name' => [$sparepartChosen ? 'required' : 'nullable', 'string', 'max:255'],
            'sparepart_items.*.qty' => [$sparepartChosen ? 'required' : 'nullable', 'integer', 'min:1'],
            'evidence' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp,pdf'],
            'worker_mode' => ['required', 'in:self,team'],
            'technician_ids' => [$teamChosen ? 'required' : 'nullable', 'array', $teamChosen ? 'min:1' : 'nullable'],
            'technician_ids.*' => ['integer', Rule::exists('technicians', 'id')->where('is_active', true)],
            'pin' => [
                Rule::requiredIf(function (): bool {
                    if (! $this->routeIs('home.submissions.op.update')) {
                        return true;
                    }

                    $report = $this->route('report');

                    return $report instanceof MaintenanceReport
                        && (bool) $report->operatorReport?->claimed_technician_id;
                }),
                'nullable',
                'digits_between:4,6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'penggunaan_sparepart_choice.required' => 'Pilih penggunaan sparepart.',
            'penggunaan_sparepart_choice.in' => 'Pilih Sparepart atau Tidak ada.',
            'sparepart_items.required' => 'Isi minimal satu nama sparepart dan qty.',
            'sparepart_items.min' => 'Isi minimal satu nama sparepart dan qty.',
            'sparepart_items.*.name.required' => 'Nama sparepart wajib diisi.',
            'sparepart_items.*.qty.required' => 'Qty sparepart wajib diisi.',
            'sparepart_items.*.qty.integer' => 'Qty sparepart harus angka.',
            'sparepart_items.*.qty.min' => 'Qty sparepart minimal 1.',
            'worker_mode.required' => 'Pilih yang mengerjakan.',
            'worker_mode.in' => 'Pilih Teknisi Tim atau Saya sendiri.',
            'technician_ids.required' => 'Pilih minimal satu teknisi tim.',
            'technician_ids.min' => 'Pilih minimal satu teknisi tim.',
            'technician_ids.*.exists' => 'Teknisi yang dipilih tidak valid atau tidak aktif.',
            'pin.required' => 'Masukkan PIN PIC yang mengambil tiket ini untuk Close ticket.',
            'pin.digits_between' => 'PIN harus 4 sampai 6 digit angka.',
        ];
    }
};
