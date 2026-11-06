<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RequiresPicSession;
use App\Http\Requests\AuthenticatePreventivePicRequest;
use App\Http\Requests\StorePreventiveReportRequest;
use App\Models\Equipment;
use App\Models\MachineName;
use App\Models\PreventiveReport;
use App\Models\PreventiveSchedule;
use App\Models\Robot;
use App\Models\Technician;
use App\Models\TechnicianPreventiveJob;
use App\Support\EquipmentCategory;
use App\Support\PicAuthenticator;
use App\Support\PicSession;
use App\Support\PreventiveCategory;
use App\Support\PreventiveChecklist;
use App\Support\PreventiveEvidence;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventiveStatus;
use App\Support\SparepartUsage;
use App\Support\TablePagination;
use App\Support\TechnicianShift;
use App\Support\WorkShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class PreventiveReportController extends Controller
{
    use RequiresPicSession;

    public function identifyPic(AuthenticatePreventivePicRequest $request): RedirectResponse
    {
        $pic = PicAuthenticator::fromIdAndPin(
            $request->integer('technician_id'),
            (string) $request->input('pin')
        );
        TechnicianShift::assertCanTake($pic);

        PicSession::set($pic);

        $destination = (string) $request->input('destination');

        if ($destination === 'menu') {
            return redirect()->route('preventive.menu');
        }

        if ($destination === 'conveyor') {
            return redirect()->route('preventive-conveyor.create');
        }

        if (PreventiveCategory::isUtility($destination)) {
            return redirect()->route('preventive.create', ['category' => $destination]);
        }

        return redirect()->route('preventive.create');
    }

    public function menu(): View
    {
        $pic = $this->requirePic();
        $schedules = $this->scheduleBoard();

        return view('preventive.menu', [
            'picName' => $pic->name,
            'currentDateTime' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'schedules' => $schedules,
            'schedulesSignature' => $this->scheduleSignature($schedules),
        ]);
    }

    public function schedules(): JsonResponse
    {
        $this->requirePic();
        $schedules = $this->scheduleBoard();

        return response()->json([
            'html' => view('partials.preventive-schedules', [
                'schedules' => $schedules,
            ])->render(),
            'signature' => $this->scheduleSignature($schedules),
            'updated_at' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
        ]);
    }

    public function create(Request $request): View
    {
        $pic = $this->requirePic();
        $lockedCategory = $request->query('category');

        if (filled($lockedCategory) && ! PreventiveCategory::isUtility($lockedCategory)) {
            abort(404);
        }

        return view('preventive.machines', [
            'machineNames' => MachineName::active()
                ->whereNotNull('machine_number')
                ->orderBy('machine_number')
                ->orderBy('name')
                ->get(),
            'currentDateTime' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'picName' => $pic->name,
            'lockedCategory' => $lockedCategory,
        ]);
    }

    public function categories(int $machineNumber): View
    {
        $pic = $this->requirePic();
        $machine = $this->findActiveMachine($machineNumber);

        return view('preventive.categories', [
            'machine' => $machine,
            'categories' => PreventiveCategory::machineLabels(),
            'currentDateTime' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'picName' => $pic->name,
        ]);
    }

    public function form(Request $request, int $machineNumber, string $category): View
    {
        abort_unless(PreventiveCategory::isValid($category), 404);

        $pic = $this->requirePic();
        TechnicianShift::ensureCanTake($pic);
        $machine = $this->findActiveMachine($machineNumber);
        TechnicianPreventiveJob::open($pic, (int) $machine->machine_number, $category);

        return view('preventive.form', [
            'machine' => $machine,
            'category' => $category,
            'checklistGroups' => PreventiveMachineChecklist::groups($category),
            'currentDateTime' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'picName' => $pic->name,
            'detectedShift' => WorkShift::detect(),
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
            'recentReports' => PreventiveReport::query()
                ->with('machineName')
                ->where('user_id', auth()->id())
                ->where('machine_name_id', $machine->id)
                ->where('category', $category)
                ->latest()
                ->limit(8)
                ->get(),
            'fromSchedule' => $request->query('from') === 'schedule' || old('from') === 'schedule',
        ]);
    }

    public function store(StorePreventiveReportRequest $request): RedirectResponse
    {
        $pic = $this->requirePic();
        $validated = $request->validated();
        $machine = MachineName::findOrFail($validated['machine_name_id']);
        $items = $validated['items'];
        $category = $validated['category'];
        $readings = PreventiveMachineChecklist::sanitizeReadings(
            $category,
            $validated['readings'] ?? []
        );
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

        $equipmentNumber = $robot?->robot_number
            ?? $equipment?->number
            ?? ($validated['equipment_number'] ?? null);
        $sparepart = SparepartUsage::persisted($validated);

        PreventiveReport::create([
            'user_id' => auth()->id(),
            'pic_name' => $pic->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'equipment_id' => $equipment?->id,
            'equipment_number' => $equipmentNumber,
            'equipment_type' => $equipment?->type,
            'robot_id' => $robot?->id,
            'robot_number' => $robot?->robot_number,
            'robot_type' => $robot?->robot_type,
            'category' => $category,
            'work_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'work_shift' => $validated['work_shift'],
            'team_members' => ($team = Technician::teamSnapshot($validated['technician_ids'] ?? [])) === [] ? null : $team,
            'items' => $items,
            'readings' => $readings === [] ? null : $readings,
            'item_remarks' => $remarks === [] ? null : $remarks,
            'notes' => $validated['notes'] ?? null,
            'penggunaan_sparepart' => $sparepart['text'],
            'sparepart_items' => $sparepart['items'],
            'evidence_paths' => PreventiveEvidence::storeFromRequest($request) ?: null,
            'result' => PreventiveMachineChecklist::result($category, $items),
            'status' => PreventiveStatus::CLOSE_BY_TECHNICIAN,
        ]);

        TechnicianPreventiveJob::close($pic, (int) $machine->machine_number, $category);

        $redirect = $request->input('from') === 'schedule'
            ? redirect()->route('preventive.menu')
            : (PreventiveCategory::isUtility($category)
                ? redirect()->route('preventive.create', ['category' => $category])
                : redirect()->route('preventive.categories', $machine->machine_number));

        return $redirect->with('success', PreventiveCategory::formTitle($category).' berhasil disimpan.');
    }

    private function scheduleBoard(): LengthAwarePaginator
    {
        return PreventiveSchedule::query()
            ->with('machineName')
            ->open()
            ->priorityByPlanDate()
            ->paginate(TablePagination::perPage(), ['*'], 'jadwal')
            ->withQueryString();
    }

    private function scheduleSignature(LengthAwarePaginator $schedules): string
    {
        $payload = $schedules->getCollection()
            ->map(fn (PreventiveSchedule $schedule) => implode('|', [
                $schedule->id,
                $schedule->machine_number,
                $schedule->preventive_type,
                $schedule->plan_date?->toDateString(),
                $schedule->updated_at,
            ]))
            ->implode(';');

        return hash('sha256', $payload.'|'.$schedules->total().'|'.$schedules->currentPage());
    }

    private function findActiveMachine(int $machineNumber): MachineName
    {
        return MachineName::query()
            ->operational()
            ->active()
            ->whereNotNull('machine_number')
            ->where('machine_number', $machineNumber)
            ->firstOrFail();
    }
}
