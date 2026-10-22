<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Technician;
use App\Support\PicSession;
use Illuminate\Http\Exceptions\HttpResponseException;

trait RequiresPicSession
{
    protected function requirePic(): Technician
    {
        $pic = PicSession::technician();

        if ($pic) {
            return $pic;
        }

        throw new HttpResponseException(
            redirect()
                ->route('home')
                ->with('success', 'Masukkan PIN PIC teknisi untuk membuka Preventive.')
        );
    }
}
