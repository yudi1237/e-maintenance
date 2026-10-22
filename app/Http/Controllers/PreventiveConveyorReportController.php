<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RequiresPicSession;
use App\Http\Requests\StorePreventiveConveyorReportRequest;
use App\Models\PreventiveConveyorReport;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveEvidence;
use App\Support\WorkShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PreventiveConveyorReportController extends Controller
{
    use RequiresPicSession;

    public function create(): View
    {
        $pic = $this->requirePic();

        return view('preventive.conveyor-create', [
            'checklistGroups' => PreventiveConveyorChecklist::groups(),
            'checklistValues' => PreventiveConveyorChecklist::valueLabels(),
            'currentDateTime' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'picName' => $pic->name,
            'detectedShift' => WorkShift::detect(),
            'shiftLabels' => WorkShift::labels(),
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

        PreventiveConveyorReport::create([
            'user_id' => auth()->id(),
            'pic_name' => $pic->name,
            'conveyor_name' => $validated['conveyor_name'],
            'work_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'work_shift' => $validated['work_shift'],
            'items' => $items,
            'notes' => $validated['notes'] ?? null,
            'evidence_paths' => PreventiveEvidence::storeFromRequest($request, PreventiveEvidence::CONVEYOR_DIRECTORY) ?: null,
            'result' => PreventiveConveyorChecklist::result($items),
        ]);

        return redirect()
            ->route('preventive-conveyor.create')
            ->with('success', 'Preventive Conveyor berhasil disimpan.');
    }
}
