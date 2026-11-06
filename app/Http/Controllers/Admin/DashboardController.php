<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayoutClusterName;
use App\Models\MachineName;
use App\Models\MachineRunDay;
use App\Models\MaintenanceReport;
use App\Models\Mold;
use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Models\Technician;
use App\Support\DashboardAnalytics;
use App\Support\InjectionMachine;
use App\Support\LayoutCluster;
use App\Support\MachineCardBoard;
use App\Support\RepairStatus;
use App\Support\TablePagination;
use App\Support\TechnicianDuty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = $this->dashboardSummary();
        $machines = $this->masterMachines();
        $machineNumbers = $machines->pluck('machine_number')->all();
        $now = now()->timezone('Asia/Jakarta');
        [$pmYear, $pmMonth] = DashboardAnalytics::resolvePmPeriod($now->year, $now->month, $now);

        return view('admin.dashboard', [
            'machines' => $machines,
            'machineNumbers' => $machineNumbers,
            'machineStats' => $this->machineStats($machineNumbers),
            'machineTotal' => count($machineNumbers),
            'totalReportsToday' => $summary['total_reports_today'],
            'activeMachinesCount' => $summary['active_machines'],
            'closedReportsToday' => $summary['closed_reports_today'],
            'todayLabel' => $summary['today_label'],
            'approvalRoleCaption' => $summary['approval_role_caption'],
            'charts' => DashboardAnalytics::charts(),
            'pmStatus' => DashboardAnalytics::pmStatus($pmYear, $pmMonth),
            'pmYears' => DashboardAnalytics::pmYearOptions($now),
            'pmMonths' => DashboardAnalytics::monthNames(),
            'pmYear' => $pmYear,
            'pmMonth' => $pmMonth,
            'preventiveToday' => $this->preventiveTodayByMachine($machineNumbers),
            'runningMachines' => MachineRunDay::runningMachineNumbers($now),
            'breakdownMachines' => MachineRunDay::breakdownMachineNumbers($now),
            'standbyMachines' => MachineRunDay::standbyMachineNumbers($now),
            'clusterNames' => LayoutCluster::names(),
            'technicianStatuses' => TechnicianDuty::snapshot(),
        ]);
    }

    public function updateRunning(Request $request): JsonResponse
    {
        abort_unless($request->user()?->canToggleMachineRunning(), 403);

        $validated = $request->validate([
            'machine_number' => ['required', 'integer', Rule::exists('machine_names', 'machine_number')],
            'status' => ['nullable', 'required_without:running', 'in:running,breakdown,standby,off'],
            'running' => ['nullable', 'required_without:status', 'in:0,1'],
        ]);

        $machineNumber = (int) $validated['machine_number'];
        $status = $validated['status'] ?? ((($validated['running'] ?? '0') === '1')
            ? MachineRunDay::STATUS_RUNNING
            : 'off');
        $date = MachineRunDay::todayDate();

        if ($status === 'off') {
            MachineRunDay::query()
                ->where('machine_number', $machineNumber)
                ->whereDate('run_date', $date)
                ->delete();
            $status = null;
        } else {
            $record = MachineRunDay::query()
                ->where('machine_number', $machineNumber)
                ->whereDate('run_date', $date)
                ->first();

            if ($record) {
                $record->update([
                    'status' => $status,
                    'user_id' => $request->user()->id,
                ]);
            } else {
                MachineRunDay::query()->create([
                    'machine_number' => $machineNumber,
                    'run_date' => $date,
                    'status' => $status,
                    'user_id' => $request->user()->id,
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'machine_number' => $machineNumber,
            'status' => $status,
            'running' => $status === MachineRunDay::STATUS_RUNNING,
        ]);
    }

    public function updateTechnicianShifts(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'shifts' => ['nullable', 'array'],
            'shifts.*' => ['nullable', 'in:1,2,3,off,overtime'],
        ]);

        $shifts = $validated['shifts'] ?? [];

        foreach (Technician::query()->active()->get() as $technician) {
            $value = $shifts[$technician->id] ?? null;

            if ($value === 'off') {
                $technician->update(['off_duty' => true, 'overtime' => false]);

                continue;
            }

            if ($value === 'overtime') {
                $technician->update(['overtime' => true, 'off_duty' => false]);

                continue;
            }

            $technician->update([
                'shift' => ($value === null || $value === '') ? null : (int) $value,
                'off_duty' => false,
                'overtime' => false,
            ]);
        }

        return redirect()
            ->route('admin.technicians.index')
            ->with('success', 'Mapping shift teknisi sudah disimpan.');
    }

    public function updateClusterName(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'key' => ['required', 'string', Rule::in(array_keys(LayoutCluster::DEFAULTS))],
            'name' => ['required', 'string', 'max:80'],
        ]);

        $name = trim((string) preg_replace('/\s+/u', ' ', $validated['name']));
        if ($name === '') {
            return back()->withErrors(['name' => 'Nama cluster wajib diisi.'])->withInput();
        }

        LayoutClusterName::query()->updateOrCreate(
            ['key' => $validated['key']],
            ['name' => $name],
        );

        return back()->with('success', 'Nama cluster sudah disimpan.');
    }

    public function machine(Request $request, int $machineNumber): View
    {
        abort_unless(InjectionMachine::isValid($machineNumber), 404);

        $machine = MachineName::query()
            ->with('mold')
            ->operational()
            ->where('machine_number', $machineNumber)
            ->first();

        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $reportsQuery = MaintenanceReport::with(['user', 'machineName', 'damageCategory', 'operatorReport'])
            ->where('machine_number', $machineNumber);

        if (! empty($filters['start_date'])) {
            $reportsQuery->whereDate('report_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $reportsQuery->whereDate('report_date', '<=', $filters['end_date']);
        }

        $reports = $reportsQuery
            ->latest()
            ->paginate(TablePagination::perPage())
            ->withQueryString();

        $operatorTicketsQuery = OperatorReport::query()
            ->with(['claimedTechnician', 'claimedBy'])
            ->where('machine_number', $machineNumber)
            ->whereDoesntHave('maintenanceReport');

        if (! empty($filters['start_date'])) {
            $operatorTicketsQuery->whereDate('stop_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $operatorTicketsQuery->whereDate('stop_date', '<=', $filters['end_date']);
        }

        $operatorTickets = $operatorTicketsQuery
            ->orderByDesc('stop_date')
            ->orderByDesc('id')
            ->get();

        $preventiveQuery = PreventiveReport::query()
            ->with('machineName')
            ->where('machine_number', $machineNumber);

        if (! empty($filters['start_date'])) {
            $preventiveQuery->whereDate('work_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $preventiveQuery->whereDate('work_date', '<=', $filters['end_date']);
        }

        $preventiveReports = $preventiveQuery
            ->latest('work_date')
            ->latest('id')
            ->paginate(TablePagination::perPage(), ['*'], 'preventive_page')
            ->withQueryString();

        return view('admin.machine-detail', [
            'machineNumber' => $machineNumber,
            'machine' => $machine,
            'molds' => Mold::query()->active()->orderBy('name')->orderBy('id')->get(),
            'reports' => $reports,
            'operatorTickets' => $operatorTickets,
            'reportCount' => MaintenanceReport::where('machine_number', $machineNumber)->count()
                + OperatorReport::query()
                    ->where('machine_number', $machineNumber)
                    ->whereDoesntHave('maintenanceReport')
                    ->count(),
            'filteredReportCount' => $reports->total() + $operatorTickets->count(),
            'preventiveReports' => $preventiveReports,
            'preventiveCount' => PreventiveReport::where('machine_number', $machineNumber)->count(),
            'filteredPreventiveCount' => $preventiveReports->total(),
            'startDate' => $filters['start_date'] ?? null,
            'endDate' => $filters['end_date'] ?? null,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function operatorRunning(): JsonResponse
    {
        abort_unless(auth()->user()?->isOperator(), 403);

        $machineNumbers = $this->masterMachines()->pluck('machine_number')->map(fn ($number) => (int) $number)->all();

        return response()->json([
            'running' => MachineRunDay::runningMachineNumbers(),
            'breakdown' => MachineRunDay::breakdownMachineNumbers(),
            'standby' => MachineRunDay::standbyMachineNumbers(),
            'machines' => $this->machineStats($machineNumbers),
            'updated_at' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $summary = $this->dashboardSummary();
        $machineNumbers = $this->masterMachines()->pluck('machine_number')->all();
        $now = now()->timezone('Asia/Jakarta');
        [$pmYear, $pmMonth] = DashboardAnalytics::resolvePmPeriod(
            $request->query('pm_year', $now->year),
            $request->has('pm_month') ? $request->query('pm_month') : $now->month,
            $now,
        );

        return response()->json([
            'machines' => $this->machineStats($machineNumbers),
            'preventive_today' => $this->preventiveTodayByMachine($machineNumbers),
            'total' => $summary['total_reports_today'],
            'active_machines' => $summary['active_machines'],
            'closed' => $summary['closed_reports_today'],
            'today_label' => $summary['today_label'],
            'updated_at' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
            'charts' => DashboardAnalytics::charts(),
            'pm_status' => DashboardAnalytics::pmStatus($pmYear, $pmMonth),
            'running' => MachineRunDay::runningMachineNumbers(),
            'breakdown' => MachineRunDay::breakdownMachineNumbers(),
            'standby' => MachineRunDay::standbyMachineNumbers(),
            'technicians' => TechnicianDuty::snapshot(),
        ]);
    }

    /**
     * @return array{
     *     total_reports_today: int,
     *     active_machines: int,
     *     closed_reports_today: int,
     *     today_label: string,
     *     approval_role_caption: string
     * }
     */
    private function dashboardSummary(): array
    {
        $today = now()->timezone('Asia/Jakarta');
        $todayDate = $today->toDateString();
        $user = auth()->user();
        $pendingStatus = RepairStatus::pendingApprovalStatusFor($user);

        $activeMachinesQuery = OperatorReport::query();
        if ($pendingStatus !== null) {
            $activeMachinesQuery->where('status', $pendingStatus);
        } else {
            $activeMachinesQuery->where('status', '!=', RepairStatus::CLOSE);
        }

        return [
            'total_reports_today' => OperatorReport::query()->whereDate('stop_date', $todayDate)->count()
                + MaintenanceReport::query()->today()->whereNull('operator_report_id')->count(),
            'active_machines' => (int) $activeMachinesQuery->distinct()->count('machine_number'),
            'closed_reports_today' => MaintenanceReport::query()
                ->today()
                ->whereIn('status_perbaikan', RepairStatus::approvedStatusesFor($user))
                ->count(),
            'today_label' => $today->format('d/m/Y'),
            'approval_role_caption' => RepairStatus::approvalRoleCaption($user),
        ];
    }

    public function updateMold(Request $request, int $machineNumber): RedirectResponse
    {
        abort_unless($request->user()?->isProduksiInjection(), 403);
        abort_unless(InjectionMachine::isValid($machineNumber), 404);

        $validated = $request->validate([
            'mold_id' => ['nullable', 'integer', Rule::exists('molds', 'id')->where('is_active', true)],
        ]);

        $machine = MachineName::query()->operational()->where('machine_number', $machineNumber)->firstOrFail();
        $machine->update(['mold_id' => $validated['mold_id'] ?? null]);

        return redirect()
            ->route('admin.machines.show', $machineNumber)
            ->with('success', 'Mold yang terpasang di mesin ini sudah diperbarui.');
    }

    private function masterMachines()
    {
        return MachineName::query()
            ->with('mold')
            ->operational()
            ->active()
            ->whereNotNull('machine_number')
            ->orderBy('machine_number')
            ->get();
    }

    /**
     * @param  list<int>  $machineNumbers
     * @return array<int, array{op: int, findings: int, pm_due: string|null, alert: string}>
     */
    private function machineStats(array $machineNumbers): array
    {
        return MachineCardBoard::stats($machineNumbers);
    }

    /**
     * @param  list<int>  $machineNumbers
     * @return array<int, list<string>>
     */
    private function preventiveTodayByMachine(array $machineNumbers): array
    {
        return MachineCardBoard::preventiveToday($machineNumbers);
    }
}
