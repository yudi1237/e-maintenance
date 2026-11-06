<x-app-layout>
    @if (auth()->user()->isOperator() && session('op_created'))
        @include('admin.partials.op-created-popup')
    @endif
    <div class="pb-16">
        {{-- Hero --}}
        <div id="dashboard-kiosk" class="border-b border-slate-200 bg-white/80 dark:border-white/5 dark:bg-slate-900/50 {{ auth()->user()->isOperator() ? 'operator-kiosk' : '' }}">
            <div id="dashboard-kiosk-inner" class="em-shell flex flex-col py-8 sm:py-10">
                <div id="dashboard-hero" class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div>
                        @unless (auth()->user()->isOperator())
                        <div class="dashboard-live-badge inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-medium text-cyan-700 dark:text-cyan-300 mb-4">
                            <span class="em-pulse-live relative pl-3">Live</span>
                            <span class="text-cyan-600 dark:text-cyan-400/80">Update setiap 5 detik</span>
                        </div>
                        @endunless
                        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ auth()->user()->isOperator() ? 'Mesin Injection' : (auth()->user()->isPpic() ? 'Dashboard PPIC' : (auth()->user()->isManagement() ? 'Dashboard Management' : 'Dashboard Mesin Injection')) }}
                        </h1>
                        <p class="mt-2 text-slate-500 dark:text-slate-400 max-w-xl">
                            {{ auth()->user()->isOperator() ? 'Klik kartu mesin untuk membuka Input OP.' : ((auth()->user()->isManagement() || auth()->user()->isPpic()) ? 'Monitoring kartu mesin, status OP, dan grafik performa PT Dharma Poliplast' : 'PT Dharma Poliplast') }}
                        </p>
                    </div>
                    <div class="flex flex-col items-start gap-3 lg:items-end">
                        @unless (auth()->user()->isOperator())
                        <div id="last-updated" class="text-sm text-slate-500 lg:text-right">
                            Memuat data...
                        </div>
                        @endunless
                        @if (auth()->user()->seesDashboardOverview() || auth()->user()->isOperator())
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

                @if (auth()->user()->seesMachineMap())
                <div id="dashboard-map" class="mt-8">
                    <div class="dashboard-map-toolbar flex flex-wrap items-center justify-between gap-4 mb-4">
                        @unless (auth()->user()->isOperator())
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Peta {{ $machineTotal }} Mesin Injection</h2>
                            @if ($machineTotal > 0)
                                <div class="em-map-switch" role="group" aria-label="Tampilan peta mesin">
                                    <button type="button" data-map-view="cards" class="is-on">Kartu Mesin</button>
                                    <button type="button" data-map-view="layout">Layout Mesin</button>
                                </div>
                            @endif
                        </div>
                        @endunless
                        <div class="flex flex-wrap items-center gap-3">
                            @include('admin.partials.machine-color-legend')
                            @if ($machineTotal > 0 && auth()->user()->seesAdminDashboard())
                                <a href="{{ route('admin.machines.qr.download-all') }}" class="em-qr-btn dashboard-fs-hide">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    Download semua QR
                                </a>
                            @endif
                        </div>
                    </div>

                    @if (auth()->user()->canToggleMachineRunning())
                        <p class="mb-3 text-sm text-slate-600 dark:text-slate-300">Klik lingkaran untuk memilih Hijau (Running), Merah (Breakdown), atau Standby. Klik pilihan yang sama lagi untuk menghapus tanda. Klik dua kali kartu untuk membuka halaman mesin.</p>
                        <div id="machine-status-menu" class="machine-status-menu hidden" role="menu">
                            <button type="button" data-status="running" role="menuitem"><span class="machine-status-swatch is-running"></span>Hijau (Running)</button>
                            <button type="button" data-status="breakdown" role="menuitem"><span class="machine-status-swatch is-breakdown"></span>Merah (Breakdown)</button>
                            <button type="button" data-status="standby" role="menuitem"><span class="machine-status-swatch is-standby"></span>Standby</button>
                        </div>
                    @endif
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
                                    'approval' => 'em-machine-card--approval',
                                    default => 'em-machine-card--idle',
                                };
                                $isRunning = in_array($number, $runningMachines ?? [], true);
                                $isBreakdown = in_array($number, $breakdownMachines ?? [], true);
                                $isStandby = in_array($number, $standbyMachines ?? [], true);
                                $markStatus = $isBreakdown ? 'breakdown' : ($isRunning ? 'running' : ($isStandby ? 'standby' : ''));
                                $markClass = match ($markStatus) {
                                    'running' => 'is-running',
                                    'breakdown' => 'is-breakdown',
                                    'standby' => 'is-standby',
                                    default => '',
                                };
                                $markTitle = match ($markStatus) {
                                    'running' => 'Hijau (Running)',
                                    'breakdown' => 'Merah (Breakdown)',
                                    'standby' => 'Standby',
                                    default => 'Pilih Hijau (Running), Merah (Breakdown), atau Standby',
                                };
                            @endphp
                            <div
                               data-machine="{{ $number }}"
                               data-alert="{{ $alert }}"
                               @if (auth()->user()->canToggleMachineRunning()) data-href="{{ route('admin.machines.show', $number) }}" @endif
                               title="{{ trim($machine->view_label.($machine->mold ? ' · '.$machine->mold->name : '')) }}"
                               class="machine-card em-machine-card relative {{ $alertClass }} {{ auth()->user()->canToggleMachineRunning() ? 'machine-running-toggle' : '' }}">
                                @if (auth()->user()->canToggleMachineRunning())
                                    <button
                                        type="button"
                                        class="machine-running-mark {{ $markClass }}"
                                        data-machine="{{ $number }}"
                                        data-status="{{ $markStatus }}"
                                        title="{{ $markTitle }}"
                                        aria-haspopup="menu"
                                        aria-pressed="{{ $markStatus !== '' ? 'true' : 'false' }}"
                                    ></button>
                                @else
                                    <span class="machine-running-mark {{ $markClass !== '' ? $markClass : 'hidden' }}" data-machine="{{ $number }}" data-status="{{ $markStatus }}" title="{{ $markStatus === '' ? 'Mesin standby' : $markTitle }}"></span>
                                @endif
                                @if (auth()->user()->canToggleMachineRunning())
                                    <div class="flex flex-col items-center justify-center w-full">
                                @else
                                <a href="{{ auth()->user()->isOperator() ? \App\Support\MachineQrCode::inputOpUrl($number) : route('admin.machines.show', $number) }}" class="flex flex-col items-center justify-center w-full">
                                @endif
                                    <span class="machine-card-kicker text-[10px] uppercase tracking-widest text-slate-500">Mesin</span>
                                    <span class="em-machine-num text-2xl font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $number }}</span>
                                    <span class="machine-card-name mt-1 text-[10px] text-slate-500 dark:text-slate-400 max-w-full truncate">{{ $machine->view_label }}</span>
                                    @if ($machine->mold)
                                        <span class="machine-card-name machine-card-mold block max-w-full truncate text-[10px] font-semibold" title="{{ $machine->mold->optionLabel() }}">{{ $machine->mold->name }}</span>
                                    @endif
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
                                @if (auth()->user()->canToggleMachineRunning())
                                    </div>
                                @else
                                </a>
                                @endif
                                @if (auth()->user()->seesAdminDashboard())
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
                                    Belum ada master mesin. Tambahkan data di menu <span class="font-medium text-slate-800 dark:text-slate-200">Asset → Data Mesin</span>.
                                @else
                                    Belum ada data mesin untuk ditampilkan.
                                @endif
                            </div>
                        @endforelse
                    </div>
                    @if ($machineTotal > 0)
                        @php
                            $machinesByNumber = $machines->keyBy(fn ($item) => (int) $item->machine_number);
                            $layoutRows = [
                                [
                                    ['key' => 'cluster-1', 'tone' => 'shell', 'numbers' => range(1, 15)],
                                    ['key' => 'cluster-2', 'tone' => 'muffler', 'numbers' => range(16, 29)],
                                ],
                                [
                                    ['key' => 'cluster-3', 'tone' => 'body', 'numbers' => range(31, 43)],
                                    ['gap' => true],
                                    ['key' => 'cluster-4', 'tone' => 'garnish', 'numbers' => array_merge(range(44, 56), [30])],
                                ],
                            ];
                            $extraClusters = [
                                'k1aa' => ['key' => 'k1aa', 'tone' => 'body', 'numbers' => [60], 'compact' => true],
                                'b65' => ['key' => 'b65', 'tone' => 'garnish', 'numbers' => [57], 'compact' => true],
                                'piece-slide' => ['key' => 'piece-slide', 'tone' => 'garnish', 'numbers' => [58, 59], 'compact' => true],
                            ];
                        @endphp
                        <div id="machine-layout">
                            <div class="em-floor">
                                @foreach ($layoutRows as $rowIndex => $clusters)
                                    <div class="{{ $rowIndex === 0 ? 'em-floor-top' : 'em-floor-bottom' }}">
                                        @foreach ($clusters as $cluster)
                                            @if (! empty($cluster['gap']))
                                                <div class="em-floor-gap" aria-hidden="true"></div>
                                            @else
                                                @include('admin.partials.machine-layout-cluster', [
                                                    'cluster' => $cluster,
                                                    'clusterNames' => $clusterNames,
                                                    'machinesByNumber' => $machinesByNumber,
                                                    'machineStats' => $machineStats,
                                                    'runningMachines' => $runningMachines,
                                                    'breakdownMachines' => $breakdownMachines ?? [],
                                                    'standbyMachines' => $standbyMachines ?? [],
                                                ])
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                                <div class="em-floor-extra">
                                    <div class="em-floor-extra-group">
                                        @include('admin.partials.machine-layout-cluster', [
                                            'cluster' => $extraClusters['k1aa'],
                                            'clusterNames' => $clusterNames,
                                            'machinesByNumber' => $machinesByNumber,
                                            'machineStats' => $machineStats,
                                            'runningMachines' => $runningMachines,
                                            'breakdownMachines' => $breakdownMachines ?? [],
                                            'standbyMachines' => $standbyMachines ?? [],
                                        ])
                                    </div>
                                    <div class="em-floor-gap" aria-hidden="true"></div>
                                    <div class="em-floor-extra-group">
                                        @include('admin.partials.machine-layout-cluster', [
                                            'cluster' => $extraClusters['b65'],
                                            'clusterNames' => $clusterNames,
                                            'machinesByNumber' => $machinesByNumber,
                                            'machineStats' => $machineStats,
                                            'runningMachines' => $runningMachines,
                                            'breakdownMachines' => $breakdownMachines ?? [],
                                            'standbyMachines' => $standbyMachines ?? [],
                                        ])
                                        @include('admin.partials.machine-layout-cluster', [
                                            'cluster' => $extraClusters['piece-slide'],
                                            'clusterNames' => $clusterNames,
                                            'machinesByNumber' => $machinesByNumber,
                                            'machineStats' => $machineStats,
                                            'runningMachines' => $runningMachines,
                                            'breakdownMachines' => $breakdownMachines ?? [],
                                            'standbyMachines' => $standbyMachines ?? [],
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @endif

                @unless (auth()->user()->isOperator())
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
                @endunless

            @if (auth()->user()->seesDashboardOverview())
            {{-- Grafik --}}
            <div id="dashboard-charts" class="em-glass mt-8 rounded-2xl p-4">
                <div class="overflow-x-auto">
                <div class="em-chart-grid {{ auth()->user()->seesTechnicianCharts() ? 'em-chart-grid--full' : 'em-chart-grid--compact' }}">
                    @php
                        $technicianChartCounts = ['idle' => 0, 'busy' => 0, 'off' => 0, 'overtime' => 0];
                        foreach ($technicianStatuses as $technicianChartRow) {
                            $technicianChartKey = $technicianChartRow['state'] ?? 'idle';
                            if (! array_key_exists($technicianChartKey, $technicianChartCounts)) {
                                $technicianChartKey = 'idle';
                            }
                            $technicianChartCounts[$technicianChartKey]++;
                        }
                    @endphp
                    <div class="em-chart-cell min-w-0">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Technician Status</h2>
                        <p class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">Standby, On Job, Off-duty, Overtime</p>
                        <div class="relative h-36" id="technician-status-chart-wrap">
                            <canvas id="technician-status-chart"></canvas>
                        </div>
                    </div>
                    @if (auth()->user()->seesTechnicianCharts())
                    <div class="em-chart-cell min-w-0">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Performer Teknisi</h2>
                        <p id="performer-period" class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">OP diambil · {{ $charts['period_label'] }}</p>
                        <p id="performer-empty" class="text-xs text-slate-500 {{ $charts['performer']['labels'] === [] ? '' : 'hidden' }}">Belum ada data.</p>
                        <div class="relative h-36 {{ $charts['performer']['labels'] === [] ? 'hidden' : '' }}" id="performer-chart-wrap">
                            <canvas id="performer-chart"></canvas>
                        </div>
                    </div>
                    <div class="em-chart-cell min-w-0">
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
                    <div class="em-chart-cell min-w-0">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Grafik Line Stop</h2>
                        <p id="machine-period" class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">MTTR per mesin · OP dibuat → Close by teknisi · {{ $charts['period_label'] }}</p>
                        <p id="machine-chart-empty" class="text-xs text-slate-500 {{ $charts['machines']['labels'] === [] ? '' : 'hidden' }}">Belum ada data.</p>
                        <div class="relative h-36 {{ $charts['machines']['labels'] === [] ? 'hidden' : '' }}" id="machine-chart-wrap">
                            <canvas id="machine-chart"></canvas>
                        </div>
                    </div>
                    <div class="em-chart-cell min-w-0">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Grafik Mesin</h2>
                        <p id="fleet-period" class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">Total, Running, Standby, OP On Progress–Close by teknisi, Breakdown</p>
                        <div class="relative h-40" id="fleet-chart-wrap">
                            <canvas id="fleet-chart"></canvas>
                        </div>
                    </div>
                    <div class="em-chart-cell min-w-0">
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
            </div>
            @endif
            </div>
        </div>

        @unless (auth()->user()->isOperator())
        <div id="dashboard-rest" class="em-shell -mt-4 space-y-8">
            <x-alert />
        </div>
        @endunless
    </div>

    @push('scripts')
    <style>
        .machine-running-mark {
            position: absolute;
            top: 0.35rem;
            left: 0.35rem;
            z-index: 2;
            width: 1.75rem;
            height: 1.75rem;
            border: 0;
            padding: 0;
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        span.machine-running-mark { cursor: default; }
        .machine-running-mark.hidden { display: none !important; }
        .machine-running-mark::after {
            content: '';
            width: 0.85rem;
            height: 0.85rem;
            border-radius: 9999px;
            border: 2px solid #94a3b8;
            background: transparent;
            box-sizing: border-box;
        }
        .machine-running-mark.is-running::after {
            border-color: #16a34a;
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.35);
        }
        .machine-running-mark.is-breakdown::after {
            border-color: #dc2626;
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.35);
        }
        .machine-running-mark.is-standby::after {
            border-color: #64748b;
            background: #475569;
            box-shadow: 0 0 0 3px rgba(71, 85, 105, 0.35);
        }
        .machine-status-menu {
            position: fixed;
            z-index: 80;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            min-width: 11.5rem;
            padding: 0.35rem;
            border-radius: 0.75rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
        }
        html.dark .machine-status-menu {
            background: #0f172a;
            border-color: rgba(255, 255, 255, 0.12);
        }
        .machine-status-menu.hidden { display: none; }
        .machine-status-menu button {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            width: 100%;
            border: 0;
            border-radius: 0.5rem;
            background: transparent;
            padding: 0.4rem 0.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #0f172a;
            cursor: pointer;
        }
        html.dark .machine-status-menu button { color: #e2e8f0; }
        .machine-status-menu button:hover,
        .machine-status-menu button.is-on { background: #f1f5f9; }
        html.dark .machine-status-menu button:hover,
        html.dark .machine-status-menu button.is-on { background: rgba(255, 255, 255, 0.08); }
        .machine-status-swatch {
            width: 0.7rem;
            height: 0.7rem;
            border-radius: 9999px;
            flex: 0 0 auto;
        }
        .machine-status-swatch.is-running { background: #22c55e; }
        .machine-status-swatch.is-breakdown { background: #ef4444; }
        .machine-status-swatch.is-standby { background: #475569; }
        .machine-running-toggle { cursor: pointer; }
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
            animation: none;
        }
        html.dark .em-machine-card--pending {
            border-color: #facc15;
            background-image: linear-gradient(to bottom right, rgba(113, 63, 18, 0.55), rgba(133, 77, 14, 0.4));
            animation: none;
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
            animation: none;
        }
        .em-machine-card--approval {
            border-color: #2563eb;
            background-image: linear-gradient(to bottom right, #eff6ff, #bfdbfe);
            animation: none;
        }
        html.dark .em-machine-card--approval {
            border-color: #60a5fa;
            background-image: linear-gradient(to bottom right, rgba(30, 58, 138, 0.55), rgba(29, 78, 216, 0.4));
            animation: none;
        }
        .em-machine-card--approval .em-machine-num {
            color: #1e3a8a;
        }
        html.dark .em-machine-card--approval .em-machine-num {
            color: #dbeafe;
        }
        .em-machine-card--approval .machine-card-kicker,
        .em-machine-card--approval .machine-card-name,
        .em-machine-card--approval .machine-card-meta {
            color: #1e3a8a;
            font-weight: 600;
        }
        html.dark .em-machine-card--approval .machine-card-kicker,
        html.dark .em-machine-card--approval .machine-card-name,
        html.dark .em-machine-card--approval .machine-card-meta {
            color: #bfdbfe;
        }
        .em-machine-card--approval .machine-op-count,
        .em-machine-card--approval .machine-finding-count,
        .em-machine-card--approval .machine-pm-due {
            color: #172554;
            font-weight: 700;
        }
        html.dark .em-machine-card--approval .machine-op-count,
        html.dark .em-machine-card--approval .machine-finding-count,
        html.dark .em-machine-card--approval .machine-pm-due {
            color: #ffffff;
        }
        .em-machine-approval-dot {
            background-color: #2563eb;
            animation: none;
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
        @keyframes em-layout-machine-alert {
            0%, 100% {
                filter: drop-shadow(2px 0 0 #ef4444) drop-shadow(-2px 0 0 #ef4444) drop-shadow(0 2px 0 #ef4444) drop-shadow(0 -2px 0 #ef4444) drop-shadow(0 0 8px rgba(239, 68, 68, 0.95));
            }
            50% {
                filter: drop-shadow(3px 0 0 #b91c1c) drop-shadow(-3px 0 0 #b91c1c) drop-shadow(0 3px 0 #b91c1c) drop-shadow(0 -3px 0 #b91c1c) drop-shadow(0 0 16px rgba(239, 68, 68, 1));
            }
        }
        @keyframes em-layout-machine-pending {
            0%, 100% {
                filter: drop-shadow(0 0 1px rgba(234, 179, 8, 0.45)) drop-shadow(0 0 4px rgba(234, 179, 8, 0.35));
            }
            50% {
                filter: drop-shadow(0 0 3px rgba(161, 98, 7, 0.95)) drop-shadow(0 0 14px rgba(250, 204, 21, 0.9));
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .em-machine-card--active,
            .em-machine-card--pending,
            .em-machine-alert-dot,
            .em-machine-pending-dot,
            #machine-layout .em-machine-card--active img,
            #machine-layout .em-machine-card--pending img {
                animation: none;
            }
        }
        .em-tech-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .em-tech-row {
            border: 1px solid rgba(148, 163, 184, 0.35);
            border-radius: 0.9rem;
            padding: 0.75rem 0.85rem;
        }
        html.dark .em-tech-row {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.45);
        }
        .em-tech-name {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }
        html.dark .em-tech-name { color: #f8fafc; }
        .em-tech-badge {
            display: inline-flex;
            margin-top: 0.45rem;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.2;
            padding: 0.22rem 0.55rem;
        }
        .em-tech-badge.is-idle { background: #dcfce7; color: #166534; }
        .em-tech-badge.is-busy { background: #fee2e2; color: #991b1b; }
        .em-tech-badge.is-off { background: #e2e8f0; color: #475569; }
        html.dark .em-tech-badge.is-off { background: #334155; color: #cbd5e1; }
        .em-tech-badge.is-overtime { background: #ffedd5; color: #9a3412; }
        html.dark .em-tech-badge.is-overtime { background: rgba(154, 52, 18, 0.45); color: #fdba74; }
        .em-tech-detail {
            margin: 0.4rem 0 0;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }
        html.dark .em-tech-detail { color: #cbd5e1; }
        .em-tech-shift { margin-top: 1.1rem; }
        .em-tech-shift-title {
            margin: 0 0 0.15rem;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #334155;
        }
        html.dark .em-tech-shift-title { color: #e2e8f0; }
        .em-tech-shift-label {
            display: block;
            margin-top: 0.55rem;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
        }
        html.dark .em-tech-shift-label { color: #94a3b8; }
        .em-tech-shift-select {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            border-radius: 0.55rem;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            padding: 0.35rem 0.5rem;
        }
        html.dark .em-tech-shift-select {
            border-color: rgba(148, 163, 184, 0.45);
            background: #0f172a;
            color: #f8fafc;
        }
        .em-tech-shift-save {
            margin-top: 1rem;
            border: 0;
            border-radius: 0.7rem;
            background: #0891b2;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 0.55rem 0.9rem;
            cursor: pointer;
        }
        .em-tech-shift-save:hover { background: #0e7490; }
        .em-map-switch {
            display: inline-flex;
            border: 1px solid #0891b2;
            border-radius: 0.7rem;
            overflow: hidden;
            background: #fff;
        }
        .em-map-switch button {
            border: 0;
            background: transparent;
            color: #0e7490;
            font-weight: 700;
            font-size: 0.8rem;
            line-height: 1;
            padding: 0.45rem 0.8rem;
            cursor: pointer;
        }
        .em-map-switch button.is-on {
            background: #0891b2;
            color: #fff;
        }
        html.dark .em-map-switch {
            background: #0f172a;
        }
        html.dark .em-map-switch button {
            color: #67e8f9;
        }
        html.dark .em-map-switch button.is-on {
            background: #0891b2;
            color: #fff;
        }
        #machine-layout { display: none; }
        #dashboard-map.is-layout #machine-grid { display: none !important; }
        #dashboard-map.is-layout #machine-layout { display: block; }
        .em-floor {
            background: transparent;
            overflow: visible;
        }
        .em-floor-top,
        .em-floor-bottom {
            display: grid;
            gap: 0.75rem;
            min-width: 1080px;
        }
        .em-floor-top { grid-template-columns: 15fr 14fr; }
        .em-floor-bottom {
            grid-template-columns: 13fr 1.6fr 14fr;
            margin-top: 0.85rem;
        }
        .em-floor-extra {
            display: grid;
            grid-template-columns: 13fr 1.6fr 14fr;
            gap: 0.75rem;
            margin-top: 0.35rem;
            min-width: 1080px;
        }
        .em-floor-extra-group {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            min-width: 0;
        }
        .em-cluster--compact {
            width: max-content;
            max-width: 100%;
            min-width: 13.5rem;
        }
        .em-cluster-rename {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            width: 100%;
            min-width: 0;
        }
        .em-cluster-rename input {
            flex: 1 1 auto;
            min-width: 0;
            width: 100%;
            border: 1px dashed transparent;
            border-radius: 0.4rem;
            background: transparent;
            color: #0f172a;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            padding: 0.15rem 0.3rem;
        }
        .em-cluster--compact .em-cluster-rename input {
            min-width: 8.5rem;
        }
        .em-cluster-rename input:hover,
        .em-cluster-rename input:focus {
            border-color: #94a3b8;
            background: #fff;
            outline: none;
        }
        html.dark .em-cluster-rename input {
            color: #f8fafc;
        }
        html.dark .em-cluster-rename input:hover,
        html.dark .em-cluster-rename input:focus {
            border-color: rgba(148, 163, 184, 0.7);
            background: #0f172a;
        }
        .em-cluster-rename button {
            flex: 0 0 auto;
            border: 0;
            border-radius: 999px;
            background: #0891b2;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            line-height: 1;
            padding: 0.28rem 0.45rem;
            cursor: pointer;
        }
        .em-cluster-rename button:hover { background: #0e7490; }
        .em-cluster {
            min-width: 0;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.38);
            background: rgba(255, 255, 255, 0.78);
            padding: 0.55rem 0.55rem 0.65rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }
        html.dark .em-cluster {
            background: rgba(15, 23, 42, 0.72);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: none;
        }
        .em-cluster--shell { box-shadow: inset 3px 0 0 #0891b2; }
        .em-cluster--muffler { box-shadow: inset 3px 0 0 #6366f1; }
        .em-cluster--body { box-shadow: inset 3px 0 0 #f59e0b; }
        .em-cluster--garnish { box-shadow: inset 3px 0 0 #10b981; }
        html.dark .em-cluster--shell { box-shadow: inset 3px 0 0 #22d3ee; }
        html.dark .em-cluster--muffler { box-shadow: inset 3px 0 0 #818cf8; }
        html.dark .em-cluster--body { box-shadow: inset 3px 0 0 #fbbf24; }
        html.dark .em-cluster--garnish { box-shadow: inset 3px 0 0 #34d399; }
        .em-cluster-head {
            display: flex;
            align-items: baseline;
            gap: 0.4rem;
            margin: 0 0.15rem 0.5rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.28);
        }
        .em-cluster-kicker {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
            color: #0f172a;
            white-space: nowrap;
        }
        html.dark .em-cluster-kicker { color: #f8fafc; }
        .em-cluster-cells { display: grid; gap: 0.15rem; overflow: visible; }
        .em-layout-cell {
            position: relative;
            min-height: 0;
            background: transparent !important;
            border: 0 !important;
            border-radius: 0 !important;
            padding: 0.1rem 0.08rem 1.25rem !important;
            box-shadow: none !important;
            animation: none !important;
            transform: none !important;
            overflow: visible;
        }
        html.dark #machine-layout .em-layout-cell {
            background: transparent !important;
            border: 0 !important;
        }
        #machine-layout .em-machine-card--idle:hover {
            border: 0 !important;
            box-shadow: none !important;
        }
        .em-cluster,
        .em-cluster-cells,
        .em-floor { overflow: visible; }
        .em-layout-hit {
            display: block;
            position: relative;
            width: 100%;
            aspect-ratio: 488 / 1148;
            height: auto;
            min-height: 0;
            text-decoration: none;
            overflow: visible;
        }
        .em-layout-cell img {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 235.25%;
            max-width: none;
            height: auto;
            object-fit: contain;
            pointer-events: none;
            transform: translate(-50%, -50%) rotate(90deg);
            transform-origin: center center;
        }
        #machine-layout .em-machine-card--active img {
            animation: em-layout-machine-alert 1.1s ease-in-out infinite;
        }
        #machine-layout .em-machine-card--pending img {
            animation: none;
            filter: drop-shadow(2px 0 0 #ca8a04) drop-shadow(-2px 0 0 #ca8a04) drop-shadow(0 2px 0 #ca8a04) drop-shadow(0 -2px 0 #ca8a04) drop-shadow(0 0 8px rgba(234, 179, 8, 0.95));
        }
        #machine-layout .em-machine-card--approval img {
            animation: none;
            filter: drop-shadow(2px 0 0 #1d4ed8) drop-shadow(-2px 0 0 #1d4ed8) drop-shadow(0 2px 0 #1d4ed8) drop-shadow(0 -2px 0 #1d4ed8) drop-shadow(0 0 8px rgba(37, 99, 235, 0.95));
        }
        .em-layout-no {
            position: absolute;
            left: 50%;
            bottom: 0.12rem;
            z-index: 3;
            transform: translateX(-50%);
            background: #0f172a;
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            line-height: 1.2;
            border-radius: 999px;
            padding: 0.22rem 0.48rem;
            pointer-events: none;
            white-space: nowrap;
            box-shadow: 0 0 0 2px #fff;
        }
        html.dark #machine-layout .em-layout-no {
            background: #f8fafc;
            color: #0f172a;
        }
        #machine-layout .em-machine-card--active .em-layout-no,
        html.dark #machine-layout .em-machine-card--active .em-layout-no {
            background: #ef4444;
            color: #fff;
        }
        #machine-layout .em-machine-card--pending .em-layout-no,
        html.dark #machine-layout .em-machine-card--pending .em-layout-no {
            background: #eab308;
            color: #422006;
        }
        #machine-layout .em-machine-card--approval .em-layout-no,
        html.dark #machine-layout .em-machine-card--approval .em-layout-no {
            background: #2563eb;
            color: #fff;
        }
        #machine-layout .em-machine-card--active,
        #machine-layout .em-machine-card--pending,
        #machine-layout .em-machine-card--approval {
            border: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
            animation: none !important;
        }
        #machine-layout .machine-running-mark {
            width: 1.15rem;
            height: 1.15rem;
            top: 0.05rem;
            left: 0.02rem;
            box-shadow: 0 0 0 2px #fff;
        }
        #machine-layout .machine-running-mark::after {
            width: 0.55rem;
            height: 0.55rem;
            border-width: 2px;
        }
        #dashboard-kiosk:fullscreen #dashboard-map.is-layout #machine-layout,
        #dashboard-kiosk:-webkit-full-screen #dashboard-map.is-layout #machine-layout,
        #dashboard-kiosk.is-fs #dashboard-map.is-layout #machine-layout,
        html.operator-screen-fit #dashboard-map.is-layout #machine-layout {
            display: block;
            flex: 1 1 auto;
            min-height: 0;
            overflow: auto;
        }
        #dashboard-kiosk:fullscreen #machine-layout .machine-card,
        #dashboard-kiosk:-webkit-full-screen #machine-layout .machine-card,
        #dashboard-kiosk.is-fs #machine-layout .machine-card,
        html.operator-screen-fit #machine-layout .machine-card,
        #dashboard-kiosk.operator-kiosk:fullscreen #machine-layout .machine-card,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen #machine-layout .machine-card {
            height: auto !important;
            min-height: 0 !important;
            padding: 0.1rem 0.08rem 1.25rem !important;
            overflow: visible !important;
        }
        #dashboard-kiosk:fullscreen, #dashboard-kiosk.is-fs,
        #dashboard-kiosk:-webkit-full-screen, #dashboard-kiosk.is-fs {
            background-color: #f8fafc;
            width: 100%;
            height: 100%;
            overflow: hidden;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        html.dark #dashboard-kiosk:fullscreen, html.dark #dashboard-kiosk.is-fs,
        html.dark #dashboard-kiosk:-webkit-full-screen, html.dark #dashboard-kiosk.is-fs {
            background-color: #020617;
        }
        #dashboard-kiosk:fullscreen #dashboard-kiosk-inner, #dashboard-kiosk.is-fs #dashboard-kiosk-inner,
        #dashboard-kiosk:-webkit-full-screen #dashboard-kiosk-inner , #dashboard-kiosk.is-fs #dashboard-kiosk-inner {
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
        #dashboard-kiosk:fullscreen #dashboard-hero, #dashboard-kiosk.is-fs #dashboard-hero,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero , #dashboard-kiosk.is-fs #dashboard-hero {
            order: 1;
            flex: 0 0 auto;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts, #dashboard-kiosk.is-fs #dashboard-charts,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts , #dashboard-kiosk.is-fs #dashboard-charts {
            order: 2;
            flex: 0 0 26vh;
            max-height: 240px;
            min-height: 150px;
            margin-top: 0;
            overflow: hidden;
            padding: 0.45rem 0.65rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-map, #dashboard-kiosk.is-fs #dashboard-map,
        #dashboard-kiosk:-webkit-full-screen #dashboard-map , #dashboard-kiosk.is-fs #dashboard-map {
            order: 3;
            flex: 1 1 auto;
            min-height: 0;
            margin-top: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        #dashboard-kiosk:fullscreen #dashboard-stats, #dashboard-kiosk.is-fs #dashboard-stats,
        #dashboard-kiosk:-webkit-full-screen #dashboard-stats, #dashboard-kiosk.is-fs #dashboard-stats,
        #dashboard-kiosk:fullscreen #technician-status, #dashboard-kiosk.is-fs #technician-status,
        #dashboard-kiosk:-webkit-full-screen #technician-status, #dashboard-kiosk.is-fs #technician-status,
        #dashboard-kiosk:fullscreen .dashboard-live-badge, #dashboard-kiosk.is-fs .dashboard-live-badge,
        #dashboard-kiosk:-webkit-full-screen .dashboard-live-badge, #dashboard-kiosk.is-fs .dashboard-live-badge,
        #dashboard-kiosk:fullscreen #last-updated, #dashboard-kiosk.is-fs #last-updated,
        #dashboard-kiosk:-webkit-full-screen #last-updated, #dashboard-kiosk.is-fs #last-updated,
        #dashboard-kiosk:fullscreen .dashboard-fs-hide, #dashboard-kiosk.is-fs .dashboard-fs-hide,
        #dashboard-kiosk:-webkit-full-screen .dashboard-fs-hide , #dashboard-kiosk.is-fs .dashboard-fs-hide {
            display: none !important;
        }
        #dashboard-kiosk:fullscreen #dashboard-hero h1, #dashboard-kiosk.is-fs #dashboard-hero h1,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero h1 , #dashboard-kiosk.is-fs #dashboard-hero h1 {
            font-size: 1.35rem;
            line-height: 1.2;
        }
        #dashboard-kiosk:fullscreen #dashboard-hero p, #dashboard-kiosk.is-fs #dashboard-hero p,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero p , #dashboard-kiosk.is-fs #dashboard-hero p {
            margin-top: 0.15rem;
            font-size: 0.85rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-hero .em-qr-btn, #dashboard-kiosk.is-fs #dashboard-hero .em-qr-btn,
        #dashboard-kiosk:-webkit-full-screen #dashboard-hero .em-qr-btn , #dashboard-kiosk.is-fs #dashboard-hero .em-qr-btn {
            min-height: 2.25rem;
            padding: 0.35rem 0.75rem;
        }
        .em-chart-grid {
            display: grid;
            gap: 0.75rem;
            align-items: start;
        }
        .em-chart-grid--full { grid-template-columns: repeat(6, minmax(0, 1fr)); min-width: 1280px; }
        .em-chart-grid--compact { grid-template-columns: repeat(4, minmax(0, 1fr)); min-width: 860px; }
        .em-chart-cell + .em-chart-cell {
            border-left: 1px solid rgba(148, 163, 184, 0.25);
            padding-left: 0.75rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts .em-chart-grid, #dashboard-kiosk.is-fs #dashboard-charts .em-chart-grid,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts .em-chart-grid , #dashboard-kiosk.is-fs #dashboard-charts .em-chart-grid {
            gap: 0.5rem;
            height: 100%;
            min-width: 0;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts .em-chart-grid--full, #dashboard-kiosk.is-fs #dashboard-charts .em-chart-grid--full,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts .em-chart-grid--full , #dashboard-kiosk.is-fs #dashboard-charts .em-chart-grid--full {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
        #dashboard-kiosk:fullscreen #dashboard-charts .em-chart-grid--compact, #dashboard-kiosk.is-fs #dashboard-charts .em-chart-grid--compact,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts .em-chart-grid--compact , #dashboard-kiosk.is-fs #dashboard-charts .em-chart-grid--compact {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        #dashboard-kiosk:fullscreen #dashboard-charts h2, #dashboard-kiosk.is-fs #dashboard-charts h2,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts h2 , #dashboard-kiosk.is-fs #dashboard-charts h2 {
            font-size: 0.72rem;
        }
        #dashboard-kiosk:fullscreen #performer-period, #dashboard-kiosk.is-fs #performer-period,
        #dashboard-kiosk:-webkit-full-screen #performer-period, #dashboard-kiosk.is-fs #performer-period,
        #dashboard-kiosk:fullscreen #sla-summary, #dashboard-kiosk.is-fs #sla-summary,
        #dashboard-kiosk:-webkit-full-screen #sla-summary, #dashboard-kiosk.is-fs #sla-summary,
        #dashboard-kiosk:fullscreen #machine-period, #dashboard-kiosk.is-fs #machine-period,
        #dashboard-kiosk:-webkit-full-screen #machine-period , #dashboard-kiosk.is-fs #machine-period,
        #dashboard-kiosk:fullscreen #fleet-period, #dashboard-kiosk.is-fs #fleet-period,
        #dashboard-kiosk:-webkit-full-screen #fleet-period, #dashboard-kiosk.is-fs #fleet-period {
            display: none;
        }
        #dashboard-kiosk:fullscreen #performer-chart-wrap, #dashboard-kiosk.is-fs #performer-chart-wrap,
        #dashboard-kiosk:-webkit-full-screen #performer-chart-wrap, #dashboard-kiosk.is-fs #performer-chart-wrap,
        #dashboard-kiosk:fullscreen #sla-chart-wrap, #dashboard-kiosk.is-fs #sla-chart-wrap,
        #dashboard-kiosk:-webkit-full-screen #sla-chart-wrap, #dashboard-kiosk.is-fs #sla-chart-wrap,
        #dashboard-kiosk:fullscreen #machine-chart-wrap, #dashboard-kiosk.is-fs #machine-chart-wrap,
        #dashboard-kiosk:-webkit-full-screen #machine-chart-wrap, #dashboard-kiosk.is-fs #machine-chart-wrap,
        #dashboard-kiosk:fullscreen #technician-status-chart-wrap, #dashboard-kiosk.is-fs #technician-status-chart-wrap,
        #dashboard-kiosk:-webkit-full-screen #technician-status-chart-wrap, #dashboard-kiosk.is-fs #technician-status-chart-wrap {
            height: calc(100% - 1.35rem) !important;
            min-height: 0;
        }
        #dashboard-kiosk:fullscreen #pm-status-chart, #dashboard-kiosk.is-fs #pm-status-chart,
        #dashboard-kiosk:-webkit-full-screen #pm-status-chart , #dashboard-kiosk.is-fs #pm-status-chart {
            max-height: 5.5rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-charts .h-28, #dashboard-kiosk.is-fs #dashboard-charts .h-28,
        #dashboard-kiosk:-webkit-full-screen #dashboard-charts .h-28 , #dashboard-kiosk.is-fs #dashboard-charts .h-28 {
            height: 5.5rem;
            width: 5.5rem;
        }
        #dashboard-kiosk:fullscreen #dashboard-map > .dashboard-map-toolbar, #dashboard-kiosk.is-fs #dashboard-map > .dashboard-map-toolbar,
        #dashboard-kiosk:-webkit-full-screen #dashboard-map > .dashboard-map-toolbar , #dashboard-kiosk.is-fs #dashboard-map > .dashboard-map-toolbar {
            margin-bottom: 0.4rem;
            flex: 0 0 auto;
        }
        #dashboard-kiosk:fullscreen #dashboard-map h2, #dashboard-kiosk.is-fs #dashboard-map h2,
        #dashboard-kiosk:-webkit-full-screen #dashboard-map h2 , #dashboard-kiosk.is-fs #dashboard-map h2 {
            font-size: 0.95rem;
        }
        #dashboard-kiosk:fullscreen #machine-grid, #dashboard-kiosk.is-fs #machine-grid,
        #dashboard-kiosk:-webkit-full-screen #machine-grid , #dashboard-kiosk.is-fs #machine-grid {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            gap: 0.35rem;
            grid-template-columns: repeat(14, minmax(0, 1fr));
            grid-auto-rows: minmax(0, 1fr);
            align-content: stretch;
        }
        #dashboard-kiosk:fullscreen .machine-card, #dashboard-kiosk.is-fs .machine-card,
        #dashboard-kiosk:-webkit-full-screen .machine-card , #dashboard-kiosk.is-fs .machine-card {
            min-height: 0;
            height: 100%;
            padding: 0.28rem 0.32rem;
            transform: none !important;
            overflow: hidden;
        }
        #dashboard-kiosk:fullscreen .machine-card-kicker, #dashboard-kiosk.is-fs .machine-card-kicker,
        #dashboard-kiosk:-webkit-full-screen .machine-card-kicker , #dashboard-kiosk.is-fs .machine-card-kicker {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            line-height: 1.1;
            color: #475569;
        }
        html.dark #dashboard-kiosk:fullscreen .machine-card-kicker, html.dark #dashboard-kiosk.is-fs .machine-card-kicker,
        html.dark #dashboard-kiosk:-webkit-full-screen .machine-card-kicker , html.dark #dashboard-kiosk.is-fs .machine-card-kicker {
            color: #cbd5e1;
        }
        html.dark #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-kicker, html.dark #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-kicker,
        html.dark #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-kicker , html.dark #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-kicker {
            color: #fecaca;
        }
        #dashboard-kiosk:fullscreen .em-machine-num, #dashboard-kiosk.is-fs .em-machine-num,
        #dashboard-kiosk:-webkit-full-screen .em-machine-num , #dashboard-kiosk.is-fs .em-machine-num {
            font-size: 1.15rem;
            margin-top: 0;
            line-height: 1.1;
        }
        #dashboard-kiosk:fullscreen .machine-card-name, #dashboard-kiosk.is-fs .machine-card-name,
        #dashboard-kiosk:-webkit-full-screen .machine-card-name , #dashboard-kiosk.is-fs .machine-card-name {
            font-size: 10px;
            font-weight: 600;
            margin-top: 0.1rem;
            color: #334155;
        }
        #dashboard-kiosk:fullscreen .machine-card-meta, #dashboard-kiosk.is-fs .machine-card-meta,
        #dashboard-kiosk:-webkit-full-screen .machine-card-meta , #dashboard-kiosk.is-fs .machine-card-meta {
            margin-top: 0.15rem;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            color: #334155;
        }
        #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-name, #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-name,
        #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-name, #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-name,
        #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-meta, #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-meta,
        #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-meta , #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-meta {
            color: #7f1d1d;
        }
        html.dark #dashboard-kiosk:fullscreen .machine-card-name, html.dark #dashboard-kiosk.is-fs .machine-card-name,
        html.dark #dashboard-kiosk:-webkit-full-screen .machine-card-name, html.dark #dashboard-kiosk.is-fs .machine-card-name,
        html.dark #dashboard-kiosk:fullscreen .machine-card-meta, html.dark #dashboard-kiosk.is-fs .machine-card-meta,
        html.dark #dashboard-kiosk:-webkit-full-screen .machine-card-meta , html.dark #dashboard-kiosk.is-fs .machine-card-meta {
            color: #e2e8f0;
        }
        html.dark #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-name, html.dark #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-name,
        html.dark #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-name, html.dark #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-name,
        html.dark #dashboard-kiosk:fullscreen .em-machine-card--active .machine-card-meta, html.dark #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-meta,
        html.dark #dashboard-kiosk:-webkit-full-screen .em-machine-card--active .machine-card-meta , html.dark #dashboard-kiosk.is-fs .em-machine-card--active .machine-card-meta {
            color: #fecaca;
        }
        html.operator-screen-fit,
        html.operator-screen-fit body {
            height: 100%;
            overflow: hidden;
        }
        html.operator-screen-fit nav,
        html.operator-screen-fit .em-site-footer {
            display: none !important;
        }
        html.operator-screen-fit .min-h-screen,
        html.operator-screen-fit .pb-16 {
            min-height: 0 !important;
            height: 100dvh;
            padding-bottom: 0 !important;
            overflow: hidden;
        }
        html.operator-screen-fit #dashboard-kiosk,
        #dashboard-kiosk.operator-kiosk:fullscreen,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen {
            height: 100dvh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        html.operator-screen-fit #dashboard-kiosk-inner,
        #dashboard-kiosk.operator-kiosk:fullscreen #dashboard-kiosk-inner,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen #dashboard-kiosk-inner {
            flex: 1 1 auto;
            min-height: 0;
            height: 100%;
            max-width: none;
            width: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 0.45rem 0.7rem 0.55rem;
        }
        html.operator-screen-fit #dashboard-hero,
        #dashboard-kiosk.operator-kiosk:fullscreen #dashboard-hero,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen #dashboard-hero {
            flex: 0 0 auto;
            gap: 0.5rem;
        }
        html.operator-screen-fit #dashboard-hero h1,
        #dashboard-kiosk.operator-kiosk:fullscreen #dashboard-hero h1,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen #dashboard-hero h1 {
            font-size: 1.2rem;
            line-height: 1.15;
        }
        html.operator-screen-fit #dashboard-hero p,
        #dashboard-kiosk.operator-kiosk:fullscreen #dashboard-hero p,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen #dashboard-hero p {
            display: none;
        }
        html.operator-screen-fit #dashboard-map,
        #dashboard-kiosk.operator-kiosk:fullscreen #dashboard-map,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen #dashboard-map {
            flex: 1 1 auto;
            min-height: 0;
            margin-top: 0.35rem;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        html.operator-screen-fit #machine-grid,
        #dashboard-kiosk.operator-kiosk:fullscreen #machine-grid,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen #machine-grid {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            align-content: stretch;
        }
        html.operator-screen-fit .machine-card,
        #dashboard-kiosk.operator-kiosk:fullscreen .machine-card,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen .machine-card {
            min-height: 0 !important;
            height: 100%;
            overflow: hidden;
            padding: 0.2rem 0.25rem;
            transform: none !important;
        }
        html.operator-screen-fit .machine-card-meta,
        html.operator-screen-fit .preventive-badges,
        #dashboard-kiosk.operator-kiosk:fullscreen .machine-card-meta,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen .machine-card-meta,
        #dashboard-kiosk.operator-kiosk:fullscreen .preventive-badges,
        #dashboard-kiosk.operator-kiosk:-webkit-full-screen .preventive-badges {
            display: none;
        }
        #dashboard-kiosk:fullscreen .preventive-badges, #dashboard-kiosk.is-fs .preventive-badges,
        #dashboard-kiosk:-webkit-full-screen .preventive-badges , #dashboard-kiosk.is-fs .preventive-badges {
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
        let fleetChart = null;
        let pmStatusChart = null;
        let technicianStatusChart = null;
        @php
            $technicianChartSeed = $technicianChartCounts ?? ['idle' => 0, 'busy' => 0, 'off' => 0, 'overtime' => 0];
        @endphp
        const technicianChartSeed = @json($technicianChartSeed);

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

        if (document.getElementById('technician-status-chart')) {
        technicianStatusChart = new Chart(document.getElementById('technician-status-chart'), {
            type: 'bar',
            data: {
                labels: ['Standby / Available', 'On Job / Assigned', 'Off-duty', 'Overtime'],
                datasets: [{
                    label: 'Teknisi',
                    data: [technicianChartSeed.idle || 0, technicianChartSeed.busy || 0, technicianChartSeed.off || 0, technicianChartSeed.overtime || 0],
                    backgroundColor: ['#22c55e', '#ef4444', '#64748b', '#f97316'],
                    borderRadius: 4,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: tickColor() }, grid: { color: gridColor() } },
                    y: { ticks: { font: { size: 9 }, color: tickColor() }, grid: { display: false } },
                },
            },
        });
        }

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

        if (document.getElementById('fleet-chart')) {
        const fleet = chartsData.machine_status || { labels: [], values: [], colors: [] };
        fleetChart = new Chart(document.getElementById('fleet-chart'), {
            type: 'bar',
            data: {
                labels: fleet.labels,
                datasets: [{
                    label: 'Mesin',
                    data: fleet.values,
                    backgroundColor: fleet.colors,
                    borderRadius: 4,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: tickColor() }, grid: { color: gridColor() } },
                    y: { ticks: { font: { size: 9 }, color: tickColor() }, grid: { display: false } },
                },
            },
        });
        }

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

        function technicianChartValues(rows) {
            const counts = { idle: 0, busy: 0, off: 0, overtime: 0 };
            (rows || []).forEach((row) => {
                const state = counts[row.state] === undefined ? 'idle' : row.state;
                counts[state] += 1;
            });
            return [counts.idle, counts.busy, counts.off, counts.overtime];
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
            if (fleetChart && data.machine_status) {
                fleetChart.data.labels = data.machine_status.labels;
                fleetChart.data.datasets[0].data = data.machine_status.values;
                fleetChart.data.datasets[0].backgroundColor = data.machine_status.colors;
                fleetChart.update('none');
            }

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
                const alert = stat.alert === 'active' || stat.alert === 'pending' || stat.alert === 'approval' ? stat.alert : 'idle';
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
            card.classList.remove('em-machine-card--idle', 'em-machine-card--active', 'em-machine-card--pending', 'em-machine-card--approval');
            const alertClass = {
                active: 'em-machine-card--active',
                pending: 'em-machine-card--pending',
                approval: 'em-machine-card--approval',
            }[parsed.alert] || 'em-machine-card--idle';
            card.classList.add(alertClass);
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
                applyRunningMarks(data.running || [], data.breakdown || [], data.standby || []);
                if (data.pm_status) {
                    applyPmStatus(data.pm_status);
                }
                if (technicianStatusChart && Array.isArray(data.technicians)) {
                    technicianStatusChart.data.datasets[0].data = technicianChartValues(data.technicians);
                    technicianStatusChart.update('none');
                }
            } catch (e) {
                console.error('Gagal memuat statistik', e);
            }
        }

        if (lastUpdatedEl && totalReportsEl) {
            refreshStats();
            setInterval(refreshStats, 5000);
        }

        @if (auth()->user()->isOperator())
        async function refreshOperatorRunning() {
            try {
                const response = await fetch(@json(route('operator.running')), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                });
                if (!response.ok) return;
                const data = await response.json();
                applyRunningMarks(data.running || [], data.breakdown || [], data.standby || []);
                if (data.machines) {
                    document.querySelectorAll('.machine-card').forEach((card) => {
                        const num = card.dataset.machine;
                        const stat = data.machines[num] ?? data.machines[Number(num)] ?? { op: 0, findings: 0, pm_due: null, alert: 'idle' };
                        applyCardState(card, stat);
                    });
                }
            } catch (e) {
                console.error('Gagal memuat status running', e);
            }
        }
        refreshOperatorRunning();
        setInterval(refreshOperatorRunning, 5000);
        @endif
        document.getElementById('pm-year')?.addEventListener('change', refreshStats);
        document.getElementById('pm-month')?.addEventListener('change', refreshStats);

        const kiosk = document.getElementById('dashboard-kiosk');
        const fullscreenBtn = document.getElementById('dashboard-fullscreen-btn');
        const fullscreenLabel = fullscreenBtn?.querySelector('[data-fullscreen-label]');
        const operatorKiosk = @json(auth()->user()->isOperator());

        function fullscreenElement() {
            return document.fullscreenElement || document.webkitFullscreenElement || null;
        }

        function browserLooksFullscreen() {
            return window.innerHeight >= screen.availHeight - 8;
        }

        function syncOperatorScreenFit() {
            if (!operatorKiosk) {
                return;
            }
            const on = browserLooksFullscreen() && fullscreenElement() !== kiosk;
            document.documentElement.classList.toggle('operator-screen-fit', on);
        }

        function kioskIsFitted() {
            return fullscreenElement() === kiosk
                || (kiosk && kiosk.classList.contains('is-fs'))
                || document.documentElement.classList.contains('operator-screen-fit');
        }

        function resizeDashboardCharts() {
            [performerChart, slaChart, machineChart, fleetChart, pmStatusChart].forEach((chart) => {
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
            const on = kioskIsFitted();
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
            const maxCols = operatorKiosk ? 20 : 16;
            let cols = Math.max(8, Math.min(maxCols, Math.round(Math.sqrt(count * (width / Math.max(height, 1))))));
            let rows = Math.ceil(count / cols);
            while (rows > 5 && cols < maxCols) {
                cols += 1;
                rows = Math.ceil(count / cols);
            }
            while (height / rows < 56 && cols < maxCols) {
                cols += 1;
                rows = Math.ceil(count / cols);
            }
            grid.style.gridTemplateColumns = `repeat(${cols}, minmax(0, 1fr))`;
            grid.style.gridTemplateRows = `repeat(${rows}, minmax(0, 1fr))`;
        }

        function syncF11Flag() {
            try {
                if (window.frameElement) {
                    return;
                }
                if (browserLooksFullscreen() && fullscreenElement() !== kiosk && !document.getElementById('em-fs-shell')) {
                    sessionStorage.setItem('em-fs-f11', '1');
                } else if (!browserLooksFullscreen()) {
                    sessionStorage.removeItem('em-fs-f11');
                }
            } catch (e) {}
        }

        function syncFullscreenLayout() {
            syncOperatorScreenFit();
            syncF11Flag();
            if (kiosk && document.documentElement.classList.contains('em-fs-persist')) {
                kiosk.classList.add('is-fs');
            }
            const on = fullscreenElement() === kiosk || document.documentElement.classList.contains('em-fs-persist');
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
            if (!kiosk || typeof window.emEnterFullscreenShell !== 'function') {
                return;
            }
            try {
                window.emEnterFullscreenShell();
            } catch (e) {
                console.error('Gagal mengubah fullscreen', e);
            }
        }

        fullscreenBtn?.addEventListener('click', toggleDashboardFullscreen);
        document.addEventListener('fullscreenchange', syncFullscreenLayout);
        document.addEventListener('webkitfullscreenchange', syncFullscreenLayout);
        window.addEventListener('resize', () => {
            syncOperatorScreenFit();
            syncF11Flag();
            if (kioskIsFitted() || document.documentElement.classList.contains('em-fs-persist')) {
                syncFullscreenLayout();
            }
        });
        syncOperatorScreenFit();
        if (kioskIsFitted() || document.documentElement.classList.contains('em-fs-persist')) {
            syncFullscreenLayout();
        }

        function applyRunningMarks(numbers, breakdownNumbers, standbyNumbers) {
            const running = new Set((numbers || []).map((number) => String(number)));
            const breakdown = new Set((breakdownNumbers || []).map((number) => String(number)));
            const standby = new Set((standbyNumbers || []).map((number) => String(number)));
            document.querySelectorAll('.machine-running-mark').forEach((mark) => {
                const machine = String(mark.dataset.machine);
                const status = breakdown.has(machine) ? 'breakdown' : (running.has(machine) ? 'running' : (standby.has(machine) ? 'standby' : ''));
                paintMachineMark(mark, status);
            });
            syncMachineStatusMenu();
        }

        function paintMachineMark(mark, status) {
            mark.classList.toggle('is-running', status === 'running');
            mark.classList.toggle('is-breakdown', status === 'breakdown');
            mark.classList.toggle('is-standby', status === 'standby');
            mark.dataset.status = status;
            const title = status === 'running'
                ? 'Hijau (Running)'
                : (status === 'breakdown' ? 'Merah (Breakdown)' : (status === 'standby' ? 'Standby' : (mark.tagName === 'BUTTON' ? 'Pilih Hijau (Running), Merah (Breakdown), atau Standby' : 'Mesin standby')));
            mark.title = title;
            if (mark.tagName === 'BUTTON') {
                mark.setAttribute('aria-pressed', status === '' ? 'false' : 'true');
            } else {
                mark.classList.toggle('hidden', status === '');
            }
        }

        const machineStatusMenu = document.getElementById('machine-status-menu');
        let machineStatusTarget = '';

        function syncMachineStatusMenu() {
            if (!machineStatusMenu || machineStatusMenu.classList.contains('hidden') || machineStatusTarget === '') {
                return;
            }
            const current = document.querySelector('button.machine-running-mark[data-machine="' + machineStatusTarget + '"]')?.dataset.status || '';
            machineStatusMenu.querySelectorAll('button').forEach((item) => {
                item.classList.toggle('is-on', item.dataset.status === current);
            });
        }

        function closeMachineStatusMenu() {
            machineStatusTarget = '';
            machineStatusMenu?.classList.add('hidden');
        }

        function openMachineStatusMenu(button) {
            if (!machineStatusMenu) {
                return;
            }
            machineStatusTarget = String(button.dataset.machine || '');
            const rect = button.getBoundingClientRect();
            machineStatusMenu.style.left = Math.max(8, Math.min(rect.left, window.innerWidth - 196)) + 'px';
            machineStatusMenu.style.top = Math.min(rect.bottom + 6, window.innerHeight - 132) + 'px';
            machineStatusMenu.classList.remove('hidden');
            syncMachineStatusMenu();
        }

        document.querySelectorAll('button.machine-running-mark').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                openMachineStatusMenu(button);
            });
        });

        machineStatusMenu?.querySelectorAll('button').forEach((item) => {
            item.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const current = document.querySelector('button.machine-running-mark[data-machine="' + machineStatusTarget + '"]')?.dataset.status || '';
                const next = current === item.dataset.status ? 'off' : item.dataset.status;
                saveMachineStatus(machineStatusTarget, next);
            });
        });

        document.addEventListener('click', (event) => {
            if (!machineStatusMenu || machineStatusMenu.classList.contains('hidden')) {
                return;
            }
            if (event.target.closest('#machine-status-menu, button.machine-running-mark')) {
                return;
            }
            closeMachineStatusMenu();
        });

        document.querySelectorAll('.machine-running-toggle').forEach((card) => {
            let clickTimer = null;

            card.addEventListener('click', (event) => {
                if (event.target.closest('a, button, #machine-status-menu')) {
                    return;
                }
                event.preventDefault();
                if (clickTimer) {
                    clearTimeout(clickTimer);
                    clickTimer = null;
                    if (card.dataset.href) {
                        window.location.href = card.dataset.href;
                    }
                    return;
                }
                clickTimer = setTimeout(() => {
                    clickTimer = null;
                }, 280);
            });
        });

        async function saveMachineStatus(machineNumber, status) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const body = new FormData();
            body.append('machine_number', machineNumber);
            body.append('status', status);
            try {
                const response = await fetch(@json(route('admin.machines.running')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body,
                });
                if (!response.ok) {
                    return;
                }
                const data = await response.json();
                const saved = data.status || '';
                document.querySelectorAll('.machine-running-mark').forEach((mark) => {
                    if (String(mark.dataset.machine) !== String(machineNumber)) {
                        return;
                    }
                    paintMachineMark(mark, saved);
                });
                syncMachineStatusMenu();
            } catch (error) {
                console.error('Gagal menyimpan status mesin', error);
            }
        }

        const machineMap = document.getElementById('dashboard-map');
        document.querySelectorAll('[data-map-view]').forEach((button) => {
            button.addEventListener('click', () => setMachineMapView(button.dataset.mapView));
        });
        function setMachineMapView(view) {
            if (!machineMap) {
                return;
            }
            const layout = view === 'layout';
            machineMap.classList.toggle('is-layout', layout);
            document.querySelectorAll('[data-map-view]').forEach((button) => {
                button.classList.toggle('is-on', button.dataset.mapView === (layout ? 'layout' : 'cards'));
            });
            try {
                localStorage.setItem('em-map-view', layout ? 'layout' : 'cards');
            } catch (e) {}
            if (!layout) {
                fitKioskMapGrid();
            }
        }
        try {
            if (!operatorKiosk && localStorage.getItem('em-map-view') === 'layout') {
                setMachineMapView('layout');
            }
        } catch (e) {}
    </script>
    @endpush
</x-app-layout>
