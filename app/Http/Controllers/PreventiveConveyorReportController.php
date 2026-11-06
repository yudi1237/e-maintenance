<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RequiresPicSession;
use App\Http\Requests\StorePreventiveConveyorReportRequest;
use App\Models\PreventiveConveyorReport;
use App\Models\Technician;
use App\Models\TechnicianPreventiveJob;
use App\Support\PreventiveChecklist;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveEvidence;
use App\Support\SparepartUsage;
use App\Support\TechnicianShift;
use App\Support\WorkShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PreventiveConveyorReportController extends Controller
{
    use RequiresPicSession;

    public function create(): View
    {
        $pic = $this->requirePic();
        TechnicianShift::ensureCanTake($pic);
        TechnicianPreventiveJob::open($pic, null, 'CONVEYOR');

        return view('preventive.conveyor-create', [
            'checklistGroups' => PreventiveConveyorChecklist::groups(),
            'checklistValues' => PreventiveConveyorChecklist::valueLabels(),
            'currentDateTime' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'picName' => $pic->name,
            'detectedShift' => WorkShift::detect(),
            'shiftLabels' => WorkShift::labels(),
            'technicians' => Technician::query()->active()->orderBy('name')->get(),
            'recentReports' => PreventiveConveyorReport::query()
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }

    public function store(StorePreventiveConveyorReportRequest $request): RedirectResponse
    {
        $pic = $this->requirePic();
        $validated = $request->validated();
        $items = $validated['items'];
        $remarks = PreventiveChecklist::sanitizeRemarks(
            array_keys(PreventiveConveyorChecklist::items()),
            $validated['remarks'] ?? []
        );
        $sparepart = SparepartUsage::persisted($validated);

        PreventiveConveyorReport::create([
            'user_id' => auth()->id(),
            'pic_name' => $pic->name,
            'conveyor_name' => $validated['conveyor_name'],
            'work_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'work_shift' => $validated['work_shift'],
            'team_members' => ($team = Technician::teamSnapshot($validated['technician_ids'] ?? [])) === [] ? null : $team,
            'items' => $items,
            'item_remarks' => $remarks === [] ? null : $remarks,
            'notes' => $validated['notes'] ?? null,
            'penggunaan_sparepart' => $sparepart['text'],
            'sparepart_items' => $sparepart['items'],
            'evidence_paths' => PreventiveEvidence::storeFromRequest($request, PreventiveEvidence::CONVEYOR_DIRECTORY) ?: null,
            'result' => PreventiveConveyorChecklist::result($items),
        ]);

        TechnicianPreventiveJob::close($pic, null, 'CONVEYOR');

        return redirect()
            ->route('preventive-conveyor.create')
            ->with('success', 'Preventive Conveyor berhasil disimpan.');
    }
}
