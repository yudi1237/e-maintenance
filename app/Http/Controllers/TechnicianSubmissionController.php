<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceReportStep2Request;
use App\Http\Requests\StorePreventiveConveyorReportRequest;
use App\Http\Requests\StorePreventiveReportRequest;
use App\Models\Equipment;
use App\Models\MaintenanceReport;
use App\Models\PreventiveConveyorReport;
use App\Models\PreventiveReport;
use App\Models\Robot;
use App\Models\Technician;
use App\Support\EquipmentCategory;
use App\Support\PreventiveChecklist;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveEvidence;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventiveStatus;
use App\Support\RepairStatus;
use App\Support\SparepartUsage;
use App\Support\TechnicianRecentWork;
use App\Support\WorkShift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TechnicianSubmissionController extends Controller
{
    public function showOp(MaintenanceReport $report): View
    {
        $this->guard($report, 'step2_completed_at');
        $report->load(['machineName', 'operatorReport']);

        return view('technician.submissions.op', compact('report'));
    }

    public function editOp(MaintenanceReport $report): View
    {
        $this->guardEditableOp($report);
        $report->load(['machineName', 'damageCategory', 'operatorReport.claimedTechnician']);

        return view('reports.step2', [
            'report' => $report,
            'technicians' => Technician::query()->active()->orderBy('name')->get(),
            'editing' => true,
        ]);
    }

    public function updateOp(StoreMaintenanceReportStep2Request $request, MaintenanceReport $report): RedirectResponse
    {
        $this->guardEditableOp($report);

        $validated = $request->validated();
        $ticket = $report->operatorReport;
        $ticket?->loadMissing('claimedTechnician');
        $pic = $ticket?->claimedTechnician;
        if ($pic && ! $pic->pinMatches((string) ($validated['pin'] ?? ''))) {
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

        $report->update([
            'masalah' => $validated['masalah'],
            'analisa_penyebab_kerusakan' => $validated['analisa_penyebab_kerusakan'],
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
            'penggunaan_sparepart' => $sparepart['text'],
            'sparepart_items' => $sparepart['items'],
            'yang_mengerjakan' => $this->workers($validated),
            'evidence_path' => $evidencePath,
        ]);

        return redirect()
            ->route('home.submissions.op', $report)
            ->with('success', 'Pengisian OP berhasil diperbarui.');
    }

    public function showPreventive(PreventiveReport $preventiveReport): View
    {
        $this->guard($preventiveReport, 'created_at');
        $preventiveReport->load(['machineName', 'equipment', 'robot']);

        return view('technician.submissions.preventive', ['report' => $preventiveReport]);
    }

    public function editPreventive(PreventiveReport $preventiveReport): View
    {
        $this->guardEditablePreventive($preventiveReport);
        $preventiveReport->load('machineName');
        $machine = $preventiveReport->machineName;
        abort_unless($machine, 404);
        $category = (string) $preventiveReport->category;

        return view('preventive.form', [
            'machine' => $machine,
            'category' => $category,
            'checklistGroups' => PreventiveMachineChecklist::groups($category),
            'currentDateTime' => $preventiveReport->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'picName' => $preventiveReport->pic_name,
            'detectedShift' => $preventiveReport->work_shift,
            'shiftLabels' => WorkShift::labels(),
            'technicians' => Technician::query()->active()->orderBy('name')->get(),
            'robots' => $category === 'ROBOT'
                ? Robot::query()->active()->orderBy('robot_number')->get()
                : collect(),
            'equipments' => EquipmentCategory::usesMasterDropdown($category)
                ? Equipment::query()
                    ->active()
                    ->forCategory((string) EquipmentCategory::fromPreventive($category))
                    ->orderBy('number')
                    ->get()
                : collect(),
            'recentReports' => collect(),
            'fromSchedule' => false,
            'editingReport' => $preventiveReport,
        ]);
    }

    public function updatePreventive(StorePreventiveReportRequest $request, PreventiveReport $preventiveReport): RedirectResponse
    {
        $this->guardEditablePreventive($preventiveReport);

        $validated = $request->validated();
        $category = (string) $preventiveReport->category;
        $items = $validated['items'];
        $readings = PreventiveMachineChecklist::sanitizeReadings($category, $validated['readings'] ?? []);
        $remarks = PreventiveChecklist::sanitizeRemarks(
            PreventiveMachineChecklist::allItemKeys($category),
            $validated['remarks'] ?? []
        );
        $robot = $category === 'ROBOT'
            ? Robot::query()->active()->findOrFail($validated['robot_id'])
            : null;
        $equipment = EquipmentCategory::usesMasterDropdown($category)
            ? Equipment::query()
                ->active()
                ->forCategory((string) EquipmentCategory::fromPreventive($category))
                ->findOrFail($validated['equipment_id'])
            : null;
        $sparepart = SparepartUsage::persisted($validated);
        $evidence = $this->mergeEvidence(
            $preventiveReport->evidence_paths ?? [],
            PreventiveEvidence::storeFromRequest($request)
        );

        $preventiveReport->update([
            'equipment_id' => $equipment?->id,
            'equipment_number' => $robot?->robot_number ?? $equipment?->number ?? ($validated['equipment_number'] ?? $preventiveReport->equipment_number),
            'equipment_type' => $equipment?->type ?? $preventiveReport->equipment_type,
            'robot_id' => $robot?->id,
            'robot_number' => $robot?->robot_number,
            'robot_type' => $robot?->robot_type,
            'work_shift' => $validated['work_shift'],
            'team_members' => ($team = Technician::teamSnapshot($validated['technician_ids'] ?? [])) === [] ? null : $team,
            'items' => $items,
            'readings' => $readings === [] ? null : $readings,
            'item_remarks' => $remarks === [] ? null : $remarks,
            'notes' => $validated['notes'] ?? null,
            'penggunaan_sparepart' => $sparepart['text'],
            'sparepart_items' => $sparepart['items'],
            'evidence_paths' => $evidence === [] ? null : $evidence,
            'result' => PreventiveMachineChecklist::result($category, $items),
        ]);

        return redirect()
            ->route('home.submissions.preventive', $preventiveReport)
            ->with('success', 'Pengisian preventive berhasil diperbarui.');
    }

    public function showConveyor(PreventiveConveyorReport $preventiveConveyorReport): View
    {
        $this->guard($preventiveConveyorReport, 'created_at');

        return view('technician.submissions.conveyor', ['report' => $preventiveConveyorReport]);
    }

    public function editConveyor(PreventiveConveyorReport $preventiveConveyorReport): View
    {
        $this->guard($preventiveConveyorReport, 'created_at');

        return view('preventive.conveyor-create', [
            'checklistGroups' => PreventiveConveyorChecklist::groups(),
            'checklistValues' => PreventiveConveyorChecklist::valueLabels(),
            'currentDateTime' => $preventiveConveyorReport->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'picName' => $preventiveConveyorReport->pic_name,
            'detectedShift' => $preventiveConveyorReport->work_shift,
            'shiftLabels' => WorkShift::labels(),
            'technicians' => Technician::query()->active()->orderBy('name')->get(),
            'recentReports' => collect(),
            'editingReport' => $preventiveConveyorReport,
        ]);
    }

    public function updateConveyor(StorePreventiveConveyorReportRequest $request, PreventiveConveyorReport $preventiveConveyorReport): RedirectResponse
    {
        $this->guard($preventiveConveyorReport, 'created_at');

        $validated = $request->validated();
        $items = $validated['items'];
        $remarks = PreventiveChecklist::sanitizeRemarks(
            array_keys(PreventiveConveyorChecklist::items()),
            $validated['remarks'] ?? []
        );
        $sparepart = SparepartUsage::persisted($validated);
        $evidence = $this->mergeEvidence(
            $preventiveConveyorReport->evidence_paths ?? [],
            PreventiveEvidence::storeFromRequest($request, PreventiveEvidence::CONVEYOR_DIRECTORY)
        );

        $preventiveConveyorReport->update([
            'conveyor_name' => $validated['conveyor_name'],
            'work_shift' => $validated['work_shift'],
            'team_members' => ($team = Technician::teamSnapshot($validated['technician_ids'] ?? [])) === [] ? null : $team,
            'items' => $items,
            'item_remarks' => $remarks === [] ? null : $remarks,
            'notes' => $validated['notes'] ?? null,
            'penggunaan_sparepart' => $sparepart['text'],
            'sparepart_items' => $sparepart['items'],
            'evidence_paths' => $evidence === [] ? null : $evidence,
            'result' => PreventiveConveyorChecklist::result($items),
        ]);

        return redirect()
            ->route('home.submissions.conveyor', $preventiveConveyorReport)
            ->with('success', 'Pengisian preventive conveyor berhasil diperbarui.');
    }

    private function guard(Model $model, string $column): void
    {
        abort_unless(TechnicianRecentWork::visible($model, (int) auth()->id(), $column), 404);
    }

    private function guardEditableOp(MaintenanceReport $report): void
    {
        $this->guard($report, 'step2_completed_at');
        abort_unless(
            $report->status_perbaikan === RepairStatus::CLOSE_BY_TECHNICIAN,
            403,
            'Pengisian ini sudah diproses lebih lanjut dan tidak bisa diedit.'
        );
    }

    private function guardEditablePreventive(PreventiveReport $report): void
    {
        $this->guard($report, 'created_at');
        abort_unless(
            ($report->status ?: PreventiveStatus::CLOSE_BY_TECHNICIAN) === PreventiveStatus::CLOSE_BY_TECHNICIAN,
            403,
            'Pengisian ini sudah diproses lebih lanjut dan tidak bisa diedit.'
        );
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function workers(array $validated): array
    {
        $workers = ['mode' => $validated['worker_mode'], 'names' => []];
        if ($validated['worker_mode'] === 'self') {
            $workers['names'] = [auth()->user()->name];

            return $workers;
        }

        $team = Technician::query()
            ->active()
            ->whereIn('id', $validated['technician_ids'] ?? [])
            ->orderBy('name')
            ->get();
        $workers['technician_ids'] = $team->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $workers['names'] = $team->map(fn (Technician $technician) => $technician->optionLabel())->all();

        return $workers;
    }

    /**
     * @param  list<string>|null  $existing
     * @param  list<string>  $added
     * @return list<string>
     */
    private function mergeEvidence(?array $existing, array $added): array
    {
        $paths = collect($existing ?? [])
            ->merge($added)
            ->filter(fn ($path): bool => is_string($path) && $path !== '')
            ->values();

        if ($paths->count() > PreventiveEvidence::MAX_FILES) {
            Storage::disk('public')->delete($added);

            throw ValidationException::withMessages([
                'evidences' => 'Evidence maksimal '.PreventiveEvidence::MAX_FILES.' foto.',
            ]);
        }

        return $paths->all();
    }
}
