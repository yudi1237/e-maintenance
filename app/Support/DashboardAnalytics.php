<?php

namespace App\Support;

use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Models\PreventiveSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardAnalytics
{
    /**
     * @return array{
     *     period_label: string,
     *     performer: array{labels: list<string>, on_progress: list<int>, closed_technician: list<int>, closed_admin: list<int>},
     *     technician_mttr: array{labels: list<string>, values: list<int>, avg_minutes: int|null},
     *     machines: array{labels: list<string>, values: list<int>}
     * }
     */
    public static function charts(?Carbon $now = null): array
    {
        $now = ($now ?? now())->timezone('Asia/Jakarta');
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        $tickets = OperatorReport::query()
            ->with(['claimedBy', 'claimedTechnician', 'maintenanceReport'])
            ->where(function ($query) use ($start, $end) {
                $query
                    ->whereBetween('stop_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('claimed_at', [$start, $end]);
            })
            ->get();

        return [
            'period_label' => self::monthLabel($now),
            'performer' => self::performerChart($tickets),
            'technician_mttr' => self::technicianMttrChart($tickets),
            'machines' => self::lineStopChart($tickets),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function monthNames(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    public static function monthLabel(Carbon $now): string
    {
        return (self::monthNames()[$now->month] ?? $now->format('F')).' '.$now->year;
    }

    /**
     * @return list<int>
     */
    public static function pmYearOptions(?Carbon $now = null): array
    {
        $now = ($now ?? now())->timezone('Asia/Jakarta');
        $years = [$now->year - 1, $now->year, $now->year + 1];

        foreach ([
            PreventiveSchedule::query()->min('plan_date'),
            PreventiveReport::query()->min('work_date'),
        ] as $date) {
            if (is_string($date) && preg_match('/^(\d{4})/', $date, $matches) === 1) {
                $years[] = (int) $matches[1];
            }
        }

        $years = array_values(array_unique(array_filter(
            $years,
            fn (int $year): bool => $year >= 2000 && $year <= 2100
        )));
        sort($years);

        return $years;
    }

    /**
     * @return array{0: int, 1: int|null}
     */
    public static function resolvePmPeriod(mixed $year, mixed $month, ?Carbon $now = null): array
    {
        $now = ($now ?? now())->timezone('Asia/Jakarta');
        $resolvedYear = (int) $year;
        if ($resolvedYear < 2000 || $resolvedYear > 2100) {
            $resolvedYear = $now->year;
        }

        if ($month === null || $month === '' || $month === 'all') {
            return [$resolvedYear, null];
        }

        $resolvedMonth = (int) $month;
        if ($resolvedMonth < 1 || $resolvedMonth > 12) {
            $resolvedMonth = $now->month;
        }

        return [$resolvedYear, $resolvedMonth];
    }

    /**
     * @return array{
     *     year: int,
     *     month: int|null,
     *     period_label: string,
     *     planned: int,
     *     pending: int,
     *     completed: int,
     *     remaining: int,
     *     unplanned: int,
     *     target: int,
     *     actual: int,
     *     percent: float,
     *     percent_label: string
     * }
     */
    public static function pmStatus(int $year, ?int $month = null): array
    {
        [$start, $end] = self::pmPeriodBounds($year, $month);
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $schedules = PreventiveSchedule::query()
            ->whereDate('plan_date', '<=', $endDate)
            ->orderBy('plan_date')
            ->orderBy('id')
            ->get(['id', 'machine_number', 'preventive_type', 'plan_date']);

        $reports = PreventiveReport::query()
            ->whereDate('work_date', '<=', $endDate)
            ->orderBy('work_date')
            ->orderBy('id')
            ->get(['id', 'machine_number', 'category', 'work_date']);

        $planned = $schedules->filter(
            fn (PreventiveSchedule $schedule): bool => $schedule->plan_date->toDateString() >= $startDate
        );
        $prior = $schedules->filter(
            fn (PreventiveSchedule $schedule): bool => $schedule->plan_date->toDateString() < $startDate
        );
        $priorReports = $reports->filter(
            fn (PreventiveReport $report): bool => $report->work_date->toDateString() < $startDate
        );
        $periodReports = $reports->filter(
            fn (PreventiveReport $report): bool => $report->work_date->toDateString() >= $startDate
        );

        $usedPriorReportIds = [];
        $pending = collect();
        foreach ($prior as $schedule) {
            $match = $priorReports->first(function (PreventiveReport $report) use ($schedule, $usedPriorReportIds): bool {
                return ! isset($usedPriorReportIds[$report->id])
                    && (int) $report->machine_number === (int) $schedule->machine_number
                    && $report->category === $schedule->preventive_type
                    && $report->work_date->toDateString() >= $schedule->plan_date->toDateString();
            });

            if ($match) {
                $usedPriorReportIds[$match->id] = true;
            } else {
                $pending->push($schedule);
            }
        }

        $usedPeriodReportIds = [];
        $completed = 0;
        $targetSchedules = $planned->concat($pending)->sortBy([
            fn (PreventiveSchedule $schedule) => $schedule->plan_date->toDateString(),
            fn (PreventiveSchedule $schedule) => $schedule->id,
        ]);

        foreach ($targetSchedules as $schedule) {
            $match = $periodReports->first(function (PreventiveReport $report) use ($schedule, $usedPeriodReportIds): bool {
                return ! isset($usedPeriodReportIds[$report->id])
                    && (int) $report->machine_number === (int) $schedule->machine_number
                    && $report->category === $schedule->preventive_type;
            });

            if ($match) {
                $usedPeriodReportIds[$match->id] = true;
                $completed++;
            }
        }

        $plannedCount = $planned->count();
        $pendingCount = $pending->count();
        $target = $plannedCount + $pendingCount;
        $remaining = max(0, $target - $completed);
        $unplanned = $periodReports->filter(
            fn (PreventiveReport $report): bool => ! isset($usedPeriodReportIds[$report->id])
        )->count();
        $percent = $target === 0 ? 0.0 : round($completed / $target * 100, 2);

        return [
            'year' => $year,
            'month' => $month,
            'period_label' => $month === null
                ? (string) $year
                : self::monthLabel($start->copy()),
            'planned' => $plannedCount,
            'pending' => $pendingCount,
            'completed' => $completed,
            'remaining' => $remaining,
            'unplanned' => $unplanned,
            'target' => $target,
            'actual' => $completed,
            'percent' => $percent,
            'percent_label' => number_format($percent, 2, '.', '').'%',
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private static function pmPeriodBounds(int $year, ?int $month): array
    {
        if ($month === null) {
            $start = Carbon::create($year, 1, 1, 0, 0, 0, 'Asia/Jakarta')->startOfYear();

            return [$start, $start->copy()->endOfYear()];
        }

        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta')->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * @param  Collection<int, OperatorReport>  $tickets
     * @return array{labels: list<string>, on_progress: list<int>, closed_technician: list<int>, closed_admin: list<int>}
     */
    private static function performerChart(Collection $tickets): array
    {
        $grouped = $tickets
            ->filter(fn (OperatorReport $ticket) => $ticket->claimed_technician_id || $ticket->claimed_by)
            ->groupBy(fn (OperatorReport $ticket) => $ticket->claimed_technician_id
                ? 't:'.$ticket->claimed_technician_id
                : 'u:'.$ticket->claimed_by)
            ->map(function (Collection $rows) {
                $first = $rows->first();
                $name = $first?->claimedTechnician?->name
                    ?: $first?->claimedBy?->name
                    ?: $first?->maintenanceReport?->pic_name
                    ?: 'Teknisi';

                return [
                    'name' => $name,
                    'on_progress' => $rows->where('status', RepairStatus::ON_PROGRESS)->count(),
                    'closed_technician' => $rows->whereIn('status', [
                        RepairStatus::CLOSE_BY_TECHNICIAN,
                        RepairStatus::APPROVE_PRODUKSI_INJECTION,
                        RepairStatus::APPROVE_QUALITY,
                    ])->count(),
                    'closed_admin' => $rows->where('status', RepairStatus::CLOSE)->count(),
                    'total' => $rows->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return [
            'labels' => $grouped->pluck('name')->all(),
            'on_progress' => $grouped->pluck('on_progress')->all(),
            'closed_technician' => $grouped->pluck('closed_technician')->all(),
            'closed_admin' => $grouped->pluck('closed_admin')->all(),
        ];
    }

    /**
     * MTTR per teknisi: tiket diambil hingga status Close by teknisi.
     *
     * @param  Collection<int, OperatorReport>  $tickets
     * @return array{labels: list<string>, values: list<int>, avg_minutes: int|null}
     */
    private static function technicianMttrChart(Collection $tickets): array
    {
        $grouped = $tickets
            ->filter(fn (OperatorReport $ticket) => $ticket->claimed_technician_id || $ticket->claimed_by)
            ->groupBy(fn (OperatorReport $ticket) => $ticket->claimed_technician_id
                ? 't:'.$ticket->claimed_technician_id
                : 'u:'.$ticket->claimed_by)
            ->map(function (Collection $rows) {
                $minutes = $rows
                    ->map(fn (OperatorReport $ticket) => self::workMinutes($ticket))
                    ->filter(fn ($value) => $value !== null)
                    ->values();

                if ($minutes->isEmpty()) {
                    return null;
                }

                $first = $rows->first();

                return [
                    'name' => $first?->claimedTechnician?->name
                        ?: $first?->claimedBy?->name
                        ?: $first?->maintenanceReport?->pic_name
                        ?: 'Teknisi',
                    'total' => (int) round($minutes->avg()),
                ];
            })
            ->filter()
            ->sortByDesc('total')
            ->take(10)
            ->values();

        $allMinutes = $tickets
            ->map(fn (OperatorReport $ticket) => self::workMinutes($ticket))
            ->filter(fn ($value) => $value !== null)
            ->values();

        return [
            'labels' => $grouped->pluck('name')->all(),
            'values' => $grouped->pluck('total')->all(),
            'avg_minutes' => $allMinutes->isEmpty() ? null : (int) round($allMinutes->avg()),
        ];
    }

    /**
     * MTTR per mesin: OP dibuat hingga status Close by teknisi.
     *
     * @param  Collection<int, OperatorReport>  $tickets
     * @return array{labels: list<string>, values: list<int>}
     */
    private static function lineStopChart(Collection $tickets): array
    {
        $grouped = $tickets
            ->groupBy('machine_number')
            ->map(function (Collection $rows, $number) {
                $minutes = $rows
                    ->map(fn (OperatorReport $ticket) => self::lineStopMinutes($ticket))
                    ->filter(fn ($value) => $value !== null)
                    ->values();

                if ($minutes->isEmpty()) {
                    return null;
                }

                return [
                    'label' => 'Mesin '.$number,
                    'total' => (int) round($minutes->avg()),
                ];
            })
            ->filter()
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return [
            'labels' => $grouped->pluck('label')->all(),
            'values' => $grouped->pluck('total')->all(),
        ];
    }

    private static function workMinutes(OperatorReport $ticket): ?int
    {
        if (! $ticket->claimed_at) {
            return null;
        }

        $endedAt = $ticket->maintenanceReport?->step2_completed_at;
        if (! $endedAt && in_array($ticket->status, RepairStatus::technicianWorkDoneValues(), true)) {
            $endedAt = $ticket->updated_at;
        }

        if (! $endedAt) {
            return null;
        }

        $minutes = (int) $ticket->claimed_at->diffInMinutes($endedAt, false);

        return $minutes < 0 ? null : $minutes;
    }

    private static function lineStopMinutes(OperatorReport $ticket): ?int
    {
        if (! in_array($ticket->status, RepairStatus::technicianWorkDoneValues(), true)) {
            return null;
        }

        $startedAt = $ticket->created_at;
        $endedAt = $ticket->maintenanceReport?->step2_completed_at
            ?: $ticket->updated_at;

        if (! $startedAt || ! $endedAt) {
            return null;
        }

        $minutes = (int) $startedAt->diffInMinutes($endedAt, false);

        return $minutes < 0 ? null : $minutes;
    }
}
