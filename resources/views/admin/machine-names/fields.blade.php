@php
    $machine = $machine ?? null;
    $value = fn (string $key, mixed $default = null) => old($key, $machine?->{$key} ?? $default);
    $cellInput = 'em-input !mt-0 min-h-[40px] rounded-lg px-2 py-1.5 text-sm';
@endphp

<div class="md:overflow-x-auto md:rounded-xl md:border md:border-slate-200 dark:md:border-white/10">
    <table class="w-full min-w-0 border-separate border-spacing-0 text-sm md:min-w-[1280px]">
        <thead class="hidden md:table-header-group">
            <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                <x-admin.spreadsheet-th>No</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Plant</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>No Mesin</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>No Asset Mesin</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Brand</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Machine Model</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Diameter Screw (mm)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Tonage (Ton)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Clamping Type</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Tipe</x-admin.spreadsheet-th>
            </tr>
        </thead>
        <tbody class="block space-y-4 md:table-row-group md:space-y-0">
            <tr class="block rounded-xl border border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                <x-admin.spreadsheet-td label="No">
                    <span class="inline-flex min-h-[40px] min-w-[2.5rem] items-center justify-center font-semibold text-slate-800 dark:text-slate-100">1</span>
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Plant">
                    <select id="plant" name="plant" class="{{ $cellInput }}" required>
                        @foreach (\App\Models\MachineName::plantOptions() as $plantValue => $plantLabel)
                            <option value="{{ $plantValue }}" @selected($value('plant', $defaultPlant ?? \App\Models\MachineName::PLANT_CIKARANG) === $plantValue)>{{ $plantLabel }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('plant')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="No Mesin">
                    <input id="machine_number" name="machine_number" type="number" min="1" max="{{ config('maintenance.machine_count', 60) }}" class="{{ $cellInput }}" value="{{ $value('machine_number') }}" required>
                    <x-input-error :messages="$errors->get('machine_number')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="No Asset Mesin">
                    <input id="asset_number" name="asset_number" type="text" class="{{ $cellInput }}" value="{{ $value('asset_number') }}" required>
                    <x-input-error :messages="$errors->get('asset_number')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Brand">
                    <input id="brand" name="brand" type="text" class="{{ $cellInput }}" value="{{ $value('brand') }}" required>
                    <x-input-error :messages="$errors->get('brand')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Machine Model">
                    <input id="machine_model" name="machine_model" type="text" class="{{ $cellInput }}" value="{{ $value('machine_model') }}" required>
                    <x-input-error :messages="$errors->get('machine_model')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Diameter Screw (mm)">
                    <input id="screw_diameter_mm" name="screw_diameter_mm" type="number" min="1" class="{{ $cellInput }}" value="{{ $value('screw_diameter_mm') }}" required>
                    <x-input-error :messages="$errors->get('screw_diameter_mm')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Tonage (Ton)">
                    <input id="tonnage" name="tonnage" type="number" min="1" class="{{ $cellInput }}" value="{{ $value('tonnage') }}" required>
                    <x-input-error :messages="$errors->get('tonnage')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Clamping Type">
                    <input id="clamping_type" name="clamping_type" type="text" class="{{ $cellInput }}" value="{{ $value('clamping_type') }}" required>
                    <x-input-error :messages="$errors->get('clamping_type')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Tipe">
                    <input id="machine_type" name="machine_type" type="text" class="{{ $cellInput }}" value="{{ $value('machine_type') }}" required>
                    <x-input-error :messages="$errors->get('machine_type')" class="mt-1" />
                </x-admin.spreadsheet-td>
            </tr>
        </tbody>
    </table>
</div>

<div class="md:overflow-x-auto md:rounded-xl md:border md:border-slate-200 dark:md:border-white/10">
    <table class="w-full min-w-0 border-separate border-spacing-0 text-sm md:min-w-[1280px]">
        <thead class="hidden md:table-header-group">
            <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                <x-admin.spreadsheet-th>Tie Bar Distance (mm)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Platen Size (mm)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Mold Thickness Min (mm)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Mold Thickness Max (mm)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Clamp Stroke (mm)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Tahun Pembuatan</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Shot Weight Small (gr)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Shot Weight Med (gr)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Power Pump (kW)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Power Heater (kW)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>Power Total (kW)</x-admin.spreadsheet-th>
            </tr>
        </thead>
        <tbody class="block space-y-4 md:table-row-group md:space-y-0">
            <tr class="block rounded-xl border border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                <x-admin.spreadsheet-td label="Tie Bar Distance (mm)">
                    <input id="tie_bar_distance_mm" name="tie_bar_distance_mm" type="text" class="{{ $cellInput }}" placeholder="450 x 450" value="{{ $value('tie_bar_distance_mm') }}" required>
                    <x-input-error :messages="$errors->get('tie_bar_distance_mm')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Platen Size (mm)">
                    <input id="platen_size_mm" name="platen_size_mm" type="text" class="{{ $cellInput }}" placeholder="600 x 600" value="{{ $value('platen_size_mm') }}" required>
                    <x-input-error :messages="$errors->get('platen_size_mm')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Mold Thickness Min (mm)">
                    <input id="mold_thickness_min_mm" name="mold_thickness_min_mm" type="number" min="1" class="{{ $cellInput }}" placeholder="Min" value="{{ $value('mold_thickness_min_mm') }}" required>
                    <x-input-error :messages="$errors->get('mold_thickness_min_mm')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Mold Thickness Max (mm)">
                    <input id="mold_thickness_max_mm" name="mold_thickness_max_mm" type="number" min="1" class="{{ $cellInput }}" placeholder="Max" value="{{ $value('mold_thickness_max_mm') }}" required>
                    <x-input-error :messages="$errors->get('mold_thickness_max_mm')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Clamp Stroke (mm)">
                    <input id="clamp_stroke_mm" name="clamp_stroke_mm" type="number" min="1" class="{{ $cellInput }}" value="{{ $value('clamp_stroke_mm') }}" required>
                    <x-input-error :messages="$errors->get('clamp_stroke_mm')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Tahun Pembuatan">
                    <input id="manufacture_year" name="manufacture_year" type="number" min="1900" max="2100" class="{{ $cellInput }}" value="{{ $value('manufacture_year') }}" required>
                    <x-input-error :messages="$errors->get('manufacture_year')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Shot Weight Small (gr)">
                    <input id="shot_weight_small_gr" name="shot_weight_small_gr" type="number" min="0" class="{{ $cellInput }}" placeholder="Small (gr)" value="{{ $value('shot_weight_small_gr') }}" required>
                    <x-input-error :messages="$errors->get('shot_weight_small_gr')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Shot Weight Med (gr)">
                    <input id="shot_weight_med_gr" name="shot_weight_med_gr" type="number" min="0" class="{{ $cellInput }}" placeholder="Med (gr)" value="{{ $value('shot_weight_med_gr') }}" required>
                    <x-input-error :messages="$errors->get('shot_weight_med_gr')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Power Pump (kW)">
                    <input id="power_pump_kw" name="power_pump_kw" type="number" step="0.01" min="0" class="{{ $cellInput }}" placeholder="Pump (kW)" value="{{ $value('power_pump_kw') }}" required>
                    <x-input-error :messages="$errors->get('power_pump_kw')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Power Heater (kW)">
                    <input id="power_heater_kw" name="power_heater_kw" type="number" step="0.01" min="0" class="{{ $cellInput }}" placeholder="Heater (kW)" value="{{ $value('power_heater_kw') }}" required>
                    <x-input-error :messages="$errors->get('power_heater_kw')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td label="Power Total (kW)">
                    <input id="power_total_kw" name="power_total_kw" type="number" step="0.01" min="0" class="{{ $cellInput }}" placeholder="Total (kW)" value="{{ $value('power_total_kw') }}" required>
                    <x-input-error :messages="$errors->get('power_total_kw')" class="mt-1" />
                </x-admin.spreadsheet-td>
            </tr>
        </tbody>
    </table>
</div>

<div class="md:overflow-x-auto md:rounded-xl md:border md:border-slate-200 dark:md:border-white/10">
    <table class="w-full min-w-0 border-separate border-spacing-0 text-sm md:min-w-[720px]">
        <thead class="hidden md:table-header-group">
            <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                <x-admin.spreadsheet-th>Catatan (Opsional)</x-admin.spreadsheet-th>
                <x-admin.spreadsheet-th>{{ $machine ? 'Tambah Foto Mesin (opsional)' : 'Foto Mesin (bisa lebih dari 1)' }}</x-admin.spreadsheet-th>
            </tr>
        </thead>
        <tbody class="block space-y-4 md:table-row-group md:space-y-0">
            <tr class="block rounded-xl border border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                <x-admin.spreadsheet-td label="Catatan (Opsional)">
                    <textarea id="specification" name="specification" rows="3" class="{{ $cellInput }} min-h-[6.5rem]">{{ $value('specification') }}</textarea>
                    <x-input-error :messages="$errors->get('specification')" class="mt-1" />
                </x-admin.spreadsheet-td>
                <x-admin.spreadsheet-td :label="$machine ? 'Tambah Foto Mesin (opsional)' : 'Foto Mesin (bisa lebih dari 1)'">
                    @if ($machine && ! empty($machine->machine_photos))
                        <div class="mb-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($machine->machine_photos as $index => $path)
                                <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-slate-900/40">
                                    <img src="{{ asset('storage/'.$path) }}" alt="Foto Mesin {{ $index + 1 }}" class="h-28 w-full object-cover">
                                    <div class="flex items-center justify-between gap-2 px-2 py-2">
                                        <form method="POST" action="{{ route('admin.machine-names.photos.update', [$machine, $index]) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input id="replace-photo-{{ $index }}" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="hidden" onchange="this.form.submit()">
                                            <label for="replace-photo-{{ $index }}" class="cursor-pointer text-xs font-medium text-cyan-600 hover:underline dark:text-cyan-400">Ganti</label>
                                        </form>
                                        <form method="POST" action="{{ route('admin.machine-names.photos.destroy', [$machine, $index]) }}" onsubmit="return confirm('Hapus foto ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="flex min-h-[40px] items-center">
                        <input id="photos" name="photos[]" type="file" accept=".jpg,.jpeg,.png,.webp" multiple class="block w-full text-sm text-gray-700 file:mr-3 file:rounded file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium dark:text-slate-200 dark:file:bg-slate-800 dark:file:text-slate-200">
                    </div>
                    <x-input-error :messages="$errors->get('photos')" class="mt-1" />
                    <x-input-error :messages="collect($errors->get('photos.*'))->flatten()->all()" class="mt-1" />
                </x-admin.spreadsheet-td>
            </tr>
        </tbody>
    </table>
</div>

<div class="rounded-xl border border-slate-200 p-3 dark:border-white/10">
    <label for="severity_condition" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Severity/Kondisi</label>
    @php
        $selectedSeverity = old('severity_condition', data_get($machine?->severity_conditions, 0, ''));
    @endphp
    <select id="severity_condition" name="severity_condition" class="em-input mt-2 max-w-xs">
        <option value="">—</option>
        @foreach (\App\Models\MachineName::severityOptions() as $severityValue => $severityLabel)
            <option value="{{ $severityValue }}" @selected($selectedSeverity === $severityValue)>{{ $severityLabel }}</option>
        @endforeach
    </select>
</div>
