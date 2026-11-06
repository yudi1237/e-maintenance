<?php

namespace App\Exports;

use App\Models\MachineName;
use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class MachineNameXlsxExport
{
    /**
     * @param  Collection<int, MachineName>  $machines
     */
    public function buildExporter(Collection $machines): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName('Master Data Mesin');

        $exporter->addRow([
            'No Mesin',
            'Plant',
            'No Asset',
            'Brand',
            'Machine Model',
            'Tonage',
            'Nama Mesin',
            'Diameter Screw (mm)',
            'Clamping Type',
            'Tipe',
            'Tie Bar Distance (mm)',
            'Platen Size (mm)',
            'Mold Thickness Min (mm)',
            'Mold Thickness Max (mm)',
            'Clamp Stroke (mm)',
            'Power Pump (kW)',
            'Power Heater (kW)',
            'Power Total (kW)',
            'Shot Weight Small (gr)',
            'Shot Weight Med (gr)',
            'Tahun Pembuatan',
            'Catatan',
            'Jumlah Foto',
            'Status',
            'Severity/Kondisi',
        ]);

        foreach ($machines as $machine) {
            $exporter->addRow([
                $machine->machine_number,
                MachineName::plantOptions()[$machine->plant] ?? $machine->plant,
                $machine->asset_number,
                $machine->brand,
                $machine->machine_model,
                $machine->tonnage,
                $machine->name,
                $machine->screw_diameter_mm,
                $machine->clamping_type,
                $machine->machine_type,
                $machine->tie_bar_distance_mm,
                $machine->platen_size_mm,
                $machine->mold_thickness_min_mm,
                $machine->mold_thickness_max_mm,
                $machine->clamp_stroke_mm,
                $machine->power_pump_kw,
                $machine->power_heater_kw,
                $machine->power_total_kw,
                $machine->shot_weight_small_gr,
                $machine->shot_weight_med_gr,
                $machine->manufacture_year,
                $machine->specification,
                is_array($machine->machine_photos) ? count($machine->machine_photos) : 0,
                $machine->is_active ? 'Aktif' : 'Nonaktif',
                collect(MachineName::severityOptions())
                    ->only($machine->severity_conditions ?? [])
                    ->implode(', '),
            ]);
        }

        return $exporter;
    }

    public function filename(): string
    {
        return 'master-data-mesin-'.now()->timezone('Asia/Jakarta')->format('Ymd-His').'.xlsx';
    }
}
