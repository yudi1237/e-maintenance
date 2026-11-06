<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaimOperatorReportRequest;
use App\Models\AreaOperatorReport;
use App\Support\AreaList;
use App\Support\PicAuthenticator;
use App\Support\PicSession;
use App\Support\RepairStatus;
use App\Support\TechnicianShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AreaOperatorReportController extends Controller
{
    public function create(): View
    {
        $now = now()->timezone('Asia/Jakarta');

        return view('area-op.create', [
            'areas' => AreaList::options(),
            'defaultDate' => old('stop_date', $now->format('Y-m-d')),
            'defaultTime' => old('stop_time', $now->format('H:i')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'area' => ['required', 'string', Rule::in(AreaList::options())],
            'reporter_name' => ['required', 'string', 'max:150'],
            'part_name' => ['nullable', 'string', 'max:200'],
            'equipment_name' => ['nullable', 'string', 'max:200'],
            'stop_date' => ['required', 'date'],
            'stop_time' => ['required', 'date_format:H:i'],
            'problem' => ['required', 'string', 'max:2000'],
            'chronology' => ['nullable', 'string', 'max:5000'],
        ], [
            'area.required' => 'Pilih area.',
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'problem.required' => 'Masalah wajib diisi.',
            'stop_date.required' => 'Stop tanggal wajib diisi.',
            'stop_time.required' => 'Jam kejadian wajib diisi.',
        ]);

        $ticket = AreaOperatorReport::query()->create([
            ...$validated,
            'part_name' => filled($validated['part_name'] ?? null) ? $validated['part_name'] : null,
            'equipment_name' => filled($validated['equipment_name'] ?? null) ? $validated['equipment_name'] : null,
            'chronology' => filled($validated['chronology'] ?? null) ? $validated['chronology'] : null,
            'status' => RepairStatus::OPEN,
        ]);

        return redirect()
            ->route('area-op.create')
            ->with('success', 'Tiket OP Area #'.$ticket->id.' sudah terkirim.');
    }

    public function claim(ClaimOperatorReportRequest $request, AreaOperatorReport $areaOperatorReport): RedirectResponse
    {
        $pic = PicAuthenticator::fromIdAndPin(
            $request->integer('technician_id'),
            (string) $request->input('pin')
        );
        TechnicianShift::assertCanTake($pic);

        DB::transaction(function () use ($areaOperatorReport, $pic) {
            $ticket = AreaOperatorReport::query()
                ->whereKey($areaOperatorReport->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $ticket->isOpen()) {
                throw ValidationException::withMessages([
                    'ticket' => 'Tiket ini sudah diambil atau tidak berstatus Open.',
                ]);
            }

            $ticket->update([
                'claimed_by' => auth()->id(),
                'claimed_technician_id' => $pic->id,
                'claimed_at' => now(),
                'status' => RepairStatus::ON_PROGRESS,
            ]);
        });

        PicSession::set($pic);

        return back()->with('success', 'Tiket OP Area #'.$areaOperatorReport->id.' diambil oleh PIC '.$pic->name.'. Status menjadi On Progress.');
    }

    public function close(ClaimOperatorReportRequest $request, AreaOperatorReport $areaOperatorReport): RedirectResponse
    {
        $pic = PicAuthenticator::fromIdAndPin(
            $request->integer('technician_id'),
            (string) $request->input('pin')
        );

        if (! $areaOperatorReport->isOnProgress()) {
            throw ValidationException::withMessages([
                'ticket' => 'Tiket ini belum On Progress, sehingga belum bisa di-Close.',
            ]);
        }

        if ((int) $areaOperatorReport->claimed_technician_id !== (int) $pic->id) {
            throw ValidationException::withMessages([
                'technician_id' => 'PIN harus milik PIC yang mengambil tiket ini ('.$areaOperatorReport->pic_name.').',
            ]);
        }

        $areaOperatorReport->update([
            'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
            'closed_at' => now(),
        ]);

        PicSession::set($pic);

        return back()->with('success', 'Tiket OP Area #'.$areaOperatorReport->id.' di-Close by teknisi.');
    }

    public function destroy(AreaOperatorReport $areaOperatorReport): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $areaOperatorReport->delete();

        return back()->with('success', 'Tiket OP Area sudah dihapus.');
    }
}
