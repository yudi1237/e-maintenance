<?php

namespace App\Support;

use App\Models\MaintenanceReport;
use App\Models\PreventiveConveyorReport;
use App\Models\PreventiveReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TechnicianRecentWork
{
    public const HOURS = 24;

    public static function since(): Carbon
    {
        return now()->subHours(self::HOURS);
    }

    /**
     * @return Collection<int, array{kind: string, title: string, meta: string, at: Carbon, url: string, edit_url: ?string}>
     */
    public static function forUser(int $userId): Collection
    {
        $since = self::since();

        $op = MaintenanceReport::query()
            ->with('machineName')
            ->where('user_id', $userId)
            ->whereNotNull('step2_completed_at')
            ->where('step2_completed_at', '>=', $since)
            ->get()
            ->map(fn (MaintenanceReport $report) => [
                'kind' => 'Tiket OP',
                'title' => 'Mesin '.$report->machine_number.' · '.$report->machine_name_display,
                'meta' => (string) $report->pic_name,
                'at' => $report->step2_completed_at,
                'url' => route('home.submissions.op', $report),
                'edit_url' => $report->status_perbaikan === RepairStatus::CLOSE_BY_TECHNICIAN
                    ? route('home.submissions.op.edit', $report)
                    : null,
            ]);

        $preventive = PreventiveReport::query()
            ->with('machineName')
            ->where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->get()
            ->map(fn (PreventiveReport $report) => [
                'kind' => 'Preventive',
                'title' => $report->category_label.' · Mesin '.$report->machine_number.' · '.$report->machine_name_display,
                'meta' => trim($report->pic_name.' · '.$report->result_label),
                'at' => $report->created_at,
                'url' => route('home.submissions.preventive', $report),
                'edit_url' => ($report->status ?: PreventiveStatus::CLOSE_BY_TECHNICIAN) === PreventiveStatus::CLOSE_BY_TECHNICIAN
                    ? route('home.submissions.preventive.edit', $report)
                    : null,
            ]);

        $conveyor = PreventiveConveyorReport::query()
            ->where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->get()
            ->map(fn (PreventiveConveyorReport $report) => [
                'kind' => 'Preventive Conveyor',
                'title' => (string) $report->conveyor_name,
                'meta' => trim($report->pic_name.' · '.$report->result_label),
                'at' => $report->created_at,
                'url' => route('home.submissions.conveyor', $report),
                'edit_url' => route('home.submissions.conveyor.edit', $report),
            ]);

        return $op->concat($preventive)->concat($conveyor)
            ->sortByDesc(fn (array $row) => $row['at']?->getTimestamp() ?? 0)
            ->values();
    }

    public static function visible(Model $model, int $userId, string $column): bool
    {
        if ((int) $model->getAttribute('user_id') !== $userId) {
            return false;
        }

        $at = $model->getAttribute($column);

        return $at instanceof \DateTimeInterface && Carbon::parse($at)->greaterThanOrEqualTo(self::since());
    }
}
