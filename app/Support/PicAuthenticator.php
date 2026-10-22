<?php

namespace App\Support;

use App\Models\Technician;
use Illuminate\Validation\ValidationException;

class PicAuthenticator
{
    public static function fromIdAndPin(int $technicianId, string $pin): Technician
    {
        $pic = Technician::query()->find($technicianId);

        if ($pic === null || ! $pic->is_active) {
            throw ValidationException::withMessages([
                'technician_id' => 'PIC teknisi ini tidak aktif.',
            ]);
        }

        if (! $pic->pinMatches($pin)) {
            throw ValidationException::withMessages([
                'pin' => 'PIN PIC teknisi tidak sesuai.',
            ]);
        }

        return $pic;
    }
}
