<?php

namespace App\Support;

use App\Models\Technician;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class TechnicianShift
{
    public static function assertCanTake(Technician $technician): void
    {
        $message = self::denialMessage($technician);

        if ($message === null) {
            return;
        }

        throw ValidationException::withMessages([
            'technician_id' => $message,
        ]);
    }

    public static function ensureCanTake(Technician $technician): void
    {
        $message = self::denialMessage($technician);

        if ($message === null) {
            return;
        }

        throw new HttpResponseException(
            redirect()
                ->route('home')
                ->withErrors(['technician_id' => $message])
        );
    }

    private static function denialMessage(Technician $technician): ?string
    {
        $current = WorkShift::currentNumber();
        $currentLabel = WorkShift::labels()['shift_'.$current];

        if ($technician->off_duty) {
            return $technician->name.' sedang Off-duty, jadi tidak bisa mengambil OP atau PM.';
        }

        if (! in_array((int) $technician->shift, [1, 2, 3], true)) {
            return 'Teknisi ini belum dipetakan ke shift, jadi belum bisa mengambil tugas. Sekarang '.$currentLabel.'.';
        }

        $assigned = (int) $technician->shift;
        if ($assigned === $current || ($technician->overtime && WorkShift::nextNumber($assigned) === $current)) {
            return null;
        }

        $ownLabel = WorkShift::labels()['shift_'.$assigned];
        if ($technician->overtime) {
            $nextLabel = WorkShift::labels()['shift_'.WorkShift::nextNumber($assigned)];

            return $technician->name.' hanya bisa mengambil tugas pada '.$ownLabel.' dan shift berikutnya ('.$nextLabel.'). Sekarang '.$currentLabel.'.';
        }

        return $technician->name.' hanya bisa mengambil tugas pada '.$ownLabel.'. Sekarang '.$currentLabel.'.';
    }
}
