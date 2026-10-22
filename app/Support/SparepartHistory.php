<?php

namespace App\Support;

use App\Models\MaintenanceReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class SparepartHistory
{
    /**
     * @return Collection<int, array{tanggal: string, sort_date: string, sparepart: string, qty: int, machine_number: int, pic_name: string, report_id: int}>
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
                $date = $report->report_date?->format('d/m/Y') ?? '—';
                $sortDate = $report->report_date?->toDateString() ?? '0000-00-00';

                foreach ($report->sparepart_items ?? [] as $item) {
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
                        'machine_number' => (int) $report->machine_number,
                        'pic_name' => (string) $report->pic_name,
                        'report_id' => $report->id,
                    ]);
                }
            });

        return $rows->values();
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
}
