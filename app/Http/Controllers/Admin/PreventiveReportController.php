<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PreventiveReportXlsxExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePreventiveReportStatusRequest;
use App\Models\MachineName;
use App\Models\PreventiveChecklistItem;
use App\Models\PreventiveReport;
use App\Models\PreventiveSchedule;
use App\Support\PreventiveCategory;
use App\Support\PreventiveStatus;
use App\Support\TablePagination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreventiveReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'machine_number' => ['nullable', 'integer', 'min:1'],
            'schedule_month' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
            'schedule_category' => ['nullable', 'string', Rule::in(array_keys(PreventiveCategory::scheduleGroupLabels()))],
        ]);

        $reportsQuery = PreventiveReport::query()->with('machineName');

        if (! empty($filters['start_date'])) {
            $reportsQuery->whereDate('work_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $reportsQuery->whereDate('work_date', '<=', $filters['end_date']);
        }

        if (! empty($filters['machine_number'])) {
            $reportsQuery->where('machine_number', (int) $filters['machine_number']);
        }

        $orderSql = PreventiveStatus::dashboardOrderSql($request->user());
        if ($orderSql !== null) {
            $reportsQuery->orderByRaw($orderSql);
        }

        $reports = $reportsQuery
            ->latestFirst()
            ->paginate(TablePagination::perPage())
            ->withQueryString();

        $machines = MachineName::query()
            ->whereNotNull('machine_number')
            ->where('is_active', true)
            ->orderBy('machine_number')
            ->get(['id', 'machine_number', 'name', 'brand', 'tonnage', 'machine_type']);

        $oldMachine = old('machine_name_id')
            ? $machines->firstWhere('id', (int) old('machine_name_id'))
            : null;

        $availableTypes = PreventiveChecklistItem::scheduleTypeLabels();
        $scheduleTypesByGroup = [];
        foreach (PreventiveCategory::scheduleGroupLabels() as $group => $label) {
            $scheduleTypesByGroup[$group] = array_intersect_key(
                PreventiveCategory::typesForScheduleGroup($group),
                $availableTypes
            );
        }

        $selectedScheduleGroup = old('schedule_group');
        if (! $selectedScheduleGroup && old('preventive_type')) {
            $selectedScheduleGroup = PreventiveCategory::scheduleGroupKey((string) old('preventive_type'));
        }

        return view('admin.preventive.index', [
            'reports' => $reports,
            'reportCount' => PreventiveReport::query()->count(),
            'filteredReportCount' => $reports->total(),
            'startDate' => $filters['start_date'] ?? null,
            'endDate' => $filters['end_date'] ?? null,
            'machineNumber' => $filters['machine_number'] ?? null,
            'machines' => $machines,
            'scheduleGroups' => PreventiveCategory::scheduleGroupLabels(),
            'scheduleTypesByGroup' => $scheduleTypesByGroup,
            'selectedScheduleGroup' => $selectedScheduleGroup,
            'scheduleMonth' => $filters['schedule_month'] ?? null,
            'scheduleCategory' => $filters['schedule_category'] ?? null,
            'schedules' => PreventiveSchedule::query()
                ->with(['machineName', 'user'])
                ->open()
                ->forPlanMonth($filters['schedule_month'] ?? null)
                ->forScheduleGroup($filters['schedule_category'] ?? null)
                ->priorityByPlanDate()
                ->paginate(TablePagination::perPage(), ['*'], 'schedules')
                ->withQueryString(),
            'oldMachineName' => $oldMachine?->view_label ?? '',
            'oldMachineType' => $oldMachine?->machine_type ?? '',
        ]);
    }

    public function show(PreventiveReport $preventiveReport): View
    {
        abort_unless(auth()->user()?->seesPreventiveResults(), 403);

        $preventiveReport->load('machineName');

        return view('admin.preventive.show', [
            'report' => $preventiveReport,
        ]);
    }

    public function updateStatus(UpdatePreventiveReportStatusRequest $request, PreventiveReport $preventiveReport): RedirectResponse
    {
        $status = $request->validated('status');
        $preventiveReport->update(['status' => $status]);

        return back()->with(
            'success',
            'Hasil preventive Mesin '.$preventiveReport->machine_number.' diperbarui menjadi '.$preventiveReport->status_label.'.'
        );
    }

    public function export(PreventiveReport $preventiveReport): StreamedResponse
    {
        $preventiveReport->load(['machineName', 'equipment', 'robot']);
        $export = new PreventiveReportXlsxExport;
        $filename = $export->filename($preventiveReport);

        return response()->streamDownload(function () use ($export, $preventiveReport) {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
            if ($tmp === false) {
                throw new \RuntimeException('Tidak dapat membuat file sementara untuk export Excel.');
            }

            $path = $tmp.'.xlsx';
            if (! rename($tmp, $path)) {
                @unlink($tmp);
                throw new \RuntimeException('Tidak dapat menyiapkan file export Excel.');
            }

            try {
                $export->buildExporter($preventiveReport)->writeToFile($path);
                readfile($path);
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
