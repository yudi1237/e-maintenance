<?php

namespace App\Http\Requests;

use App\Models\Technician;
use Illuminate\Foundation\Http\FormRequest;

class ClaimOperatorReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTechnician() ?? false;
    }

    public function rules(): array
    {
        return [
            'technician_id' => ['required', 'integer', 'exists:technicians,id'],
            'pin' => ['required', 'digits_between:4,6'],
        ];
    }

    public function attributes(): array
    {
        return [
            'technician_id' => 'PIC teknisi',
            'pin' => 'PIN',
        ];
    }

    public function messages(): array
    {
        return [
            'technician_id.required' => 'Pilih PIC teknisi terlebih dahulu.',
            'pin.required' => 'Masukkan PIN PIC teknisi.',
            'pin.digits_between' => 'PIN harus 4 sampai 6 digit angka.',
        ];
    }

    public function technician(): Technician
    {
        return Technician::query()->findOrFail($this->integer('technician_id'));
    }
}
