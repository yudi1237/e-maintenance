<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MachineName;
use App\Support\InjectionMachine;
use App\Support\MachineQrCode;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class MachineQrController extends Controller
{
    public function image(int $machineNumber): Response
    {
        abort_unless(auth()->user()?->seesFullDashboard(), 403);
        abort_unless(InjectionMachine::isValid($machineNumber), 404);

        return response(MachineQrCode::png($machineNumber), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function download(int $machineNumber): Response
    {
        abort_unless(auth()->user()?->seesFullDashboard(), 403);
        abort_unless(InjectionMachine::isValid($machineNumber), 404);

        return response(MachineQrCode::png($machineNumber), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.MachineQrCode::filename($machineNumber).'"',
        ]);
    }

    public function downloadAll(): StreamedResponse
    {
        abort_unless(auth()->user()?->seesFullDashboard(), 403);

        $numbers = MachineName::query()
            ->active()
            ->whereNotNull('machine_number')
            ->orderBy('machine_number')
            ->pluck('machine_number')
            ->map(fn ($number) => (int) $number)
            ->filter(fn (int $number) => InjectionMachine::isValid($number))
            ->unique()
            ->values();

        abort_if($numbers->isEmpty(), 404, 'Belum ada data mesin untuk QR Code.');

        $zipPath = tempnam(sys_get_temp_dir(), 'qr-mesin-');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file ZIP QR Code.');
        }

        foreach ($numbers as $number) {
            $zip->addFromString(MachineQrCode::filename($number), MachineQrCode::png($number));
        }

        $zip->close();

        return response()->streamDownload(function () use ($zipPath) {
            readfile($zipPath);
            @unlink($zipPath);
        }, 'qr-semua-mesin.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }
}
