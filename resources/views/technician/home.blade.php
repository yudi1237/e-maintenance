<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 em-page-hero">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative">
                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/30 bg-cyan-500/10 px-3 py-1 text-xs font-medium text-cyan-200">
                    <span class="em-pulse-live relative pl-3">Live</span>
                    <span class="text-cyan-200/80">Update setiap 5 detik</span>
                </div>
                <p class="mt-4 text-cyan-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                <h1 class="mt-2 text-2xl sm:text-4xl font-bold text-white leading-tight">Beranda</h1>
                <p id="last-updated" class="mt-3 text-xs text-slate-400">Memuat data...</p>
            </div>
        </div>

        <div class="em-shell pb-10 -mt-4 sm:pb-12 sm:-mt-6">
            <x-flash-popup />
            <x-data-terkirim-modal />

            <section id="machine-board" class="em-glass rounded-2xl p-4 sm:p-5">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Kartu {{ $machineTotal }} Mesin</h2>
                    <button type="button" id="machine-fullscreen-btn" class="em-qr-btn" title="Tampilkan kartu mesin dalam layar penuh">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/></svg>
                        <span data-fullscreen-label>Fullscreen</span>
                    </button>
                </div>
                <div class="mb-4 flex flex-wrap gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full border border-slate-500 bg-slate-600"></span> Standby</span>
                    <span class="flex items-center gap-2"><span class="em-machine-alert-dot h-3 w-3 rounded-full"></span> OP belum diambil</span>
                    <span class="flex items-center gap-2"><span class="em-machine-pending-dot h-3 w-3 rounded-full"></span> OP On Progress</span>
                    <span class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full" style="background:#22c55e"></span> Running</span>
                    <span class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full" style="background:#ef4444"></span> Breakdown</span>
                </div>
                <div id="machine-grid" class="grid grid-cols-3 gap-2 sm:grid-cols-5 sm:gap-3 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12">
                    @forelse ($machines as $machine)
                        @php
                            $number = (int) $machine->machine_number;
                            $stat = $machineStats[$number] ?? ['op' => 0, 'findings' => 0, 'pm_due' => null, 'alert' => 'idle'];
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
                                default => 'hidden',
                            };
                            $pmCategories = $preventiveToday[$number] ?? [];
                        @endphp
                        <div
                            data-machine="{{ $number }}"
                            data-alert="{{ $alert }}"
                            data-op-count="{{ (int) ($stat['op'] ?? 0) }}"
                            title="{{ trim($machine->view_label.($machine->mold ? ' · '.$machine->mold->name : '')) }}"
                            class="machine-card em-machine-card relative {{ $alertClass }}"
                        >
                            <span class="machine-running-mark {{ $markClass }}" data-machine="{{ $number }}" data-status="{{ $markStatus }}"></span>
                            <span class="machine-card-kicker text-[10px] uppercase tracking-widest text-slate-500">Mesin</span>
                            <span class="em-machine-num mt-0.5 text-2xl font-bold text-slate-800 dark:text-slate-200">{{ $number }}</span>
                            <span class="machine-card-name mt-1 max-w-full truncate text-[10px] text-slate-500 dark:text-slate-400">{{ $machine->view_label }}</span>
                            @if ($machine->mold)
                                <span class="machine-card-name machine-card-mold block max-w-full truncate text-[10px] font-semibold">{{ $machine->mold->name }}</span>
                            @endif
                            <span class="machine-card-meta mt-2 w-full space-y-0.5 text-left text-[9px] leading-tight text-slate-600 dark:text-slate-400">
                                <span class="block truncate">OP : <span class="machine-op-count font-semibold text-slate-800 dark:text-slate-200">{{ (int) ($stat['op'] ?? 0) }}</span></span>
                                <span class="block truncate">Log Temuan : <span class="machine-finding-count font-semibold text-slate-800 dark:text-slate-200">{{ (int) ($stat['findings'] ?? 0) }}</span></span>
                                <span class="block">
                                    <span class="block truncate">Planned PM Due :</span>
                                    <span class="machine-pm-due block font-semibold tabular-nums text-slate-800 dark:text-slate-200">{{ ($stat['pm_due'] ?? null) === null ? '—' : $stat['pm_due'] }}</span>
                                </span>
                            </span>
                            <span class="preventive-badges mt-1 flex min-h-[1.1rem] flex-wrap justify-center gap-0.5" data-preventive-badges>
                                @foreach ($pmCategories as $category)
                                    <span class="rounded bg-emerald-100 px-1 py-0.5 text-[9px] font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">{{ $category }}</span>
                                @endforeach
                            </span>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl p-6 text-center text-sm text-slate-400">Belum ada data mesin untuk ditampilkan.</div>
                    @endforelse
                </div>
            </section>
        </div>
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
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
        .em-machine-card--active .em-machine-num { color: #991b1b; }
        html.dark .em-machine-card--active .em-machine-num { color: #fff7f7; }
        .em-machine-card--active .machine-card-kicker,
        .em-machine-card--active .machine-card-name,
        .em-machine-card--active .machine-card-meta { color: #7f1d1d; font-weight: 600; }
        html.dark .em-machine-card--active .machine-card-kicker,
        html.dark .em-machine-card--active .machine-card-name,
        html.dark .em-machine-card--active .machine-card-meta { color: #fecaca; }
        .em-machine-card--active .machine-op-count,
        .em-machine-card--active .machine-finding-count,
        .em-machine-card--active .machine-pm-due { color: #450a0a; font-weight: 700; }
        html.dark .em-machine-card--active .machine-op-count,
        html.dark .em-machine-card--active .machine-finding-count,
        html.dark .em-machine-card--active .machine-pm-due { color: #ffffff; }
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
        .em-machine-card--pending .em-machine-num { color: #854d0e; }
        html.dark .em-machine-card--pending .em-machine-num { color: #fef9c3; }
        .em-machine-pending-dot { background-color: #eab308; animation: none; }
        html.em-fs-persist .em-page-hero,
        html.em-fs-persist .em-shell > :not(#machine-board) { display: none !important; }
        html.em-fs-persist main { overflow: hidden; }
        html.em-fs-persist #machine-board {
            position: fixed;
            inset: 0;
            z-index: 40;
            margin: 0;
            border-radius: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #f8fafc;
        }
        html.dark.em-fs-persist #machine-board { background: #020617; }
        html.em-fs-persist #machine-grid { flex: 1 1 auto; min-height: 0; align-content: stretch; }
        html.em-fs-persist:has(#machine-fullscreen-btn) #em-fs-exit { display: none !important; }
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
    </style>
    <script>
        const machinesUrl = @json(route('home.machines'));
        const lastUpdatedEl = document.getElementById('last-updated');

        function paintMachineMark(mark, status) {
            mark.classList.toggle('is-running', status === 'running');
            mark.classList.toggle('is-breakdown', status === 'breakdown');
            mark.classList.toggle('is-standby', status === 'standby');
            mark.classList.toggle('hidden', status === '');
            mark.dataset.status = status;
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
        }

        function applyCardState(card, stat) {
            const alert = stat?.alert === 'active' || stat?.alert === 'pending' || stat?.alert === 'approval' ? stat.alert : 'idle';
            const opEl = card.querySelector('.machine-op-count');
            const findingEl = card.querySelector('.machine-finding-count');
            const pmEl = card.querySelector('.machine-pm-due');
            if (opEl) opEl.textContent = String(stat?.op || 0);
            if (findingEl) findingEl.textContent = String(stat?.findings || 0);
            if (pmEl) pmEl.textContent = stat?.pm_due ? String(stat.pm_due) : '—';
            card.dataset.opCount = String(stat?.op || 0);
            card.dataset.alert = alert;
            card.classList.remove('em-machine-card--idle', 'em-machine-card--active', 'em-machine-card--pending', 'em-machine-card--approval');
            card.classList.add({
                active: 'em-machine-card--active',
                pending: 'em-machine-card--pending',
                approval: 'em-machine-card--approval',
            }[alert] || 'em-machine-card--idle');
        }

        function applyPreventiveBadges(card, categories) {
            const wrap = card.querySelector('[data-preventive-badges]');
            if (!wrap) return;
            wrap.innerHTML = (categories || []).map((category) => '<span class="rounded bg-emerald-100 px-1 py-0.5 text-[9px] font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">' + category + '</span>').join('');
        }

        async function refreshMachines() {
            try {
                const response = await fetch(machinesUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                });
                if (!response.ok) return;
                const data = await response.json();
                if (lastUpdatedEl && data.updated_at) {
                    lastUpdatedEl.innerHTML = '<span class="text-cyan-300">●</span> Terakhir diperbarui: <span class="text-slate-200">' + data.updated_at + '</span>';
                }
                document.querySelectorAll('.machine-card').forEach((card) => {
                    const num = card.dataset.machine;
                    const stat = (data.machines && (data.machines[num] || data.machines[Number(num)])) || { op: 0, findings: 0, pm_due: null, alert: 'idle' };
                    applyCardState(card, stat);
                    applyPreventiveBadges(card, (data.preventive_today && data.preventive_today[num]) || []);
                });
                applyRunningMarks(data.running || [], data.breakdown || [], data.standby || []);
                noteNewOpTickets();
            } catch (e) {
                console.error('Gagal memuat kartu mesin', e);
            }
        }

        const opAlarm = document.getElementById('op-new-alarm');
        const seenOp = new Map();
        let alarmPlaying = false;
        let alarmQueued = false;
        let alarmPending = false;

        function snapshotOpTickets() {
            document.querySelectorAll('.machine-card').forEach((card) => {
                seenOp.set(String(card.dataset.machine), {
                    op: Number(card.dataset.opCount || 0),
                    alert: card.dataset.alert || 'idle',
                });
            });
        }

        function playNewOpAlarm() {
            if (window.frameElement) return;
            if (!opAlarm || alarmPlaying) {
                return;
            }
            alarmPlaying = true;
            alarmQueued = false;
            opAlarm.loop = false;
            opAlarm.muted = false;
            opAlarm.volume = 1;
            try {
                if (opAlarm.readyState >= 1) opAlarm.currentTime = 0;
            } catch (e) {}
            opAlarm.onended = () => {
                alarmPlaying = false;
                alarmPending = false;
                if (alarmQueued) {
                    alarmQueued = false;
                    playNewOpAlarm();
                }
            };
            const started = opAlarm.play();
            if (started && typeof started.then === 'function') {
                started.then(() => {
                    alarmPending = false;
                }).catch(() => {
                    alarmPlaying = false;
                    alarmPending = true;
                });
            }
        }

        function noteNewOpTickets() {
            let hasNew = false;
            document.querySelectorAll('.machine-card').forEach((card) => {
                const num = String(card.dataset.machine);
                const next = {
                    op: Number(card.dataset.opCount || 0),
                    alert: card.dataset.alert || 'idle',
                };
                const prev = seenOp.get(num);
                if (prev && (next.op > prev.op || (next.alert === 'active' && prev.alert !== 'active'))) {
                    hasNew = true;
                }
                seenOp.set(num, next);
            });
            if (hasNew || alarmPending) playNewOpAlarm();
        }

        function unlockNewOpAlarm() {
            if (!alarmPending) return;
            alarmPending = false;
            playNewOpAlarm();
        }

        document.addEventListener('pointerdown', unlockNewOpAlarm);
        document.addEventListener('keydown', unlockNewOpAlarm);
        snapshotOpTickets();
        refreshMachines();
        setInterval(refreshMachines, 5000);

        const fullscreenBtn = document.getElementById('machine-fullscreen-btn');
        const fullscreenLabel = fullscreenBtn?.querySelector('[data-fullscreen-label]');

        function boardIsFullscreen() {
            return document.documentElement.classList.contains('em-fs-persist');
        }

        function fitMachineGrid() {
            const grid = document.getElementById('machine-grid');
            if (!grid) return;
            if (!boardIsFullscreen()) {
                grid.style.gridTemplateColumns = '';
                grid.style.gridTemplateRows = '';
                return;
            }
            const count = grid.querySelectorAll('.machine-card').length;
            if (count === 0) return;
            const width = grid.clientWidth;
            const height = grid.clientHeight;
            if (width < 80 || height < 80) return;
            const maxCols = 16;
            let cols = Math.max(8, Math.min(maxCols, Math.round(Math.sqrt(count * (width / Math.max(height, 1))))));
            let rows = Math.ceil(count / cols);
            while (rows > 6 && cols < maxCols) {
                cols += 1;
                rows = Math.ceil(count / cols);
            }
            while (height / rows < 72 && cols < maxCols) {
                cols += 1;
                rows = Math.ceil(count / cols);
            }
            grid.style.gridTemplateColumns = 'repeat(' + cols + ', minmax(0, 1fr))';
            grid.style.gridTemplateRows = 'repeat(' + rows + ', minmax(0, 1fr))';
        }

        function syncFullscreenButton() {
            const on = boardIsFullscreen();
            if (fullscreenLabel) fullscreenLabel.textContent = on ? 'Keluar' : 'Fullscreen';
            if (fullscreenBtn) fullscreenBtn.title = on ? 'Keluar dari layar penuh' : 'Tampilkan kartu mesin dalam layar penuh';
            window.requestAnimationFrame(fitMachineGrid);
            window.setTimeout(fitMachineGrid, 120);
        }

        fullscreenBtn?.addEventListener('click', () => {
            if (boardIsFullscreen() && typeof window.emExitFullscreen === 'function') {
                window.emExitFullscreen();
                return;
            }
            if (typeof window.emEnterFullscreenShell === 'function') {
                window.emEnterFullscreenShell();
            }
        });
        window.addEventListener('resize', () => {
            if (boardIsFullscreen()) fitMachineGrid();
        });
        syncFullscreenButton();
    </script>
    @endpush
    <video id="op-new-alarm" preload="auto" playsinline src="{{ asset('sounds/op-baru.mp4') }}" style="position:fixed;left:0;bottom:0;width:2px;height:2px;opacity:0;pointer-events:none;"></video>
</x-app-layout>
