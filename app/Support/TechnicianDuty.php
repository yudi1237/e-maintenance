<?php

namespace App\Support;

use App\Models\OperatorReport;
use App\Models\Technician;
use App\Models\TechnicianPreventiveJob;

class TechnicianDuty
{
    /**
     * @return list<array{id: int, name: string, shift: int|null, state: string, badge: string, detail: string|null}>
     */
    public static function snapshot(): array
    {
        $technicians = Technician::query()->active()->orderBy('name')->orderBy('id')->get();
        $jobs = [];

        foreach ($technicians as $technician) {
            $jobs[$technician->id] = null;
        }

        $tickets = OperatorReport::query()
            ->where('status', RepairStatus::ON_PROGRESS)
            ->whereNotNull('claimed_technician_id')
            ->orderByDesc('claimed_at')
            ->orderByDesc('id')
            ->get(['id', 'claimed_technician_id', 'machine_number']);

        foreach ($tickets as $ticket) {
            $id = (int) $ticket->claimed_technician_id;
            if (! array_key_exists($id, $jobs) || $jobs[$id] !== null) {
                continue;
            }

            $jobs[$id] = 'Tiket #'.$ticket->id.' · Mesin '.$ticket->machine_number;
        }

        $preventives = TechnicianPreventiveJob::query()
            ->whereNull('closed_at')
            ->orderByDesc('id')
            ->get();

        foreach ($preventives as $job) {
            $id = (int) $job->technician_id;
            if (! array_key_exists($id, $jobs) || $jobs[$id] !== null) {
                continue;
            }

            $jobs[$id] = $job->detailLabel();
        }

        return $technicians->map(function (Technician $technician) use ($jobs): array {
            $detail = $jobs[$technician->id] ?? null;
            $onShift = $technician->isOnShift();
            $state = ! $onShift ? 'off' : ($detail ? 'busy' : ($technician->overtime ? 'overtime' : 'idle'));
            $badge = match ($state) {
                'off' => 'Off-duty',
                'busy' => 'On Job / Assigned',
                'overtime' => 'Overtime',
                default => 'Standby / Available',
            };

            return [
                'id' => $technician->id,
                'name' => $technician->name,
                'shift' => $technician->shift ? (int) $technician->shift : null,
                'off_duty' => (bool) $technician->off_duty,
                'overtime' => (bool) $technician->overtime,
                'state' => $state,
                'badge' => $badge,
                'detail' => $onShift ? $detail : null,
            ];
        })->all();
    }
}
