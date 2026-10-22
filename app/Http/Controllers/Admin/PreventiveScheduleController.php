<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePreventiveScheduleRequest;
use App\Models\MachineName;
use App\Models\PreventiveSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PreventiveScheduleController extends Controller
{
    public function store(StorePreventiveScheduleRequest $request): RedirectResponse
    {
        $userId = $request->user()->id;
        $created = 0;

        DB::transaction(function () use ($request, $userId, &$created): void {
            foreach ($request->validated('rows') as $row) {
                $machine = MachineName::query()->findOrFail((int) $row['machine_name_id']);

                PreventiveSchedule::query()->create($this->scheduleAttributes($machine, $row, $userId));
                $created++;
            }
        });

        return redirect()
            ->route('admin.preventive.index')
            ->with('success', $created === 1
                ? 'Jadwal preventive berhasil disimpan.'
                : $created.' jadwal preventive berhasil disimpan.');
    }

    public function update(StorePreventiveScheduleRequest $request, PreventiveSchedule $preventiveSchedule): RedirectResponse
    {
        $machine = MachineName::query()->findOrFail($request->integer('machine_name_id'));

        $preventiveSchedule->update($this->scheduleAttributes($machine, [
            'plan_date' => $request->string('plan_date')->toString(),
            'preventive_type' => $request->string('preventive_type')->toString(),
            'notes' => $request->input('notes'),
        ]));

        return redirect()
            ->route('admin.preventive.index')
            ->with('success', 'Jadwal preventive berhasil diperbarui.');
    }

    public function destroy(PreventiveSchedule $preventiveSchedule): RedirectResponse
    {
        $preventiveSchedule->delete();

        return redirect()
            ->route('admin.preventive.index')
            ->with('success', 'Jadwal preventive berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function scheduleAttributes(MachineName $machine, array $row, ?int $userId = null): array
    {
        $attributes = [
            'machine_name_id' => $machine->id,
            'machine_number' => (int) $machine->machine_number,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $row['plan_date'],
            'preventive_type' => $row['preventive_type'],
            'notes' => filled($row['notes'] ?? null) ? (string) $row['notes'] : null,
        ];

        if ($userId !== null) {
            $attributes['user_id'] = $userId;
        }

        return $attributes;
    }
}
