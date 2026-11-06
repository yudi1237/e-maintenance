<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaimOperatorReportRequest;
use App\Http\Requests\StoreMaintenanceReportRequest;
use App\Http\Requests\StoreMaintenanceReportStep2Request;
use App\Models\DamageCategory;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Models\Technician;
use App\Support\PicAuthenticator;
use App\Support\PicSession;
use App\Support\RepairStatus;
use App\Support\TechnicianShift;
use App\Support\WorkShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MaintenanceReportController extends Controller
{
    public function create(): View
    {
        $pic = PicSession::technician();

        $openTickets = OperatorReport::query()
            ->claimableBy(auth()->id(), $pic?->id)
            ->with(['machineName', 'maintenanceReport'])
            ->latest()
            ->get();

        $selectedTicketId = old('operator_report_id', request('ticket'));
        $selectedTicket = $selectedTicketId
            ? $openTickets->first(fn ($ticket) => (int) $ticket->id === (int) $selectedTicketId)
            : null;
        if ($selectedTicketId && ! $selectedTicket) {
            $selectedTicketId = null;
        }

        $draftReport = $selectedTicket?->maintenanceReport;
        if ($draftReport?->isStep2Completed()) {
            $draftReport = null;
        }

        $started = $this->jakartaDateTimeParts(
            $draftReport?->repair_started_at ?? $selectedTicket?->claimed_at
        );
        $finished = $this->jakartaDateTimeParts($draftReport?->repair_finished_at ?? now());

        return view('reports.create', [
            'machineNames' => MachineName::active()
                ->whereNotNull('machine_number')
                ->orderBy('machine_number')
                ->orderBy('name')
                ->get(),
            'damageCategories' => DamageCategory::active()->get(),
            'openTickets' => $openTickets,
            'selectedTicketId' => $selectedTicketId,
            'draftReport' => $draftReport,
            'currentDate' => now()->timezone('Asia/Jakarta')->format('Y-m-d'),
            'currentDateTime' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'startedDate' => $started['date'],
            'startedHour' => $started['hour'],
            'startedMinute' => $started['minute'],
            'finishedDate' => $finished['date'],
            'finishedHour' => $finished['hour'],
            'finishedMinute' => $finished['minute'],
            'picName' => $pic?->name ?? auth()->user()->name,
            'activePic' => $pic,
            'detectedShift' => $draftReport?->work_shift ?? WorkShift::detect(),
            'shiftLabels' => WorkShift::labels(),
            'preserveRepairTimes' => old('repair_started_date') !== null || old('repair_finished_date') !== null,
        ]);
    }

    public function claimTicket(ClaimOperatorReportRequest $request, OperatorReport $operatorReport): RedirectResponse
    {
        $pic = $this->authenticatedPic($request);
        TechnicianShift::assertCanTake($pic);

        DB::transaction(function () use ($operatorReport, $pic) {
            $ticket = OperatorReport::query()
                ->whereKey($operatorReport->id)
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

        return back()->with('success', 'Tiket #'.$operatorReport->id.' diambil oleh PIC '.$pic->name.'. Status menjadi On Progress.');
    }

    public function identifyPic(ClaimOperatorReportRequest $request, OperatorReport $operatorReport): RedirectResponse
    {
        $pic = $this->authenticatedPic($request);

        if ((int) $operatorReport->claimed_technician_id !== (int) $pic->id) {
            throw ValidationException::withMessages([
                'technician_id' => 'PIN harus milik PIC yang mengambil tiket ini ('.$operatorReport->pic_name.').',
            ]);
        }

        PicSession::set($pic);

        return back()->with('success', 'PIC aktif: '.$pic->name.'.');
    }

    public function authorizeClose(ClaimOperatorReportRequest $request, OperatorReport $operatorReport): RedirectResponse
    {
        $pic = $this->authenticatedPic($request);

        if (! $operatorReport->isOnProgress()) {
            throw ValidationException::withMessages([
                'ticket' => 'Tiket ini belum On Progress, sehingga belum bisa di-Close.',
            ]);
        }

        if ((int) $operatorReport->claimed_technician_id !== (int) $pic->id) {
            throw ValidationException::withMessages([
                'technician_id' => 'PIN harus milik PIC yang mengambil tiket ini ('.$operatorReport->pic_name.').',
            ]);
        }

        PicSession::set($pic);
        PicSession::authorizeClose($operatorReport->id);

        $report = $operatorReport->maintenanceReport;
        if ($report?->isStep2Completed()) {
            $report->update([
                'status_perbaikan' => RepairStatus::CLOSE_BY_TECHNICIAN,
            ]);
            $operatorReport->update([
                'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
            ]);
            PicSession::clearCloseAuthorization();

            return redirect()
                ->route('home')
                ->with('success', 'Tiket #'.$operatorReport->id.' di-Close by teknisi. Form 2 sudah lengkap.');
        }

        PicSession::lockForm2();

        return redirect()
            ->route('reports.create', ['ticket' => $operatorReport->id])
            ->with('success', 'PIN PIC '.$pic->name.' terverifikasi. Isi Form 1 untuk Close ticket.');
    }

    public function store(StoreMaintenanceReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $category = DamageCategory::findOrFail($validated['damage_category_id']);

        $report = DB::transaction(function () use ($validated, $category) {
            $ticket = OperatorReport::query()
                ->whereKey($validated['operator_report_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $ticket->loadMissing('claimedTechnician');

            if (! $ticket->canCloseByTechnician(auth()->user()) || ! $ticket->isOnProgress()) {
                throw ValidationException::withMessages([
                    'operator_report_id' => 'Pilih Close by teknisi dan masukkan PIN PIC yang mengambil tiket ini.',
                ]);
            }

            $ticket->loadMissing('maintenanceReport');
            $existing = $ticket->maintenanceReport;

            if ($existing?->isStep2Completed()) {
                throw ValidationException::withMessages([
                    'operator_report_id' => 'Laporan Problem untuk tiket ini sudah dibuat.',
                ]);
            }

            $payload = [
                'report_date' => now()->timezone('Asia/Jakarta')->toDateString(),
                'machine_number' => $ticket->machine_number,
                'machine_name_id' => $ticket->machine_name_id,
                'machine_name_text' => null,
                'damage_category_id' => $validated['damage_category_id'],
                'damage_category_other' => $category->allows_other ? ($validated['damage_category_other'] ?? null) : null,
                'mold_assy_utility' => $validated['mold_assy_utility'] ?? $ticket->item_name,
                'user_id' => auth()->id(),
                'pic_name' => $ticket->claimedTechnician?->name ?? auth()->user()->name,
                'work_shift' => $validated['work_shift'],
                'repair_started_at' => $validated['repair_started_at'],
                'repair_finished_at' => $validated['repair_finished_at'],
                'status_perbaikan' => RepairStatus::ON_PROGRESS,
                'operator_report_id' => $ticket->id,
                'ada_op' => true,
            ];

            if ($existing) {
                $existing->update($payload);

                return $existing;
            }

            return MaintenanceReport::create($payload);
        });

        PicSession::unlockForm2($report->id);

        return redirect()
            ->route('reports.step2', $report)
            ->with('success', 'Form 1 berhasil disimpan. Silakan lanjutkan Form 2.');
    }

    public function step2(MaintenanceReport $report): View|RedirectResponse
    {
        $this->authorizeReport($report);

        if ($report->isStep2Completed()) {
            return redirect()
                ->route('home')
                ->with('success', 'Laporan ini sudah lengkap (Form 1 & 2).');
        }

        $this->assertTicketCanBeClosedByTechnician($report);

        if (! PicSession::isForm2Unlocked($report->id)) {
            return redirect()
                ->route('reports.create', ['ticket' => $report->operator_report_id])
                ->with('success', 'Lengkapi Form 1 terlebih dahulu sebelum Form 2.');
        }

        $report->load(['machineName', 'damageCategory', 'operatorReport.claimedTechnician']);

        return view('reports.step2', [
            'report' => $report,
            'technicians' => Technician::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function storeStep2(StoreMaintenanceReportStep2Request $request, MaintenanceReport $report): RedirectResponse
    {
        $this->authorizeReport($report);

        if ($report->isStep2Completed()) {
            return redirect()
                ->route('home')
                ->with('success', 'Form 2 sudah pernah disimpan.');
        }

        $this->assertTicketCanBeClosedByTechnician($report);

        $validated = $request->validated();

        $ticket = $report->operatorReport ?? OperatorReport::query()->find($report->operator_report_id);
        $ticket?->loadMissing('claimedTechnician');
        $pic = $ticket?->claimedTechnician;
        if (! $pic?->pinMatches((string) $validated['pin'])) {
            throw ValidationException::withMessages([
                'pin' => 'PIN harus milik PIC yang mengambil tiket ini ('.($ticket?->pic_name ?? 'PIC').').',
            ]);
        }

        $evidencePath = $report->evidence_path;
        if ($request->hasFile('evidence')) {
            if ($evidencePath) {
                Storage::disk('public')->delete($evidencePath);
            }
            $evidencePath = $request->file('evidence')->store('evidence', 'public');
        }

        $sparepart = MaintenanceReport::persistSparepart(
            $validated['penggunaan_sparepart_choice'],
            $validated['sparepart_items'] ?? []
        );

        $workers = ['mode' => $validated['worker_mode'], 'names' => []];
        if ($validated['worker_mode'] === 'self') {
            $workers['names'] = [auth()->user()->name];
        } else {
            $team = Technician::query()
                ->active()
                ->whereIn('id', $validated['technician_ids'] ?? [])
                ->orderBy('name')
                ->get();
            $workers['technician_ids'] = $team->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
            $workers['names'] = $team->map(fn (Technician $technician) => $technician->optionLabel())->all();
        }

        $report->update([
            'masalah' => $validated['masalah'],
            'analisa_penyebab_kerusakan' => $validated['analisa_penyebab_kerusakan'],
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
            'tindakan_perbaikan_2' => null,
            'penggunaan_sparepart' => $sparepart['text'],
            'sparepart_items' => $sparepart['items'],
            'yang_mengerjakan' => $workers,
            'ada_op' => $report->operator_report_id !== null,
            'status_perbaikan' => RepairStatus::CLOSE_BY_TECHNICIAN,
            'evidence_path' => $evidencePath,
            'step2_completed_at' => now(),
        ]);

        if ($report->operator_report_id) {
            OperatorReport::query()->whereKey($report->operator_report_id)->update([
                'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
            ]);
            PicSession::clearCloseAuthorization();
        }

        PicSession::lockForm2();

        return redirect()
            ->route('home')
            ->with('data_terkirim', true);
    }

    private function authorizeReport(MaintenanceReport $report): void
    {
        if ($report->user_id !== auth()->id()) {
            abort(403, 'Akses hanya untuk pemilik laporan.');
        }
    }

    private function assertTicketCanBeClosedByTechnician(MaintenanceReport $report): void
    {
        $ticket = $report->operatorReport ?? OperatorReport::query()->find($report->operator_report_id);

        if (! $ticket?->canCloseByTechnician(auth()->user())) {
            abort(403, 'Close ticket memerlukan PIN PIC yang mengambil tiket ini.');
        }
    }

    /**
     * @return array{date: string, hour: int, minute: int}
     */
    private function jakartaDateTimeParts(?\DateTimeInterface $time): array
    {
        $local = Carbon::parse($time ?? now())->timezone('Asia/Jakarta');

        return [
            'date' => $local->format('Y-m-d'),
            'hour' => (int) $local->format('H'),
            'minute' => (int) $local->format('i'),
        ];
    }

    private function authenticatedPic(ClaimOperatorReportRequest $request): Technician
    {
        return PicAuthenticator::fromIdAndPin(
            $request->integer('technician_id'),
            (string) $request->input('pin')
        );
    }
}
