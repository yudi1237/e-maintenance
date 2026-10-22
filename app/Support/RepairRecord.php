<?php

namespace App\Support;

use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use Illuminate\Support\Facades\Storage;

class RepairRecord
{
    public static function deleteTicket(OperatorReport $ticket): void
    {
        $ticket->loadMissing('maintenanceReport');

        if ($ticket->maintenanceReport) {
            self::deleteReportFiles($ticket->maintenanceReport);
            $ticket->maintenanceReport->delete();
        }

        $ticket->delete();
    }

    public static function deleteReport(MaintenanceReport $report): void
    {
        $ticketId = $report->operator_report_id;

        self::deleteReportFiles($report);
        $report->delete();

        if ($ticketId) {
            OperatorReport::query()->whereKey($ticketId)->delete();
        }
    }

    private static function deleteReportFiles(MaintenanceReport $report): void
    {
        if ($report->evidence_path) {
            Storage::disk('public')->delete($report->evidence_path);
        }
    }
}
