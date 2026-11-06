<div class="em-glass mb-6 rounded-2xl p-6">
    <div class="mb-5 flex items-center gap-3">
        <div class="rounded-xl bg-indigo-500/20 p-2.5">
            <svg class="h-5 w-5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Spesifikasi Mesin</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 truncate">
                {{ $machine?->view_label ?: 'Data master mesin belum diisi' }}
            </p>
        </div>
    </div>

    @if ($machine)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">No Asset Mesin</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->asset_number ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Diameter Screw (mm)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->screw_diameter_mm ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Brand</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->brand ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Machine Model</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->machine_model ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Tonage</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->tonnage ? $machine->tonnage.'T' : '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Tahun Pembuatan</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->manufacture_year ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Clamping Type</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->clamping_type ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Tipe</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->machine_type ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Tie Bar Distance (mm)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->tie_bar_distance_mm ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Platen Size (mm)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->platen_size_mm ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Mold Thickness Min (mm)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->mold_thickness_min_mm ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Mold Thickness Max (mm)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->mold_thickness_max_mm ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Clamp Stroke (mm)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->clamp_stroke_mm ?: '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Power Total (kW)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->power_total_kw !== null ? $machine->power_total_kw.' kW' : '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Power Pump (kW)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->power_pump_kw !== null ? $machine->power_pump_kw.' kW' : '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Power Heater (kW)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->power_heater_kw !== null ? $machine->power_heater_kw.' kW' : '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Shot Weight Small (gr)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->shot_weight_small_gr !== null ? $machine->shot_weight_small_gr.' gr' : '—' }}</div>
            </div>
            <div class="em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Shot Weight Med (gr)</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $machine->shot_weight_med_gr !== null ? $machine->shot_weight_med_gr.' gr' : '—' }}</div>
            </div>
        </div>

        @if ($machine->specification)
            <div class="mt-4 em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Catatan</div>
                <div class="mt-1 text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line">{{ $machine->specification }}</div>
            </div>
        @endif

        @if (! empty($machine->machine_photos))
            <div class="mt-4 em-spec-card">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Foto Mesin</div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @foreach ($machine->machine_photos as $path)
                        <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener noreferrer" class="block overflow-hidden rounded-lg border border-slate-200 bg-slate-100 dark:border-white/10 dark:bg-slate-900/40">
                            <img src="{{ asset('storage/'.$path) }}" alt="Foto Mesin" class="h-24 w-full object-cover">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
            @if (! empty($public))
                Data master untuk Mesin {{ $machineNumber }} belum tersedia.
            @else
                Data master untuk Mesin {{ $machineNumber }} belum tersedia. Silakan isi di menu Asset → Data Mesin.
            @endif
        </div>
    @endif
</div>
