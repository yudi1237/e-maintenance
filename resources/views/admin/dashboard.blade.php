<x-app-layout>
    <div class="pb-16">
        {{-- Hero --}}
        <div id="dashboard-kiosk" class="border-b border-slate-200 bg-white/80 dark:border-white/5 dark:bg-slate-900/50">
            <div id="dashboard-kiosk-inner" class="em-shell flex flex-col py-8 sm:py-10">
                <div id="dashboard-hero" class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div>
                        <div class="dashboard-live-badge inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-medium text-cyan-700 dark:text-cyan-300 mb-4">
                            <span class="em-pulse-live relative pl-3">Live</span>
                            <span class="text-cyan-600 dark:text-cyan-400/80">Update setiap 5 detik</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ auth()->user()->isManagement() ? 'Dashboard Management' : 'Dashboard Mesin Injection' }}
                        </h1>
                        <p class="mt-2 text-slate-500 dark:text-slate-400 max-w-xl">
                            {{ auth()->user()->isManagement() ? 'Monitoring kartu mesin, status OP, dan grafik performa PT Dharma Poliplast' : 'PT Dharma Poliplast' }}
                        </p>
                    </div>
                    <div class="flex flex-col items-start gap-3 lg:items-end">
                        <div id="last-updated" class="text-sm text-slate-500 lg:text-right">
                            Memuat data...
                        </div>
                        @if (auth()->user()->seesDashboardOverview())
                            <button
                                type="button"
                                id="dashboard-fullscreen-btn"
                                class="em-qr-btn"
                                title="Tampilkan judul, grafik, dan peta mesin dalam layar penuh"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/></svg>
                                <span data-fullscreen-label>Fullscreen</span>
                            </button>
                        @endif
                    </div>
                </div>

                @if (auth()->user()->seesDashboardOverview())
                <div id="dashboard-map" class="mt-8">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Peta {{ $machineTotal }} Mesin Injection</h2>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex flex-wrap gap-4 text-xs text-slate-500 dark:text-slate-400">
                                <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-slate-600 border border-slate-500"></span> Normal</span>
                                <span class="flex items-center gap-2"><span class="em-machine-alert-dot h-3 w-3 rounded-full"></span> Ada OP</span>
                                <span class="flex items-center gap-2"><span class="em-machine-pending-dot h-3 w-3 rounded-full"></span> Belum Close Approval / Log Temuan</span>
                            </div>
                            @if ($machineTotal > 0 && auth()->user()->seesFullDashboard())
                                <a href="{{ route('admin.machines.qr.download-all') }}" class="em-qr-btn dashboard-fs-hide">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    Download semua QR
                                </a>
                            @endif
                        </div>
                    </div>

                    <div id="machine-grid" class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-2 sm:gap-3">
                        @forelse ($machines as $machine)
                            @php
                                $number = (int) $machine->machine_number;
                                $stat = $machineStats[$number] ?? ['op' => 0, 'findings' => 0, 'pm_due' => null, 'alert' => 'idle'];
                                $opCount = (int) ($stat['op'] ?? 0);
                                $findingCount = (int) ($stat['findings'] ?? 0);
                                $pmDue = $stat['pm_due'] ?? null;
                                $pmCategories = $preventiveToday[$number] ?? [];
                                $alert = $stat['alert'] ?? 'idle';
                                $alertClass = match ($alert) {
                                    'active' => 'em-machine-card--active',
                                    'pending' => 'em-machine-card--pending',
                                    default => 'em-machine-card--idle',
                                };
                            @endphp
                            <div
                               data-machine="{{ $number }}"
                               data-alert="{{ $alert }}"
                               title="{{ $machine->view_label }}"
                               class="machine-card em-machine-card relative {{ $alertClass }}">
                                <a href="{{ route('admin.machines.show', $number) }}" class="flex flex-col items-center justify-center w-full">
                                    <span class="machine-card-kicker text-[10px] uppercase tracking-widest text-slate-500">Mesin</span>
                                    <span class="em-machine-num text-2xl font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $number }}</span>
                                    <span class="machine-card-name mt-1 text-[10px] text-slate-500 dark:text-slate-400 max-w-full truncate">{{ $machine->view_label }}</span>
                                    <span class="machine-card-meta mt-2 w-full space-y-0.5 text-left text-[9px] leading-tight text-slate-600 dark:text-slate-400">
                                        <span class="block truncate">OP : <span class="machine-op-count font-semibold text-slate-800 dark:text-slate-200">{{ $opCount }}</span></span>
                                        <span class="block truncate">Log Temuan : <span class="machine-finding-count font-semibold text-slate-800 dark:text-slate-200">{{ $findingCount }}</span></span>
                                        <span class="block">
                                            <span class="block truncate">Planned PM Due :</span>
                                            <span class="machine-pm-due block font-semibold tabular-nums text-slate-800 dark:text-slate-200">{{ $pmDue === null ? '—' : $pmDue }}</span>
                                        </span>
                                    </span>
                                    <span class="preventive-badges mt-1 flex min-h-[1.1rem] flex-wrap justify-center gap-0.5" data-preventive-badges>
                                        @foreach ($pmCategories as $category)
                                            <span class="rounded px-1 py-0.5 text-[9px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">{{ $category }}</span>
                                        @endforeach
                                    </span>
                                </a>
                                @if (auth()->user()->seesFullDashboard())
                                <a href="{{ route('admin.machines.qr.download', $number) }}"
                                   title="Download QR Mesin {{ $number }}"
                                   class="dashboard-fs-hide absolute top-1.5 right-1.5 inline-flex h-7 w-7 items-center justify-center rounded-md bg-white/90 text-slate-600 shadow-sm ring-1 ring-slate-200 hover:bg-cyan-50 hover:text-cyan-700 dark:bg-slate-900/80 dark:text-slate-300 dark:ring-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-200">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </a>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full em-glass rounded-2xl p-6 text-center text-sm text-slate-400">
                                @if (auth()->user()->seesFullDashboard())
                                    Belum ada master mesin. Tambahkan data di menu <span class="font-medium text-slate-800 dark:text-slate-200">Master Data Mesin</span>.
                                @else
                                    Belum ada data mesin untuk ditampilkan.
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
                @endif

                {{-- Stats --}}
                <div id="dashboard-stats" class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="em-glass rounded-2xl p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Laporan Harian</p>
                            <div class="rounded-lg bg-cyan-500/20 p-2">
                                <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <p id="total-reports" class="mt-2 text-4xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $totalReportsToday }}</p>
                        <p id="today-label" class="text-xs text-slate-500 mt-1">Hari ini · {{ $todayLabel }}</p>
                    </div>
                    <div class="em-glass rounded-2xl p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Mesin Bermasalah</p>
                            <div class="rounded-lg bg-amber-500/20 p-2">
                                <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                        </div>
                        <p id="active-machines" class="mt-2 text-4xl font-bold text-amber-300 tabular-nums">{{ $activeMachinesCount }}</p>
                        <p class="text-xs text-slate-500 mt-1">Belum Approve ({{ $approvalRoleCaption }})</p>
                    </div>
                    <div class="em-glass rounded-2xl p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Laporan CLOSE</p>
                            <div class="rounded-lg bg-green-500/20 p-2">
                                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <p id="closed-reports" class="mt-2 text-4xl font-bold text-green-300 tabular-nums">{{ $closedReportsToday }}</p>
                            <p class="text-xs text-slate-500 mt-1">Status Approve ({{ $approvalRoleCaption }}) · hari ini</p>
                    </div>
                </div>

            @if (auth()->user()->seesDashboardOverview())
            {{-- Grafik --}}
            <div id="dashboard-charts" class="em-glass mt-8 rounded-2xl p-4">
                <div class="grid grid-cols-1 gap-4 {{ auth()->user()->seesFullDashboard() ? 'lg:grid-cols-4' : 'lg:grid-cols-2' }} lg:gap-3 lg:divide-x lg:divide-slate-200 dark:lg:divide-white/10">
                    @if (auth()->user()->seesFullDashboard())
                    <div class="min-w-0 lg:pr-3">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Performer Teknisi</h2>
                        <p id="performer-period" class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">OP diambil · {{ $charts['period_label'] }}</p>
                        <p id="performer-empty" class="text-xs text-slate-500 {{ $charts['performer']['labels'] === [] ? '' : 'hidden' }}">Belum ada data.</p>
                        <div class="relative h-36 {{ $charts['performer']['labels'] === [] ? 'hidden' : '' }}" id="performer-chart-wrap">
                            <canvas id="performer-chart"></canvas>
                        </div>
                    </div>
                    <div class="min-w-0 lg:px-3">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">MTTR Teknisi</h2>
                        <p id="sla-summary" class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">
                            MTTR per teknisi · tiket diambil → Close by teknisi · {{ $charts['period_label'] }}
                            @if ($charts['technician_mttr']['avg_minutes'] !== null)
                                · avg {{ $charts['technician_mttr']['avg_minutes'] }} mnt
                            @endif
                        </p>
                        <p id="sla-empty" class="text-xs text-slate-500 {{ $charts['technician_mttr']['labels'] === [] ? '' : 'hidden' }}">Belum ada data.</p>
                        <div class="relative h-36 {{ $charts['technician_mttr']['labels'] === [] ? 'hidden' : '' }}" id="sla-chart-wrap">
                            <canvas id="sla-chart"></canvas>
                        </div>
                    </div>
                    @endif
                    <div class="min-w-0 {{ auth()->user()->seesFullDashboard() ? 'lg:px-3' : 'lg:pr-3' }}">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Grafik Line Stop</h2>
                        <p id="machine-period" class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">MTTR per mesin · OP dibuat → Close by teknisi · {{ $charts['period_label'] }}</p>
                        <p id="machine-chart-empty" class="text-xs text-slate-500 {{ $charts['machines']['labels'] === [] ? '' : 'hidden' }}">Belum ada data.</p>
                        <div class="relative h-36 {{ $charts['machines']['labels'] === [] ? 'hidden' : '' }}" id="machine-chart-wrap">
                            <canvas id="machine-chart"></canvas>
                        </div>
                    </div>
                    <div class="min-w-0 lg:pl-3">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">PM Status</h2>
                            <div class="flex items-center gap-1">
                                <label class="sr-only" for="pm-year">Tahun</label>
                                <select id="pm-year" class="em-pm-filter min-w-[4.75rem] rounded-md border px-2 py-1 text-[11px] font-semibold">
                                    @foreach ($pmYears as $yearOption)
                                        <option value="{{ $yearOption }}" @selected((int) $pmYear === (int) $yearOption)>{{ $yearOption }}</option>
                                    @endforeach
                                </select>
                                <label class="sr-only" for="pm-month">Bulan</label>
                                <select id="pm-month" class="em-pm-filter min-w-[6.25rem] rounded-md border px-2 py-1 text-[11px] font-semibold">
                                    <option value="" @selected($pmMonth === null)>Semua</option>
                                    @foreach ($pmMonths as $monthNumber => $monthName)
                                        <option value="{{ $monthNumber }}" @selected((int) $pmMonth === (int) $monthNumber)>{{ $monthName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-1 flex gap-3 text-[10px] text-slate-500 dark:text-slate-400">
                            <span>Target <span id="pm-target" class="font-semibold tabular-nums text-slate-800 dark:text-slate-200">{{ $pmStatus['target'] }}</span></span>
                            <span>Actual <span id="pm-actual" class="font-semibold tabular-nums text-slate-800 dark:text-slate-200">{{ $pmStatus['actual'] }}</span></span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="relative h-28 w-28 shrink-0">
                                <canvas id="pm-status-chart"></canvas>
                                <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                                    <span id="pm-status-percent" class="text-xs font-bold tabular-nums text-slate-800 dark:text-white">{{ $pmStatus['percent_label'] }}</span>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1 space-y-0.5 text-[11px]">
                                <div class="flex items-center justify-between gap-2" title="Pekerjaan yang direncanakan">
                                    <span class="font-medium text-violet-800 dark:text-violet-300">Planned</span>
                                    <span id="pm-planned" class="em-pm-count inline-flex min-w-[2rem] justify-center rounded px-1.5 py-0.5 font-semibold tabular-nums">{{ $pmStatus['planned'] }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2" title="Sisa/tunggakan dari periode sebelumnya">
                                    <span class="font-medium text-teal-700 dark:text-teal-300">Pending</span>
                                    <span id="pm-pending" class="em-pm-count inline-flex min-w-[2rem] justify-center rounded px-1.5 py-0.5 font-semibold tabular-nums">{{ $pmStatus['pending'] }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2" title="Pekerjaan telah selesai dilaksanakan">
                                    <span class="font-medium text-lime-700 dark:text-lime-300">Completed</span>
                                    <span id="pm-completed" class="em-pm-count inline-flex min-w-[2rem] justify-center rounded px-1.5 py-0.5 font-semibold tabular-nums">{{ $pmStatus['completed'] }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2" title="Pekerjaan sisa/belum dikerjakan">
                                    <span class="font-medium text-rose-700 dark:text-rose-300">Remaining</span>
                                    <span id="pm-remaining" class="em-pm-count inline-flex min-w-[2rem] justify-center rounded px-1.5 py-0.5 font-semibold tabular-nums">{{ $pmStatus['remaining'] }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2" title="Pemeliharaan tidak terencana di luar schedule">
                                    <span class="font-medium text-indigo-700 dark:text-indigo-300">Unplanned</span>
                                    <span id="pm-unplanned" class="em-pm-count inline-flex min-w-[2rem] justify-center rounded px-1.5 py-0.5 font-semibold tabular-nums">{{ $pmStatus['unplanned'] }}</span>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </div>
            @endif
            </div>
        </div>

        <div id="dashboard-rest" class="em-shell -mt-4 space-y-8">
            <x-alert />

            {{-- Semua Laporan --}}
            <div class="em-glass rounded-2xl overflow-hidden">
                <div class="px-4 py-4 sm:px-6 border-b border-slate-200 dark:border-white/10">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Semua Laporan Masuk</h2>
                            @if (auth()->user()->seesDashboardOverview())
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    {{ auth()->user()->seesFullDashboard() ? 'Filter daftar tiket di bawah, atau Export Laporan Excel sesuai periode dan mesin.' : 'Filter daftar tiket untuk memantau status mesin dan laporan.' }}
                                </p>
                            @endif
                        </div>
                        @if (auth()->user()->seesDashboardOverview())
                            <form method="GET" action="{{ route('admin.dashboard') }}" class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-5 xl:w-auto xl:max-w-4xl">
                                <div>
                                    <label for="export_start_date" class="em-label">Periode Mulai</label>
                                    <input id="export_start_date" name="start_date" type="date" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ old('start_date', $exportStartDate) }}">
                                </div>
                                <div>
                                    <label for="export_end_date" class="em-label">Periode Akhir</label>
                                    <input id="export_end_date" name="end_date" type="date" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ old('end_date', $exportEndDate) }}">
                                </div>
                                <div>
                                    <label for="export_machine_number" class="em-label">Filter Mesin</label>
                                    <select id="export_machine_number" name="machine_number" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm">
                                        <option value="">Semua Mesin</option>
                                        @foreach ($machineNumbers as $num)
                                            <option value="{{ $num }}" @selected((string) old('machine_number', $ticketMachineNumber) === (string) $num)>Mesin {{ $num }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="em-btn-secondary w-full min-h-[40px] justify-center px-3">Filter</button>
                                </div>
                                @if (auth()->user()->seesFullDashboard())
                                <div class="flex items-end">
                                    <button type="submit" formaction="{{ route('admin.reports.export') }}" class="em-qr-btn w-full min-h-[40px] justify-center px-3">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Download Excel
                                    </button>
                                </div>
                                @endif
                            </form>
                        @endif
                    </div>
                    @if (auth()->user()->seesFullDashboard() && $errors->has('export'))
                        <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200">
                            {{ $errors->first('export') }}
                        </div>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                                <th class="px-4 py-3">Stop</th>
                                <th class="px-4 py-3">Tiket OP</th>
                                <th class="px-4 py-3">Mesin</th>
                                <th class="px-4 py-3">Nama Barang</th>
                                <th class="px-4 py-3">Masalah</th>
                                <th class="px-4 py-3">Operator</th>
                                <th class="px-4 py-3">Teknisi</th>
                                <th class="px-4 py-3">Form 2</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Approval</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse ($tickets as $ticket)
                                @php
                                    $report = $ticket->maintenanceReport;
                                @endphp
                                <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $ticket->stop_date_time_label }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap dark:text-slate-100">#{{ $ticket->id }}</td>
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">
                                        #{{ $ticket->machine_number }}
                                        <span class="block text-xs text-slate-500 max-w-[160px] truncate">{{ $ticket->machine_name_display }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 max-w-[180px] truncate dark:text-slate-300" title="{{ $ticket->item_name }}">{{ $ticket->item_name }}</td>
                                    <td class="px-4 py-3 text-slate-600 max-w-[220px] truncate dark:text-slate-300" title="{{ $ticket->problem }}">{{ $ticket->problem }}</td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">
                                        {{ $ticket->operator_name }}
                                        <span class="block text-xs text-slate-500">NPK {{ $ticket->npk }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">
                                        @if ($ticket->pic_name !== 'Belum diambil')
                                            {{ $ticket->pic_name }}
                                        @else
                                            <span class="text-slate-400">Belum diambil</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($report?->isStep2Completed())
                                            <span class="text-emerald-400 text-xs">✓ Lengkap</span>
                                        @else
                                            <span class="text-amber-400 text-xs">Belum</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($report)
                                            <x-admin.report-status-form :report="$report" :status-labels="$statusLabels" compact />
                                        @else
                                            <span class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold {{ $ticket->status_badge_class }}" style="color: {{ \App\Support\RepairStatus::legendColor($ticket->status ?? \App\Support\RepairStatus::OPEN) }};">
                                                {{ $ticket->status_label }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.approval-progress :status="$report->status_perbaikan ?? $ticket->status" :report="$report" compact />
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.operator-reports.show', $ticket) }}" class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium">Detail →</a>
                                            @if (auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('admin.operator-reports.destroy', $ticket) }}" class="inline" onsubmit="return confirm('Hapus tiket OP #{{ $ticket->id }} beserta laporan perbaikannya?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-500 text-xs font-medium">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-12 text-center text-slate-500">Tidak ada tiket OP yang sedang berjalan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($tickets->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $tickets->links() }}</div>
                @endif
            </div>

            @if (auth()->user()->seesPreventiveResults())
            {{-- Preventive Mesin --}}
            <div class="em-glass rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-white/10 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Preventive Mesin</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Hasil preventive sesuai mesin yang dipilih teknisi. Approval: Injection lalu Maintenance.</p>
                    </div>
                    @if (auth()->user()->seesFullDashboard())
                        <a href="{{ route('admin.preventive.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">Lihat semua →</a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Mesin</th>
                                <th class="px-4 py-3">Nama Mesin</th>
                                <th class="px-4 py-3">Kategori Preventive</th>
                                <th class="px-4 py-3">Tipe Preventive</th>
                                <th class="px-4 py-3">Shift</th>
                                <th class="px-4 py-3">PIC</th>
                                <th class="px-4 py-3">Hasil</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Approval</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse ($preventiveReports as $report)
                                <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $report->work_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">
                                        <a href="{{ route('admin.machines.show', $report->machine_number) }}" class="font-medium text-cyan-600 hover:text-cyan-500 dark:text-cyan-400">#{{ $report->machine_number }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 max-w-[180px] truncate dark:text-slate-200">{{ $report->machine_name_display }}</td>
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $report->schedule_group_label }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $report->category_label }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">{{ $report->work_shift_label }}</td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">{{ $report->pic_name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $report->result_badge_class }}">
                                            {{ $report->result_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.preventive-status-form :report="$report" compact />
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.approval-progress :status="$report->status" variant="preventive" compact />
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.preventive.export', $report) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium">Download</a>
                                            <a href="{{ route('admin.preventive.show', $report) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium">Detail →</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-12 text-center text-slate-500">Belum ada hasil preventive mesin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($preventiveReports->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $preventiveReports->links() }}</div>
                @endif
            </div>
            @endif

            @if (auth()->user()->seesDashboardOverview())
            {{-- Log Temuan --}}
            <div class="em-glass rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-white/10 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Log Temuan</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Temuan mesin beserta due date dan riwayat catatan status</p>
                    </div>
                    @if (auth()->user()->seesFullDashboard())
                        <a href="{{ route('admin.finding-recaps.index') }}" class="text-sm font-medium text-cyan-600 hover:text-cyan-500 dark:text-cyan-400">Kelola log temuan →</a>
                    @endif
                </div>
                <x-admin.finding-recap-table :recaps="$findingRecaps" :status-labels="$findingRecapStatusLabels" paginated :allow-status-change="auth()->user()->seesFullDashboard()" />
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <style>
        .em-machine-card--active {
            border-color: #ef4444;
            background-image: linear-gradient(to bottom right, #fef2f2, #fecaca);
            animation: em-machine-alert 1.1s ease-in-out infinite;
        }
        html.dark .em-machine-card--active {
            border-color: #f87171;
            background-image: linear-gradient(to bottom right, rgba(127, 29, 29, 0.55), rgba(153, 27, 27, 0.4));
            animation-name: em-machine-alert-dark;
        }
        .em-machine-card--active .em-machine-num {
            color: #991b1b;
        }
        html.dark .em-machine-card--active .em-machine-num {
            color: #fff7f7;
        }
        .em-machine-card--active .machine-card-kicker,
        .em-machine-card--active .machine-card-name,
        .em-machine-card--active .machine-card-meta {
            color: #7f1d1d;
            font-weight: 600;
        }
        html.dark .em-machine-card--active .machine-card-kicker,
        html.dark .em-machine-card--active .machine-card-name,
        html.dark .em-machine-card--active .machine-card-meta {
            color: #fecaca;
        }
        .em-machine-card--active .machine-op-count,
        .em-machine-card--active .machine-finding-count,
        .em-machine-card--active .machine-pm-due {
            color: #450a0a;
            font-weight: 700;
        }
        html.dark .em-machine-card--active .machine-op-count,
        html.dark .em-machine-card--active .machine-finding-count,
        html.dark .em-machine-card--active .machine-pm-due {
            color: #ffffff;
        }
        .em-machine-alert-dot {
            background-color: #ef4444;
            animation: em-machine-alert 1.1s ease-in-out infinite;
        }
        .em-machine-card--pending {
            border-color: #eab308;
            background-image: linear-gradient(to bottom right, #fefce8, #fde68a);
            animation: em-machine-pending 1.1s ease-in-out infinite;
        }
        html.dark .em-machine-card--pending {
            border-color: #facc15;
            background-image: linear-gradient(to bottom right, rgba(113, 63, 18, 0.55), rgba(133, 77, 14, 0.4));
            animation-name: em-machine-pending-dark;
        }
        .em-machine-card--pending .em-machine-num {
            color: #854d0e;
        }
        html.dark .em-machine-card--pending .em-machine-num {
            color: #fef9c3;
        }
        .em-machine-card--pending .machine-card-kicker,
        .em-machine-card--pending .machine-card-name,
        .em-machine-card--pending .machine-card-meta {
            color: #713f12;
            font-weight: 600;
        }
        html.dark .em-machine-card--pending .machine-card-kicker,
        html.dark .em-machine-card--pending .machine-card-name,
        html.dark .em-machine-card--pending .machine-card-meta {
            color: #fde68a;
        }
        .em-machine-card--pending .machine-op-count,
        .em-machine-card--pending .machine-finding-count,
        .em-machine-card--pending .machine-pm-due {
            color: #422006;
            font-weight: 700;
        }
        html.dark .em-machine-card--pending .machine-op-count,
        html.dark .em-machine-card--pending .machine-finding-count,
        html.dark .em-machine-card--pending .machine-pm-due {
            color: #ffffff;
        }
        .em-machine-pending-dot {
            background-color: #eab308;
            animation: em-machine-pending 1.1s ease-in-out infinite;
        }
        @keyframes em-machine-alert {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.55);
                border-color: #ef4444;
                background-image: linear-gradient(to bottom right, #fef2f2, #fecaca);
            }
            50% {
                box-shadow: 0 0 16px 2px rgba(239, 68, 68, 0.8);
                border-color: #b91c1c;
                background-image: linear-gradient(to bottom right, #fee2e2, #fca5a5);
            }
        }
        @keyframes em-machine-alert-dark {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(248, 113, 113, 0.45);
                border-color: #f87171;
                background-image: linear-gradient(to bottom right, rgba(127, 29, 29, 0.55), rgba(153, 27, 27, 0.4));
            }
            50% {
                box-shadow: 0 0 16px 2px rgba(248, 113, 113, 0.75);
                border-color: #fecaca;
                background-image: linear-gradient(to bottom right, rgba(185, 28, 28, 0.7), rgba(127, 29, 29, 0.55));
            }
        }
        @keyframes em-machine-pending {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.5);
                border-color: #eab308;
                background-image: linear-gradient(to bottom right, #fefce8, #fde68a);
            }
            50% {
                box-shadow: 0 0 16px 2px rgba(234, 179, 8, 0.85);
                border-color: #a16207;
                background-image: linear-gradient(to bottom right, #fef08a, #facc15);
            }
        }
        @keyframes em-machine-pending-dark {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(250, 204, 21, 0.4);
                border-color: #facc15;
                background-image: linear-gradient(to bottom right, rgba(113, 63, 18, 0.55), rgba(133, 77, 14, 0.4));
            }
            50% {
                box-shadow: 0 0 16px 2px rgba(250, 204, 21, 0.75);
                border-color: #fde68a;
                background-image: linear-gradient(to bottom right, rgba(161, 98, 7, 0.7), rgba(113, 63, 18, 0.55));
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .em-machine-card--active,
            .em-machine-card--pending,
            .em-machine-alert-dot,
            .em-machine-pending-dot {
                animation: none;
            }
        }
        #dashboard-kiosk:fullscreen,
        #dashboard-kiosk:-webkit-full-screen {
            background-color: #f8fafc;
            width: 100%;
            height: 100%;
            overflow: hidden;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        html.dark #dashboard-kiosk:fullscreen,
        html.dark #dashboard-kiosk:-webkit-full-screen {
            background-color: #020617;
        }
        #dashboard-kiosk:fullscreen #dashboard-kiosk-inner,
        #dashboard-kiosk:-webkit-full-screen #dashboard-kiosk-inner {
            max-width: none;
            width: 100%;
            height: 100%;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.65rem 0.9rem 0.75rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-hero,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero {
            order: 1;
            flex: 0 0 auto;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts {
            order: 2;
            flex: 0 0 26vh;
            max-height: 240px;
            min-height: 150px;
            margin-top: 0;
            overflow: hidden;
            padding: 0.45rem 0.65rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-map,
        #dashboard-kiosk:-webkit-full-screen #dashboard-map {
            order: 3;
            flex: 1 1 auto;
            min-height: 0;
            margin-top: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        #dashboard-kiosk:fullscreen #dashboard-stats,
        #dashboard-kiosk:-webkit-full-screen #dashboard-stats,
        #dashboard-kiosk:fullscreen .dashboard-live-badge,
        #dashboard-kiosk:-webkit-full-screen .dashboard-live-badge,
        #dashboard-kiosk:fullscreen #last-updated,
        #dashboard-kiosk:-webkit-full-screen #last-updated,
        #dashboard-kiosk:fullscreen .dashboard-fs-hide,
        #dashboard-kiosk:-webkit-full-screen .dashboard-fs-hide {
            display: none !important;
        }
        #dashboard-kiosk:fullscreen #dashboard-hero h1,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero h1 {
            font-size: 1.35rem;
            line-height: 1.2;
        }
        #dashboard-kiosk:fullscreen #dashboard-hero p,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero p {
            margin-top: 0.15rem;
            font-size: 0.85rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-hero .em-qr-btn,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero .em-qr-btn {
            min-height: 2.25rem;
            padding: 0.35rem 0.75rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts .grid,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.5rem;
            height: 100%;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts h2,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts h2 {
            font-size: 0.72rem;
        }
        #dashboard-kiosk:fullscreen #performer-period,
        #dashboard-kiosk:-webkit-full-screen #performer-period,
        #dashboard-kiosk:fullscreen #sla-summary,
        #dashboard-kiosk:-webkit-full-screen #sla-summary,
        #dashboard-kiosk:fullscreen #machine-period,
        #dashboard-kiosk:-webkit-full-screen #machine-period {
            display: none;
        }
        #dashboard-kiosk:fullscreen #performer-chart-wrap,
        #dashboard-kiosk:-webkit-full-screen #performer-chart-wrap,
        #dashboard-kiosk:fullscreen #sla-chart-wrap,
        #dashboard-kiosk:-webkit-full-screen #sla-chart-wrap,
        #dashboard-kiosk:fullscreen #machine-chart-wrap,
        #dashboard-kiosk:-webkit-full-screen #machine-chart-wrap {
            height: calc(100% - 1.35rem) !important;
            min-height: 0;
        }
        #dashboard-kiosk:fullscreen #pm-status-chart,
        #dashboard-kiosk:-webkit-full-screen #pm-status-chart {
            max-height: 5.5rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts .h-28,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts .h-28 {
            height: 5.5rem;
            width: 5.5rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-map > div:first-child,
        #dashboard-kiosk:-webkit-full-screen #dashboard-map > div:first-child {
            margin-bottom: 0.4rem;
            flex: 0 0 auto;
        }
        #dashboard-kiosk:fullscreen #dashboard-map h2,
        #dashboard-kiosk:-webkit-full-screen #dashboard-map h2 {
            font-size: 0.95rem;
        }
        #dashboard-kiosk:fullscreen #machine-grid,
        #dashboard-kiosk:-webkit-full-screen #machine-grid {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            gap: 0.35rem;
            grid-template-columns: repeat(14, minmax(0, 1fr));
            grid-auto-rows: minmax(0, 1fr);
            align-content: stretch;
        }
        #dashboard-kiosk:fullscreen .machine-card,
        #dashboard-kiosk:-webkit-full-screen .machine-card {
            min-height: 0;
            height: 100%;
            padding: 0.28rem 0.32rem;
            transform: none !important;
            overflow: hidden;
        }
        #dashboard-kiosk:fullscreen .machine-card-kicker,
        #dashboard-kiosk:-webkit-full-screen .machine-card-kicker {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            line-height: 1.1;
            color: #475569;
        }
        html.dark #dashboard-kiosk:fullscreen .machine-card-kicker,
        html.dark #dashboard-kiosk:-webkit-full-screen .machine-card-kicker {
            color: #cbd5e1;
        }
        html.dark #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-kicker,
        html.dark #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-kicker {
            color: #fecaca;
        }
        #dashboard-kiosk:fullscreen .em-machine-num,
        #dashboard-kiosk:-webkit-full-screen .em-machine-num {
            font-size: 1.15rem;
            margin-top: 0;
            line-height: 1.1;
        }
        #dashboard-kiosk:fullscreen .machine-card-name,
        #dashboard-kiosk:-webkit-full-screen .machine-card-name {
            font-size: 10px;
            font-weight: 600;
            margin-top: 0.1rem;
            color: #334155;
        }
        #dashboard-kiosk:fullscreen .machine-card-meta,
        #dashboard-kiosk:-webkit-full-screen .machine-card-meta {
            margin-top: 0.15rem;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            color: #334155;
        }
        #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-name,
        #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-name,
        #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-meta,
        #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-meta {
            color: #7f1d1d;
        }
        html.dark #dashboard-kiosk:fullscreen .machine-card-name,
        html.dark #dashboard-kiosk:-webkit-full-screen .machine-card-name,
        html.dark #dashboard-kiosk:fullscreen .machine-card-meta,
        html.dark #dashboard-kiosk:-webkit-full-screen .machine-card-meta {
            color: #e2e8f0;
        }
        html.dark #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-name,
        html.dark #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-name,
        html.dark #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-meta,
        html.dark #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-meta {
            color: #fecaca;
        }
        #dashboard-kiosk:fullscreen .preventive-badges,
        #dashboard-kiosk:-webkit-full-screen .preventive-badges {
            min-height: 0;
            margin-top: 0.1rem;
        }
        select.em-pm-filter {
            background-color: #ffffff;
            color: #0f172a;
            border-color: #cbd5e1;
            color-scheme: light;
        }
        html.dark select.em-pm-filter {
            background-color: #1e293b;
            color: #f8fafc;
            border-color: #64748b;
            color-scheme: dark;
        }
        select.em-pm-filter option {
            background-color: #ffffff;
            color: #0f172a;
        }
        html.dark select.em-pm-filter option {
            background-color: #1e293b;
            color: #f8fafc;
        }
        #pm-planned.em-pm-count { background-color: #ede9fe; color: #5b21b6 !important; }
        #pm-pending.em-pm-count { background-color: #ccfbf1; color: #0f766e !important; }
        #pm-completed.em-pm-count { background-color: #d9f99d; color: #3f6212 !important; }
        #pm-remaining.em-pm-count { background-color: #ffe4e6; color: #9f1239 !important; }
        #pm-unplanned.em-pm-count { background-color: #e0e7ff; color: #3730a3 !important; }
        html.dark #pm-planned.em-pm-count { background-color: #5b21b6; color: #f5f3ff !important; }
        html.dark #pm-pending.em-pm-count { background-color: #0f766e; color: #ccfbf1 !important; }
        html.dark #pm-completed.em-pm-count { background-color: #3f6212; color: #ecfccb !important; }
        html.dark #pm-remaining.em-pm-count { background-color: #9f1239; color: #ffe4e6 !important; }
        html.dark #pm-unplanned.em-pm-count { background-color: #3730a3; color: #e0e7ff !important; }
    </style>
    @if (auth()->user()->seesDashboardOverview())
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endif
    <script>
        const statsUrl = @json(route('admin.dashboard.stats'));
        const fullDashboard = @json(auth()->user()->seesDashboardOverview());
        const lastUpdatedEl = document.getElementById('last-updated');
        const totalReportsEl = document.getElementById('total-reports');
        const activeMachinesEl = document.getElementById('active-machines');
        const closedReportsEl = document.getElementById('closed-reports');
        const todayLabelEl = document.getElementById('today-label');
        const slaSummaryEl = document.getElementById('sla-summary');
        let chartsData = @json($charts);
        let pmStatusData = @json($pmStatus);

        let performerChart = null;
        let slaChart = null;
        let machineChart = null;
        let pmStatusChart = null;

        if (fullDashboard && typeof Chart !== 'undefined') {
        const isDark = () => document.documentElement.classList.contains('dark');
        const tickColor = () => isDark() ? '#94a3b8' : '#64748b';
        const gridColor = () => isDark() ? 'rgba(148, 163, 184, 0.12)' : 'rgba(148, 163, 184, 0.2)';

        Chart.defaults.font.family = "'DM Sans', Figtree, ui-sans-serif, system-ui, sans-serif";
        Chart.defaults.color = tickColor();

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 8, boxHeight: 8, padding: 8, font: { size: 10 }, color: tickColor() },
                },
            },
            scales: {
                x: { ticks: { font: { size: 10 }, color: tickColor(), maxRotation: 0 }, grid: { display: false } },
                y: { ticks: { font: { size: 10 }, precision: 0, color: tickColor() }, grid: { color: gridColor() } },
            },
        };

        if (document.getElementById('performer-chart')) {
        performerChart = new Chart(document.getElementById('performer-chart'), {
            type: 'bar',
            data: {
                labels: chartsData.performer.labels,
                datasets: [
                    { label: 'On Progress', data: chartsData.performer.on_progress, backgroundColor: '#facc15', borderRadius: 4, stack: 'op' },
                    { label: 'Close teknisi', data: chartsData.performer.closed_technician, backgroundColor: '#22d3ee', borderRadius: 4, stack: 'op' },
                    { label: 'Approve Admin', data: chartsData.performer.closed_admin, backgroundColor: '#22c55e', borderRadius: 4, stack: 'op' },
                ],
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                scales: {
                    x: { stacked: true, beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: tickColor() }, grid: { color: gridColor() } },
                    y: { stacked: true, ticks: { font: { size: 10 }, color: tickColor() }, grid: { display: false } },
                },
            },
        });
        }

        if (document.getElementById('sla-chart')) {
        slaChart = new Chart(document.getElementById('sla-chart'), {
            type: 'bar',
            data: {
                labels: chartsData.technician_mttr.labels,
                datasets: [{
                    label: 'MTTR (menit)',
                    data: chartsData.technician_mttr.values,
                    backgroundColor: '#14b8a6',
                    borderRadius: 4,
                }],
            },
            options: {
                ...commonOptions,
                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, padding: 8, font: { size: 10 }, color: tickColor() } } },
                scales: {
                    x: { ticks: { font: { size: 10 }, color: tickColor(), maxRotation: 0 }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: tickColor() }, grid: { color: gridColor() } },
                },
            },
        });
        }

        machineChart = new Chart(document.getElementById('machine-chart'), {
            type: 'bar',
            data: {
                labels: chartsData.machines.labels,
                datasets: [{
                    label: 'MTTR (menit)',
                    data: chartsData.machines.values,
                    backgroundColor: '#06b6d4',
                    borderRadius: 4,
                }],
            },
            options: {
                ...commonOptions,
                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, padding: 8, font: { size: 10 }, color: tickColor() } } },
                scales: {
                    x: { ticks: { font: { size: 10 }, color: tickColor(), maxRotation: 0 }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: tickColor() }, grid: { color: gridColor() } },
                },
            },
        });

        pmStatusChart = new Chart(document.getElementById('pm-status-chart'), {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Remaining'],
                datasets: [{
                    data: pmDonutValues(pmStatusData),
                    backgroundColor: ['#84cc16', '#d4d4d4'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': ' + ctx.parsed;
                            },
                        },
                    },
                },
            },
        });
        }

        function pmDonutValues(status) {
            const completed = Number(status?.completed || 0);
            const remaining = Number(status?.remaining || 0);
            if (completed === 0 && remaining === 0) {
                return [0, 1];
            }
            return [completed, remaining];
        }

        function applyPmStatus(status) {
            if (!status) return;
            pmStatusData = status;
            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = String(value);
            };
            setText('pm-target', status.target);
            setText('pm-actual', status.actual);
            setText('pm-planned', status.planned);
            setText('pm-pending', status.pending);
            setText('pm-completed', status.completed);
            setText('pm-remaining', status.remaining);
            setText('pm-unplanned', status.unplanned);
            setText('pm-status-percent', status.percent_label);
            if (pmStatusChart) {
                pmStatusChart.data.datasets[0].data = pmDonutValues(status);
                pmStatusChart.update('none');
            }
        }

        function pmStatusQuery() {
            const year = document.getElementById('pm-year')?.value || '';
            const month = document.getElementById('pm-month')?.value ?? '';
            return '?pm_year=' + encodeURIComponent(year) + '&pm_month=' + encodeURIComponent(month);
        }

        function toggleChart(wrapId, emptyId, hasData) {
            document.getElementById(wrapId)?.classList.toggle('hidden', !hasData);
            document.getElementById(emptyId)?.classList.toggle('hidden', hasData);
        }

        function applyChart(chart, labels, datasetValues) {
            if (!chart) return;
            chart.data.labels = labels;
            datasetValues.forEach((values, index) => {
                if (chart.data.datasets[index]) {
                    chart.data.datasets[index].data = values;
                }
            });
            chart.update('none');
        }

        function applyCharts(data) {
            if (!fullDashboard) return;
            chartsData = data;
            const machineHasData = data.machines.labels.length > 0;

            if (performerChart) {
                toggleChart('performer-chart-wrap', 'performer-empty', data.performer.labels.length > 0);
                applyChart(performerChart, data.performer.labels, [
                    data.performer.on_progress,
                    data.performer.closed_technician,
                    data.performer.closed_admin,
                ]);
            }
            if (slaChart) {
                toggleChart('sla-chart-wrap', 'sla-empty', data.technician_mttr.labels.length > 0);
                applyChart(slaChart, data.technician_mttr.labels, [data.technician_mttr.values]);
            }
            toggleChart('machine-chart-wrap', 'machine-chart-empty', machineHasData);
            applyChart(machineChart, data.machines.labels, [data.machines.values]);

            if (slaSummaryEl && data.period_label) {
                const avg = data.technician_mttr.avg_minutes === null ? '' : ' · avg ' + data.technician_mttr.avg_minutes + ' mnt';
                slaSummaryEl.textContent = 'MTTR per teknisi · tiket diambil → Close by teknisi · ' + data.period_label + avg;
            }
            const performerPeriod = document.getElementById('performer-period');
            const machinePeriod = document.getElementById('machine-period');
            if (data.period_label && performerPeriod) {
                performerPeriod.textContent = 'OP diambil · ' + data.period_label;
            }
            if (data.period_label && machinePeriod) {
                machinePeriod.textContent = 'MTTR per mesin · OP dibuat → Close by teknisi · ' + data.period_label;
            }
        }

        function applyPreventiveBadges(card, categories) {
            const wrap = card.querySelector('[data-preventive-badges]');
            if (!wrap) return;
            wrap.innerHTML = (categories || []).map(function (category) {
                const label = document.createElement('span');
                label.className = 'rounded px-1 py-0.5 text-[9px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300';
                label.textContent = category;
                return label.outerHTML;
            }).join('');
        }

        function machineStat(stat) {
            if (stat && typeof stat === 'object' && !Array.isArray(stat)) {
                const alert = stat.alert === 'active' || stat.alert === 'pending' ? stat.alert : 'idle';
                return {
                    op: Number(stat.op || 0),
                    findings: Number(stat.findings || 0),
                    pm_due: stat.pm_due == null || stat.pm_due === '' ? null : String(stat.pm_due),
                    alert: alert,
                };
            }
            const n = Number(stat || 0);
            return { op: n, findings: 0, pm_due: null, alert: n > 0 ? 'active' : 'idle' };
        }

        function applyCardState(card, stat) {
            const parsed = machineStat(stat);
            const opEl = card.querySelector('.machine-op-count');
            const findingEl = card.querySelector('.machine-finding-count');
            const pmEl = card.querySelector('.machine-pm-due');
            if (opEl) opEl.textContent = String(parsed.op);
            if (findingEl) findingEl.textContent = String(parsed.findings);
            if (pmEl) pmEl.textContent = parsed.pm_due ? parsed.pm_due : '—';
            card.dataset.alert = parsed.alert;
            card.classList.remove('em-machine-card--idle', 'em-machine-card--active', 'em-machine-card--pending');
            card.classList.add(
                parsed.alert === 'active'
                    ? 'em-machine-card--active'
                    : (parsed.alert === 'pending' ? 'em-machine-card--pending' : 'em-machine-card--idle')
            );
        }

        async function refreshStats() {
            try {
                const response = await fetch(statsUrl + pmStatusQuery(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;

                const data = await response.json();
                const machines = data.machines;

                totalReportsEl.textContent = data.total;
                activeMachinesEl.textContent = data.active_machines;
                closedReportsEl.textContent = data.closed;
                if (todayLabelEl && data.today_label) {
                    todayLabelEl.textContent = 'Hari ini · ' + data.today_label;
                }
                lastUpdatedEl.innerHTML = '<span class="text-cyan-600 dark:text-cyan-400/80">●</span> Terakhir diperbarui: <span class="text-slate-700 dark:text-slate-300">' + data.updated_at + '</span>';

                document.querySelectorAll('.machine-card').forEach(card => {
                    const num = card.dataset.machine;
                    const stat = machines[num] ?? machines[Number(num)] ?? { op: 0, findings: 0, pm_due: null, alert: 'idle' };
                    applyCardState(card, stat);
                    applyPreventiveBadges(card, (data.preventive_today && data.preventive_today[num]) || []);
                });

                if (data.charts) {
                    applyCharts(data.charts);
                }
                if (data.pm_status) {
                    applyPmStatus(data.pm_status);
                }
            } catch (e) {
                console.error('Gagal memuat statistik', e);
            }
        }

        refreshStats();
        setInterval(refreshStats, 5000);
        document.getElementById('pm-year')?.addEventListener('change', refreshStats);
        document.getElementById('pm-month')?.addEventListener('change', refreshStats);

        const kiosk = document.getElementById('dashboard-kiosk');
        const fullscreenBtn = document.getElementById('dashboard-fullscreen-btn');
        const fullscreenLabel = fullscreenBtn?.querySelector('[data-fullscreen-label]');

        function fullscreenElement() {
            return document.fullscreenElement || document.webkitFullscreenElement || null;
        }

        function resizeDashboardCharts() {
            [performerChart, slaChart, machineChart, pmStatusChart].forEach((chart) => {
                if (chart) {
                    chart.resize();
                }
            });
        }

        function fitKioskMapGrid() {
            const grid = document.getElementById('machine-grid');
            if (!grid) {
                return;
            }
            const on = fullscreenElement() === kiosk;
            if (!on) {
                grid.style.gridTemplateColumns = '';
                grid.style.gridTemplateRows = '';
                return;
            }
            const count = grid.querySelectorAll('.machine-card').length;
            if (count === 0) {
                return;
            }
            const width = grid.clientWidth;
            const height = grid.clientHeight;
            if (width < 80 || height < 80) {
                return;
            }
            let cols = Math.max(8, Math.min(14, Math.round(Math.sqrt(count * (width / Math.max(height, 1))))));
            let rows = Math.ceil(count / cols);
            while (rows > 5 && cols < 16) {
                cols += 1;
                rows = Math.ceil(count / cols);
            }
            if (height / rows < 68 && cols < 16) {
                cols = Math.min(16, cols + 1);
                rows = Math.ceil(count / cols);
            }
            grid.style.gridTemplateColumns = `repeat(${cols}, minmax(0, 1fr))`;
            grid.style.gridTemplateRows = `repeat(${rows}, minmax(0, 1fr))`;
        }

        function syncFullscreenLayout() {
            const on = fullscreenElement() === kiosk;
            if (fullscreenLabel) {
                fullscreenLabel.textContent = on ? 'Keluar' : 'Fullscreen';
            }
            if (fullscreenBtn) {
                fullscreenBtn.title = on
                    ? 'Keluar dari layar penuh'
                    : 'Tampilkan judul, grafik, dan peta mesin dalam layar penuh';
            }
            window.requestAnimationFrame(() => {
                fitKioskMapGrid();
                resizeDashboardCharts();
            });
            window.setTimeout(() => {
                fitKioskMapGrid();
                resizeDashboardCharts();
            }, 120);
        }

        async function toggleDashboardFullscreen() {
            if (!kiosk) {
                return;
            }
            try {
                if (fullscreenElement()) {
                    if (document.exitFullscreen) {
                        await document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    }
                    return;
                }
                if (kiosk.requestFullscreen) {
                    await kiosk.requestFullscreen();
                } else if (kiosk.webkitRequestFullscreen) {
                    kiosk.webkitRequestFullscreen();
                }
            } catch (e) {
                console.error('Gagal mengubah fullscreen', e);
            }
        }

        fullscreenBtn?.addEventListener('click', toggleDashboardFullscreen);
        document.addEventListener('fullscreenchange', syncFullscreenLayout);
        document.addEventListener('webkitfullscreenchange', syncFullscreenLayout);
        window.addEventListener('resize', () => {
            if (fullscreenElement() === kiosk) {
                syncFullscreenLayout();
            }
        });
    </script>
    @endpush
</x-app-layout>
