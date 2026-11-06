<?php

namespace App\Support;

use App\Models\MaintenanceReport;
use App\Models\PreventiveConveyorReport;
use App\Models\PreventiveReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class SparepartHistory
{
    /**
     * @return Collection<int, array{tanggal: string, sort_date: string, sparepart: string, qty: int, machine_number: int|string, pic_name: string, report_id: int}>
     */
    public static function rows(Request $request): Collection
    {
        $sparepartFilter = mb_strtolower(trim((string) $request->input('sparepart', '')));
        $rows = collect();

        self::filteredReports($request)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->get()
            ->each(function (MaintenanceReport $report) use ($rows, $sparepartFilter): void {
                self::pushItems(
                    $rows,
                    $report->sparepart_items,
                    $report->report_date?->format('d/m/Y') ?? '—',
                    $report->report_date?->toDateString() ?? '0000-00-00',
                    (int) $report->machine_number,
                    (string) $report->pic_name,
                    $report->id,
                    $sparepartFilter
                );
            });

        self::filteredPreventiveReports($request)
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->get()
            ->each(function (PreventiveReport $report) use ($rows, $sparepartFilter): void {
                self::pushItems(
                    $rows,
                    $report->sparepart_items,
                    $report->work_date?->format('d/m/Y') ?? '—',
                    $report->work_date?->toDateString() ?? '0000-00-00',
                    (int) $report->machine_number,
                    (string) $report->pic_name,
                    $report->id,
                    $sparepartFilter
                );
            });

        if (! filled($request->input('machine_number'))) {
            self::filteredConveyorReports($request)
                ->orderByDesc('work_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (PreventiveConveyorReport $report) use ($rows, $sparepartFilter): void {
                    self::pushItems(
                        $rows,
                        $report->sparepart_items,
                        $report->work_date?->format('d/m/Y') ?? '—',
                        $report->work_date?->toDateString() ?? '0000-00-00',
                        (string) $report->conveyor_name,
                        (string) $report->pic_name,
                        $report->id,
                        $sparepartFilter
                    );
                });
        }

        return $rows
            ->sortByDesc(fn (array $row) => $row['sort_date'])
            ->values();
    }

    public static function paginate(Request $request): LengthAwarePaginator
    {
        $rows = self::rows($request);
        $perPage = TablePagination::perPage();
        $page = Paginator::resolveCurrentPage();
        $total = $rows->count();

        return new Paginator(
            $rows->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
    }

    public static function filteredReports(Request $request): Builder
    {
        $query = MaintenanceReport::query()
            ->whereNotNull('operator_report_id')
            ->whereNotNull('step2_completed_at')
            ->whereNotNull('sparepart_items')
            ->where('status_perbaikan', RepairStatus::CLOSE)
            ->whereHas('operatorReport', function (Builder $ticket): void {
                $ticket->where('status', RepairStatus::CLOSE);
            });

        $start = $request->input('start_date');
        $end = $request->input('end_date');
        if (is_string($start) && $start !== '') {
            $query->whereDate('report_date', '>=', $start);
        }
        if (is_string($end) && $end !== '') {
            $query->whereDate('report_date', '<=', $end);
        }

        $machineNumber = $request->input('machine_number');
        if (filled($machineNumber)) {
            $query->where('machine_number', (int) $machineNumber);
        }

        $pic = trim((string) $request->input('pic', ''));
        if ($pic !== '') {
            $query->where('pic_name', 'like', '%'.$pic.'%');
        }

        return $query;
    }

    public static function filteredPreventiveReports(Request $request): Builder
    {
        $query = PreventiveReport::query()->whereNotNull('sparepart_items');

        self::applyWorkDateFilters($query, $request);

        return $query;
    }

    public static function filteredConveyorReports(Request $request): Builder
    {
        $query = PreventiveConveyorReport::query()->whereNotNull('sparepart_items');

        self::applyWorkDateFilters($query, $request);

        return $query;
    }

    private static function applyWorkDateFilters(Builder $query, Request $request): void
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        if (is_string($start) && $start !== '') {
            $query->whereDate('work_date', '>=', $start);
        }
        if (is_string($end) && $end !== '') {
            $query->whereDate('work_date', '<=', $end);
        }

        $machineNumber = $request->input('machine_number');
        if (filled($machineNumber) && $query->getModel() instanceof PreventiveReport) {
            $query->where('machine_number', (int) $machineNumber);
        }

        $pic = trim((string) $request->input('pic', ''));
        if ($pic !== '') {
            $query->where('pic_name', 'like', '%'.$pic.'%');
        }
    }

    /**
     * @param  Collection<int, array{tanggal: string, sort_date: string, sparepart: string, qty: int, machine_number: int|string, pic_name: string, report_id: int}>  $rows
     * @param  array<int, array{name?: mixed, qty?: mixed}>|null  $items
     */
    private static function pushItems(
        Collection $rows,
        ?array $items,
        string $date,
        string $sortDate,
        int|string $machineNumber,
        string $picName,
        int $reportId,
        string $sparepartFilter,
    ): void {
        foreach ($items ?? [] as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $qty = (int) ($item['qty'] ?? 0);
            if ($name === '' || $qty < 1) {
                continue;
            }

            if ($sparepartFilter !== '' && ! str_contains(mb_strtolower($name), $sparepartFilter)) {
                continue;
            }

            $rows->push([
                'tanggal' => $date,
                'sort_date' => $sortDate,
                'sparepart' => $name,
                'qty' => $qty,
                'machine_number' => $machineNumber,
                'pic_name' => $picName,
                'report_id' => $reportId,
            ]);
        }
    }
}
