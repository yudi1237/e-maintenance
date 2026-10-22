<?php

namespace App\Http\Requests;

use App\Rules\SafePlainText;
use App\Support\InjectionMachine;
use App\Support\MachineQrCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOperatorReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $machineNumber = (int) $this->route('machineNumber');
        $token = (string) $this->route('token');

        if (! InjectionMachine::isValid($machineNumber) || ! MachineQrCode::isValidToken($machineNumber, $token)) {
            abort(404);
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        $cleaned = [];

        foreach (['operator_name', 'npk', 'item_name', 'problem', 'chronology'] as $field) {
            $value = $this->input($field);
            if (! is_string($value)) {
                continue;
            }

            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value = str_replace("\0", '', $value);
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
            if (! in_array($field, ['problem', 'chronology'], true)) {
                $value = preg_replace('/\s+/u', ' ', $value) ?? '';
            }
            $cleaned[$field] = trim($value);
        }

        $time = $this->input('stop_time');
        if (is_string($time) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
            $cleaned['stop_time'] = substr($time, 0, 5);
        }

        $this->merge($cleaned);
    }

    public function rules(): array
    {
        return [
            'operator_name' => ['required', 'string', 'max:150', 'regex:/^[\p{L}\p{M} .\'\-]+$/u', new SafePlainText],
            'npk' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/'],
            'item_name' => ['required', 'string', 'max:200', 'regex:/^[\p{L}\p{M}0-9 .,\-\/()]+$/u', new SafePlainText],
            'stop_date' => ['required', 'date', 'date_format:Y-m-d'],
            'stop_time' => ['required', 'date_format:H:i'],
            'problem' => ['required', 'string', 'max:2000', new SafePlainText(true)],
            'chronology' => ['nullable', 'string', 'max:5000', new SafePlainText(true)],
        ];
    }

    public function attributes(): array
    {
        return [
            'operator_name' => 'Nama',
            'npk' => 'NPK',
            'item_name' => 'Nama Barang',
            'stop_date' => 'Stop Tanggal',
            'stop_time' => 'Jam',
            'problem' => 'Masalah',
            'chronology' => 'Kronologi',
        ];
    }

    public function messages(): array
    {
        return [
            'operator_name.required' => 'Nama wajib diisi.',
            'npk.required' => 'NPK wajib diisi.',
            'item_name.required' => 'Nama Barang wajib diisi.',
            'stop_date.required' => 'Stop Tanggal wajib diisi.',
            'stop_time.required' => 'Jam wajib diisi.',
            'problem.required' => 'Masalah wajib diisi.',
            'operator_name.regex' => 'Nama hanya boleh huruf dan spasi.',
            'npk.regex' => 'NPK hanya boleh huruf dan angka.',
            'item_name.regex' => 'Nama Barang hanya boleh teks biasa.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->allFiles() !== []) {
                $validator->errors()->add('operator_name', 'Input OP hanya menerima teks, tidak boleh mengunggah file.');
            }
        });
    }
}
