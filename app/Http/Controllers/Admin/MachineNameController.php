<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MachineNameXlsxExport;
use App\Http\Controllers\Controller;
use App\Models\MachineName;
use App\Support\TablePagination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MachineNameController extends Controller
{
    public function index(Request $request): View
    {
        $plant = $this->selectedPlant($request);

        return view('admin.machine-names.index', [
            'plant' => $plant,
            'plants' => MachineName::plantOptions(),
            'machineNames' => MachineName::query()
                ->when($plant, fn ($query) => $query->where('plant', $plant))
                ->orderByRaw('machine_number is null')
                ->orderBy('machine_number')
                ->orderBy('name')
                ->paginate(TablePagination::perPage())
                ->withQueryString(),
        ]);
    }

    public function edit(MachineName $machineName): View
    {
        return view('admin.machine-names.edit', [
            'machine' => $machineName,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->specRules(), $this->specMessages());
        $machine = MachineName::create($this->payload($validated, isActive: true));
        $this->storePhotos($request, $machine);

        return back()->with('success', 'Nama mesin berhasil ditambahkan.');
    }

    public function update(Request $request, MachineName $machineName): RedirectResponse
    {
        if ($request->boolean('toggle_active')) {
            $machineName->update([
                'is_active' => $request->boolean('is_active'),
            ]);

            return back()->with('success', 'Status mesin berhasil diperbarui.');
        }

        if ($request->boolean('update_severity')) {
            $validated = $request->validate([
                'severity_condition' => ['nullable', 'string', Rule::in(array_keys(MachineName::severityOptions()))],
            ]);
            $condition = $validated['severity_condition'] ?? null;
            $machineName->update([
                'severity_conditions' => $condition ? [$condition] : [],
            ]);

            return back()->with('success', 'Severity/kondisi mesin berhasil diperbarui.');
        }

        $validated = $request->validate($this->specRules($machineName->id), $this->specMessages());
        $machineName->update($this->payload($validated, $machineName->is_active));
        $this->storePhotos($request, $machineName, merge: true);

        return redirect()
            ->route('admin.machine-names.index')
            ->with('success', 'Master data mesin berhasil diperbarui.');
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $plant = $this->selectedPlant($request);
        $machines = MachineName::query()
            ->when($plant, fn ($query) => $query->where('plant', $plant))
            ->orderByRaw('machine_number is null')
            ->orderBy('machine_number')
            ->orderBy('name')
            ->get();

        if ($machines->isEmpty()) {
            return redirect()
                ->route('admin.machine-names.index')
                ->withErrors(['export' => 'Belum ada master data mesin untuk diunduh.']);
        }

        $export = new MachineNameXlsxExport;
        $filename = $export->filename();

        return response()->streamDownload(function () use ($export, $machines) {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
            if ($tmp === false) {
                throw new \RuntimeException('Tidak dapat membuat file sementara untuk export Excel.');
            }

            $path = $tmp.'.xlsx';
            if (! rename($tmp, $path)) {
                @unlink($tmp);
                throw new \RuntimeException('Tidak dapat menyiapkan file export Excel.');
            }

            try {
                $export->buildExporter($machines)->writeToFile($path);
                readfile($path);
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function updatePhoto(Request $request, MachineName $machineName, int $photoIndex): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $photos = array_values($machineName->machine_photos ?? []);
        abort_unless(isset($photos[$photoIndex]), 404);

        Storage::disk('public')->delete($photos[$photoIndex]);
        $photos[$photoIndex] = $request->file('photo')->store(
            'machine-photos/'.$machineName->plant.'/'.$machineName->machine_number,
            'public'
        );
        $machineName->update(['machine_photos' => $photos]);

        return back()->with('success', 'Foto mesin berhasil diganti.');
    }

    public function destroyPhoto(MachineName $machineName, int $photoIndex): RedirectResponse
    {
        $photos = array_values($machineName->machine_photos ?? []);
        abort_unless(isset($photos[$photoIndex]), 404);

        Storage::disk('public')->delete($photos[$photoIndex]);
        unset($photos[$photoIndex]);
        $machineName->update(['machine_photos' => array_values($photos)]);

        return back()->with('success', 'Foto mesin berhasil dihapus.');
    }

    public function destroy(MachineName $machineName): RedirectResponse
    {
        $machineName->delete();

        return back()->with('success', 'Nama mesin berhasil dihapus.');
    }

    private function selectedPlant(Request $request): ?string
    {
        $plant = (string) $request->query('plant', '');

        return array_key_exists($plant, MachineName::plantOptions()) ? $plant : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function specRules(?int $ignoreId = null): array
    {
        $plant = (string) request()->input('plant');
        $uniqueNumber = Rule::unique('machine_names', 'machine_number')
            ->where(fn ($query) => $query->where('plant', $plant));
        $uniqueAsset = Rule::unique('machine_names', 'asset_number');

        if ($ignoreId) {
            $uniqueNumber->ignore($ignoreId);
            $uniqueAsset->ignore($ignoreId);
        }

        return [
            'machine_number' => ['required', 'integer', 'min:1', 'max:'.config('maintenance.machine_count', 60), $uniqueNumber],
            'plant' => ['required', 'string', Rule::in(array_keys(MachineName::plantOptions()))],
            'asset_number' => ['required', 'string', 'max:100', $uniqueAsset],
            'screw_diameter_mm' => ['required', 'integer', 'min:1', 'max:9999'],
            'brand' => ['required', 'string', 'max:100'],
            'machine_model' => ['required', 'string', 'max:150'],
            'tonnage' => ['required', 'integer', 'min:1', 'max:9999'],
            'clamping_type' => ['required', 'string', 'max:100'],
            'machine_type' => ['required', 'string', 'max:100'],
            'tie_bar_distance_mm' => ['required', 'string', 'max:50'],
            'platen_size_mm' => ['required', 'string', 'max:50'],
            'mold_thickness_min_mm' => ['required', 'integer', 'min:1', 'max:9999'],
            'mold_thickness_max_mm' => ['required', 'integer', 'min:1', 'max:9999', 'gte:mold_thickness_min_mm'],
            'clamp_stroke_mm' => ['required', 'integer', 'min:1', 'max:9999'],
            'power_pump_kw' => ['required', 'numeric', 'min:0', 'max:9999'],
            'power_heater_kw' => ['required', 'numeric', 'min:0', 'max:9999'],
            'power_total_kw' => ['required', 'numeric', 'min:0', 'max:9999'],
            'shot_weight_small_gr' => ['required', 'integer', 'min:0', 'max:9999'],
            'shot_weight_med_gr' => ['required', 'integer', 'min:0', 'max:9999'],
            'manufacture_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'specification' => ['nullable', 'string', 'max:2000'],
            'severity_condition' => ['nullable', 'string', Rule::in(array_keys(MachineName::severityOptions()))],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ];
    }

    /** @return array<string, string> */
    private function specMessages(): array
    {
        return [
            'machine_number.unique' => 'No mesin ini sudah ada di plant yang sama. Plant lain boleh memakai nomor yang sama.',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated, bool $isActive): array
    {
        return [
            'machine_number' => $validated['machine_number'],
            'plant' => $validated['plant'],
            'asset_number' => $validated['asset_number'],
            'screw_diameter_mm' => $validated['screw_diameter_mm'],
            'brand' => $validated['brand'],
            'machine_model' => $validated['machine_model'],
            'tonnage' => $validated['tonnage'],
            'clamping_type' => $validated['clamping_type'],
            'machine_type' => $validated['machine_type'],
            'tie_bar_distance_mm' => $validated['tie_bar_distance_mm'],
            'platen_size_mm' => $validated['platen_size_mm'],
            'mold_thickness_min_mm' => $validated['mold_thickness_min_mm'],
            'mold_thickness_max_mm' => $validated['mold_thickness_max_mm'],
            'clamp_stroke_mm' => $validated['clamp_stroke_mm'],
            'power_pump_kw' => $validated['power_pump_kw'],
            'power_heater_kw' => $validated['power_heater_kw'],
            'power_total_kw' => $validated['power_total_kw'],
            'shot_weight_small_gr' => $validated['shot_weight_small_gr'],
            'shot_weight_med_gr' => $validated['shot_weight_med_gr'],
            'manufacture_year' => $validated['manufacture_year'],
            'name' => $validated['brand'].' '.$validated['machine_model'].' '.$validated['tonnage'].'T',
            'specification' => $validated['specification'] ?? null,
            'severity_conditions' => filled($validated['severity_condition'] ?? null) ? [$validated['severity_condition']] : [],
            'is_active' => $isActive,
        ];
    }

    private function storePhotos(Request $request, MachineName $machine, bool $merge = false): void
    {
        $photoPaths = [];
        foreach ($request->file('photos', []) as $file) {
            $photoPaths[] = $file->store('machine-photos/'.$machine->plant.'/'.$machine->machine_number, 'public');
        }

        if ($photoPaths === []) {
            return;
        }

        $machine->update([
            'machine_photos' => $merge
                ? array_values(array_merge($machine->machine_photos ?? [], $photoPaths))
                : $photoPaths,
        ]);
    }
}
