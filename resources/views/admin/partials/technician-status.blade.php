<section id="technician-status" class="em-glass rounded-2xl p-5">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Technician Status</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Diperbarui setiap 5 detik</p>
        </div>
        <div class="flex flex-wrap gap-3 text-xs text-slate-500 dark:text-slate-400">
            <span class="inline-flex items-center gap-2"><span class="em-tech-badge is-idle">Standby / Available</span></span>
            <span class="inline-flex items-center gap-2"><span class="em-tech-badge is-busy">On Job / Assigned</span></span>
            <span class="inline-flex items-center gap-2"><span class="em-tech-badge is-off">Off-duty</span></span>
            <span class="inline-flex items-center gap-2"><span class="em-tech-badge is-overtime">Overtime</span></span>
        </div>
    </div>
    @php
        $technicianChartCounts = ['idle' => 0, 'busy' => 0, 'off' => 0, 'overtime' => 0];
        foreach ($technicianStatuses as $technicianChartRow) {
            $technicianChartKey = $technicianChartRow['state'] ?? 'idle';
            if (! array_key_exists($technicianChartKey, $technicianChartCounts)) {
                $technicianChartKey = 'idle';
            }
            $technicianChartCounts[$technicianChartKey]++;
        }
        $technicianShiftBuckets = [1 => [], 2 => [], 3 => [], 0 => []];
        foreach ($technicianStatuses as $technicianStatus) {
            $shiftKey = (int) ($technicianStatus['shift'] ?? 0);
            if (! in_array($shiftKey, [1, 2, 3], true)) {
                $shiftKey = 0;
            }
            $technicianShiftBuckets[$shiftKey][] = $technicianStatus;
        }
        $technicianShiftSections = [
            1 => ['title' => 'Shift 1 (00:00 - 08:00)', 'rows' => $technicianShiftBuckets[1]],
            2 => ['title' => 'Shift 2 (08:00 - 16:00)', 'rows' => $technicianShiftBuckets[2]],
            3 => ['title' => 'Shift 3 (16:00 - 24:00)', 'rows' => $technicianShiftBuckets[3]],
        ];
        if ($technicianShiftBuckets[0] !== []) {
            $technicianShiftSections[0] = ['title' => 'Belum masuk shift', 'rows' => $technicianShiftBuckets[0]];
        }
    @endphp
    <div class="relative mt-3 h-24 max-w-md">
        <canvas id="technician-status-chart"></canvas>
    </div>
    <form method="POST" action="{{ route('admin.technicians.shifts') }}" class="em-tech-shift-form">
        @csrf
        <div id="technician-status-list">
            @foreach ($technicianShiftSections as $technicianShiftSection)
                <div class="em-tech-shift">
                    <h3 class="em-tech-shift-title">{{ $technicianShiftSection['title'] }}</h3>
                    <div class="em-tech-list">
                        @foreach ($technicianShiftSection['rows'] as $technicianStatus)
                            @php
                                $techBadgeClass = match ($technicianStatus['state']) {
                                    'busy' => 'is-busy',
                                    'off' => 'is-off',
                                    'overtime' => 'is-overtime',
                                    default => 'is-idle',
                                };
                            @endphp
                            <div class="em-tech-row" data-technician="{{ $technicianStatus['id'] }}">
                                <p class="em-tech-name">{{ $technicianStatus['name'] }}</p>
                                <span class="em-tech-badge {{ $techBadgeClass }}">{{ $technicianStatus['badge'] }}</span>
                                @if ($technicianStatus['detail'])
                                    <p class="em-tech-detail">{{ $technicianStatus['detail'] }}</p>
                                @endif
                                <label class="em-tech-shift-label">
                                    Shift
                                    <select name="shifts[{{ $technicianStatus['id'] }}]" class="em-tech-shift-select">
                                        <option value="" @selected(empty($technicianStatus['shift']) && empty($technicianStatus['off_duty']) && empty($technicianStatus['overtime']))></option>
                                        <option value="1" @selected(empty($technicianStatus['off_duty']) && empty($technicianStatus['overtime']) && (int) ($technicianStatus['shift'] ?? 0) === 1)>1</option>
                                        <option value="2" @selected(empty($technicianStatus['off_duty']) && empty($technicianStatus['overtime']) && (int) ($technicianStatus['shift'] ?? 0) === 2)>2</option>
                                        <option value="3" @selected(empty($technicianStatus['off_duty']) && empty($technicianStatus['overtime']) && (int) ($technicianStatus['shift'] ?? 0) === 3)>3</option>
                                        <option value="off" @selected(! empty($technicianStatus['off_duty']))>Off-duty</option>
                                        <option value="overtime" @selected(! empty($technicianStatus['overtime']) && empty($technicianStatus['off_duty']))>Overtime</option>
                                    </select>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="em-tech-shift-save">Simpan mapping shift</button>
    </form>
</section>

<style>
    .em-tech-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem; margin-top: 1rem; }
    .em-tech-row { border: 1px solid rgba(148, 163, 184, 0.35); border-radius: 0.9rem; padding: 0.75rem 0.85rem; }
    html.dark .em-tech-row { border-color: rgba(255, 255, 255, 0.08); background: rgba(15, 23, 42, 0.45); }
    .em-tech-name { margin: 0; font-weight: 700; color: #0f172a; }
    html.dark .em-tech-name { color: #f8fafc; }
    .em-tech-badge { display: inline-flex; margin-top: 0.45rem; border-radius: 999px; font-size: 11px; font-weight: 800; line-height: 1.2; padding: 0.22rem 0.55rem; }
    .em-tech-badge.is-idle { background: #dcfce7; color: #166534; }
    .em-tech-badge.is-busy { background: #fee2e2; color: #991b1b; }
    .em-tech-badge.is-off { background: #e2e8f0; color: #475569; }
    html.dark .em-tech-badge.is-off { background: #334155; color: #cbd5e1; }
    .em-tech-badge.is-overtime { background: #ffedd5; color: #9a3412; }
    html.dark .em-tech-badge.is-overtime { background: rgba(154, 52, 18, 0.45); color: #fdba74; }
    .em-tech-detail { margin: 0.4rem 0 0; font-size: 12px; font-weight: 600; color: #475569; }
    html.dark .em-tech-detail { color: #cbd5e1; }
    .em-tech-shift { margin-top: 1.1rem; }
    .em-tech-shift-title { margin: 0 0 0.15rem; font-size: 0.8rem; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; color: #334155; }
    html.dark .em-tech-shift-title { color: #e2e8f0; }
    .em-tech-shift-label { display: block; margin-top: 0.55rem; font-size: 11px; font-weight: 700; color: #64748b; }
    html.dark .em-tech-shift-label { color: #94a3b8; }
    .em-tech-shift-select { display: block; width: 100%; margin-top: 0.25rem; border-radius: 0.55rem; border: 1px solid #cbd5e1; background: #fff; color: #0f172a; font-size: 13px; font-weight: 600; padding: 0.35rem 0.5rem; }
    html.dark .em-tech-shift-select { border-color: rgba(148, 163, 184, 0.45); background: #0f172a; color: #f8fafc; }
    .em-tech-shift-save { margin-top: 1rem; border: 0; border-radius: 0.7rem; background: #0891b2; color: #fff; font-size: 0.85rem; font-weight: 700; padding: 0.55rem 0.9rem; cursor: pointer; }
    .em-tech-shift-save:hover { background: #0e7490; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const technicianStatusUrl = @json(route('admin.technicians.status'));
    const technicianChartSeed = @json($technicianChartCounts);
    let technicianStatusChart = null;

    function technicianChartValues(rows) {
        const counts = { idle: 0, busy: 0, off: 0, overtime: 0 };
        (rows || []).forEach((row) => {
            const state = counts[row.state] === undefined ? 'idle' : row.state;
            counts[state] += 1;
        });
        return [counts.idle, counts.busy, counts.off, counts.overtime];
    }

    if (document.getElementById('technician-status-chart') && typeof Chart !== 'undefined') {
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
                    x: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } } },
                    y: { ticks: { font: { size: 9 } }, grid: { display: false } },
                },
            },
        });
    }

    function applyTechnicianStatus(rows) {
        if (!Array.isArray(rows)) return;
        rows.forEach((row) => {
            const item = document.querySelector('#technician-status-list [data-technician="' + String(row.id) + '"]');
            if (!item) return;
            const badge = item.querySelector('.em-tech-badge');
            if (badge) {
                badge.className = 'em-tech-badge ' + ({ busy: 'is-busy', off: 'is-off', overtime: 'is-overtime' }[row.state] || 'is-idle');
                badge.textContent = row.badge || '';
            }
            let detail = item.querySelector('.em-tech-detail');
            if (row.detail) {
                if (!detail) {
                    detail = document.createElement('p');
                    detail.className = 'em-tech-detail';
                    badge ? badge.insertAdjacentElement('afterend', detail) : item.appendChild(detail);
                }
                detail.textContent = row.detail;
            } else if (detail) {
                detail.remove();
            }
        });
        if (technicianStatusChart) {
            technicianStatusChart.data.datasets[0].data = technicianChartValues(rows);
            technicianStatusChart.update('none');
        }
    }

    async function refreshTechnicianStatus() {
        try {
            const response = await fetch(technicianStatusUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
            });
            if (!response.ok) return;
            const data = await response.json();
            applyTechnicianStatus(data.technicians || []);
        } catch (error) {
            console.error('Gagal memuat status teknisi', error);
        }
    }

    refreshTechnicianStatus();
    setInterval(refreshTechnicianStatus, 5000);
</script>
@endpush
