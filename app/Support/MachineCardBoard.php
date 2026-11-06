<?php

namespace App\Support;

use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\MachineRunDay;
use App\Models\OperatorReport;
use App\Models\PreventiveSchedule;
use Illuminate\Support\Collection;

class MachineCardBoard
{
    /**
     * @return Collection<int, MachineName>
     */
    public static function machines(): Collection
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
    public static function stats(array $machineNumbers): array
    {
        $opRows = OperatorReport::query()
            ->where('status', '!=', RepairStatus::CLOSE)
            ->selectRaw('machine_number, status, COUNT(*) as total')
            ->groupBy('machine_number', 'status')
            ->get();

        $opTotals = [];
        $opOpen = [];
        $opProgress = [];
        $opApproval = [];
        foreach ($opRows as $row) {
            $number = (int) $row->machine_number;
            $total = (int) $row->total;
            $opTotals[$number] = ($opTotals[$number] ?? 0) + $total;
            if ($row->status === RepairStatus::OPEN) {
                $opOpen[$number] = ($opOpen[$number] ?? 0) + $total;
            } elseif ($row->status === RepairStatus::ON_PROGRESS) {
                $opProgress[$number] = ($opProgress[$number] ?? 0) + $total;
            } elseif (in_array($row->status, RepairStatus::pendingMaintenanceCloseValues(), true)) {
                $opApproval[$number] = ($opApproval[$number] ?? 0) + $total;
            }
        }

        $findingCounts = FindingRecap::query()
            ->where('status', '!=', FindingRecapStatus::CLOSE)
            ->selectRaw('machine_number, COUNT(*) as total')
            ->groupBy('machine_number')
            ->pluck('total', 'machine_number');

        $pmDueDates = self::nearestPmDueDates($machineNumbers);
        $showApproval = (bool) auth()->user()?->seesMachineApprovalColor();
        $stats = [];
        foreach ($machineNumbers as $number) {
            $number = (int) $number;
            $findings = (int) ($findingCounts[$number] ?? 0);
            $stats[$number] = [
                'op' => (int) ($opTotals[$number] ?? 0),
                'findings' => $findings,
                'pm_due' => $pmDueDates[$number] ?? null,
                'alert' => RepairStatus::machineCardAlert(
                    (int) ($opOpen[$number] ?? 0),
                    (int) ($opProgress[$number] ?? 0),
                    (int) ($opApproval[$number] ?? 0),
                    $showApproval,
                ),
            ];
        }

        return $stats;
    }

    /**
     * @param  list<int>  $machineNumbers
     * @return array<int, list<string>>
     */
    public static function preventiveToday(array $machineNumbers): array
    {
        $map = [];
        foreach ($machineNumbers as $number) {
            $map[(int) $number] = [];
        }

        if ($machineNumbers === []) {
            return $map;
        }

        $closedIds = PreventiveSchedule::closedIds(PreventiveStatus::CLOSE);
        $rows = PreventiveSchedule::query()
            ->whereIn('machine_number', $machineNumbers)
            ->when($closedIds !== [], fn ($query) => $query->whereNotIn('id', $closedIds))
            ->orderBy('plan_date')
            ->orderBy('id')
            ->get(['machine_number', 'preventive_type']);

        foreach ($rows as $row) {
            $number = (int) $row->machine_number;
            if (! array_key_exists($number, $map)) {
                continue;
            }
            $category = PreventiveCategory::label($row->preventive_type);
            if ($category === '-' || $category === '') {
                continue;
            }
            if (! in_array($category, $map[$number], true)) {
                $map[$number][] = $category;
            }
        }

        return $map;
    }

    /**
     * @return array{
     *     machines: Collection<int, MachineName>,
     *     machineStats: array<int, array{op: int, findings: int, pm_due: string|null, alert: string}>,
     *     preventiveToday: array<int, list<string>>,
     *     runningMachines: list<int>,
     *     breakdownMachines: list<int>,
     *     standbyMachines: list<int>,
     *     machineTotal: int
     * }
     */
    public static function payload(): array
    {
        $machines = self::machines();
        $numbers = $machines->pluck('machine_number')->map(fn ($number) => (int) $number)->all();

        return [
            'machines' => $machines,
            'machineStats' => self::stats($numbers),
            'preventiveToday' => self::preventiveToday($numbers),
            'runningMachines' => MachineRunDay::runningMachineNumbers(),
            'breakdownMachines' => MachineRunDay::breakdownMachineNumbers(),
            'standbyMachines' => MachineRunDay::standbyMachineNumbers(),
            'machineTotal' => count($numbers),
        ];
    }

    /**
     * @param  list<int>  $machineNumbers
     * @return array<int, string>
     */
    private static function nearestPmDueDates(array $machineNumbers): array
    {
        if ($machineNumbers === []) {
            return [];
        }

        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $open = PreventiveSchedule::query()
            ->open()
            ->whereIn('machine_number', $machineNumbers)
            ->get(['machine_number', 'plan_date']);

        $dates = [];
        foreach ($machineNumbers as $number) {
            $forMachine = $open->filter(
                fn (PreventiveSchedule $schedule): bool => (int) $schedule->machine_number === (int) $number
            );

            if ($forMachine->isEmpty()) {
                continue;
            }

            $upcoming = $forMachine
                ->filter(fn (PreventiveSchedule $schedule): bool => $schedule->plan_date->toDateString() >= $today)
                ->sortBy(fn (PreventiveSchedule $schedule): string => $schedule->plan_date->toDateString())
                ->first();

            if ($upcoming) {
                $dates[(int) $number] = $upcoming->plan_date->format('d/m/Y');
                continue;
            }

            $overdue = $forMachine
                ->filter(fn (PreventiveSchedule $schedule): bool => $schedule->plan_date->toDateString() < $today)
                ->sortByDesc(fn (PreventiveSchedule $schedule): string => $schedule->plan_date->toDateString())
                ->first();

            if ($overdue) {
                $dates[(int) $number] = $overdue->plan_date->format('d/m/Y');
            }
        }

        return $dates;
    }
}
