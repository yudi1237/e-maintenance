<?php

namespace App\Support;

use App\Models\AreaOperatorReport;
use App\Models\FindingRecap;
use App\Models\OperatorReport;

class ReportMenu
{
    /**
     * Jumlah yang belum close untuk badge menu Report.
     *
     * @return array{findings: int, machines: int, areas: int}
     */
    public static function counts(): array
    {
        return [
            'findings' => FindingRecap::query()
                ->where('status', '!=', FindingRecapStatus::CLOSE)
                ->count(),
            'machines' => OperatorReport::query()
                ->where('status', '!=', RepairStatus::CLOSE)
                ->count(),
            'areas' => AreaOperatorReport::query()
                ->whereNotIn('status', [RepairStatus::CLOSE, RepairStatus::CLOSE_BY_TECHNICIAN])
                ->count(),
        ];
    }
}
