<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900 em-page-hero">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-emerald-200/90 hover:text-white">← Kembali ke beranda</a>
                <p class="mt-4 text-emerald-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                <h1 class="mt-2 text-2xl sm:text-4xl font-bold text-white leading-tight">{{ \App\Support\PreventiveCategory::hubTitle() }}</h1>
                <div class="mt-4 flex flex-wrap items-center gap-2 sm:gap-3">
                    <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 px-4 py-2 text-sm text-white">
                        <span class="text-slate-300">Tanggal:</span>
                        <strong>{{ $currentDateTime }}</strong>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 px-4 py-2 text-sm text-white">
                        <span class="text-slate-300">PIC aktif:</span>
                        <strong class="text-white">{{ $picName }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="em-shell pb-10 -mt-4 sm:pb-12 sm:-mt-6">
            <x-flash-popup />

            <p id="schedule-last-updated" class="mb-3 text-xs text-slate-500 dark:text-slate-400">Memuat jadwal...</p>
            <div id="preventive-schedule-board" class="mb-6" data-signature="{{ $schedulesSignature }}">
                @include('partials.preventive-schedules', ['schedules' => $schedules])
            </div>

            @php
                $utilityCards = [
                    'MTC' => [
                        'title' => \App\Support\PreventiveCategory::formTitle('MTC'),
                        'desc' => 'Checklist perawatan mold temperature controller.',
                        'icon' => 'mtc',
                        'tone' => 'sky',
                    ],
                    'HOPPER' => [
                        'title' => \App\Support\PreventiveCategory::formTitle('HOPPER'),
                        'desc' => 'Checklist perawatan hopper dryer.',
                        'icon' => 'hopper',
                        'tone' => 'violet',
                    ],
                    'DEHUM' => [
                        'title' => \App\Support\PreventiveCategory::formTitle('DEHUM'),
                        'desc' => 'Checklist perawatan dehumidifier.',
                        'icon' => 'dehum',
                        'tone' => 'teal',
                    ],
                    'ROBOT' => [
                        'title' => \App\Support\PreventiveCategory::formTitle('ROBOT'),
                        'desc' => 'Checklist perawatan robot.',
                        'icon' => 'robot',
                        'tone' => 'rose',
                    ],
                    'HOT' => [
                        'title' => \App\Support\PreventiveCategory::formTitle('HOT'),
                        'desc' => 'Checklist perawatan hot runner.',
                        'icon' => 'hot',
                        'tone' => 'orange',
                    ],
                ];
                $toneClasses = [
                    'emerald' => [
                        'border' => 'hover:border-emerald-400/40',
                        'icon' => 'bg-gradient-to-br from-emerald-400/30 to-emerald-600/10 text-emerald-600 ring-1 ring-emerald-400/30 dark:from-emerald-400/25 dark:to-emerald-700/15 dark:text-emerald-300 dark:ring-emerald-400/25',
                        'link' => 'text-emerald-600 dark:text-emerald-400',
                    ],
                    'amber' => [
                        'border' => 'hover:border-amber-400/40',
                        'icon' => 'bg-gradient-to-br from-amber-400/30 to-amber-600/10 text-amber-600 ring-1 ring-amber-400/30 dark:from-amber-400/25 dark:to-amber-700/15 dark:text-amber-300 dark:ring-amber-400/25',
                        'link' => 'text-amber-600 dark:text-amber-400',
                    ],
                    'sky' => [
                        'border' => 'hover:border-sky-400/40',
                        'icon' => 'bg-gradient-to-br from-sky-400/30 to-sky-600/10 text-sky-600 ring-1 ring-sky-400/30 dark:from-sky-400/25 dark:to-sky-700/15 dark:text-sky-300 dark:ring-sky-400/25',
                        'link' => 'text-sky-600 dark:text-sky-400',
                    ],
                    'violet' => [
                        'border' => 'hover:border-violet-400/40',
                        'icon' => 'bg-gradient-to-br from-violet-400/30 to-violet-600/10 text-violet-600 ring-1 ring-violet-400/30 dark:from-violet-400/25 dark:to-violet-700/15 dark:text-violet-300 dark:ring-violet-400/25',
                        'link' => 'text-violet-600 dark:text-violet-400',
                    ],
                    'teal' => [
                        'border' => 'hover:border-teal-400/40',
                        'icon' => 'bg-gradient-to-br from-teal-400/30 to-teal-600/10 text-teal-600 ring-1 ring-teal-400/30 dark:from-teal-400/25 dark:to-teal-700/15 dark:text-teal-300 dark:ring-teal-400/25',
                        'link' => 'text-teal-600 dark:text-teal-400',
                    ],
                    'rose' => [
                        'border' => 'hover:border-rose-400/40',
                        'icon' => 'bg-gradient-to-br from-rose-400/30 to-rose-600/10 text-rose-600 ring-1 ring-rose-400/30 dark:from-rose-400/25 dark:to-rose-700/15 dark:text-rose-300 dark:ring-rose-400/25',
                        'link' => 'text-rose-600 dark:text-rose-400',
                    ],
                    'orange' => [
                        'border' => 'hover:border-orange-400/40',
                        'icon' => 'bg-gradient-to-br from-orange-400/30 to-orange-600/10 text-orange-600 ring-1 ring-orange-400/30 dark:from-orange-400/25 dark:to-orange-700/15 dark:text-orange-300 dark:ring-orange-400/25',
                        'link' => 'text-orange-600 dark:text-orange-400',
                    ],
                ];
            @endphp

            <div class="mt-2" x-data="{ showAdditional: false }">
                <button
                    type="button"
                    class="em-btn-secondary w-full sm:w-auto"
                    @click="showAdditional = !showAdditional"
                    x-text="showAdditional ? 'Sembunyikan Additional Preventive' : 'Additional Preventive'"
                >Additional Preventive</button>

                <div class="mt-4 space-y-4" x-show="showAdditional" x-cloak>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a
                            href="{{ route('preventive.create') }}"
                            class="em-glass group w-full rounded-2xl p-6 text-left transition hover:-translate-y-0.5 hover:border-emerald-400/40"
                        >
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $toneClasses['emerald']['icon'] }}">
                                <x-preventive.menu-icon name="mesin" />
                            </div>
                            <h2 class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">Preventive Mesin</h2>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Checklist PM1, PM2, PM3, PM4, P1E, dan JSW EL.</p>
                            <span class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                Buka form
                                <span class="transition group-hover:translate-x-0.5">→</span>
                            </span>
                        </a>
                        <a
                            href="{{ route('preventive-conveyor.create') }}"
                            class="em-glass group w-full rounded-2xl p-6 text-left transition hover:-translate-y-0.5 hover:border-amber-400/40"
                        >
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $toneClasses['amber']['icon'] }}">
                                <x-preventive.menu-icon name="conveyor" />
                            </div>
                            <h2 class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">Preventive Conveyor</h2>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Isi checklist perawatan berkala conveyor.</p>
                            <span class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-amber-600 dark:text-amber-400">
                                Buka form
                                <span class="transition group-hover:translate-x-0.5">→</span>
                            </span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        @foreach ($utilityCards as $destination => $card)
                            @php $tone = $toneClasses[$card['tone']]; @endphp
                            <a
                                href="{{ route('preventive.create', ['category' => $destination]) }}"
                                class="em-glass group w-full rounded-2xl p-6 text-left transition hover:-translate-y-0.5 {{ $tone['border'] }}"
                            >
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $tone['icon'] }}">
                                    <x-preventive.menu-icon :name="$card['icon']" />
                                </div>
                                <h2 class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">{{ $card['title'] }}</h2>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $card['desc'] }}</p>
                                <span class="mt-5 inline-flex items-center gap-1 text-sm font-semibold {{ $tone['link'] }}">
                                    Buka form
                                    <span class="transition group-hover:translate-x-0.5">→</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const schedulesUrl = @json(route('preventive.schedules'));
        const board = document.getElementById('preventive-schedule-board');
        const lastUpdatedEl = document.getElementById('schedule-last-updated');
        let lastSignature = board?.dataset.signature || null;

        async function refreshSchedules() {
            if (document.hidden) {
                return;
            }

            try {
                const params = new URLSearchParams(window.location.search);
                const url = params.toString() ? `${schedulesUrl}?${params.toString()}` : schedulesUrl;
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                });
                if (!response.ok) return;

                const data = await response.json();
                if (lastUpdatedEl && data.updated_at) {
                    lastUpdatedEl.innerHTML = '<span class="text-emerald-600 dark:text-emerald-400">●</span> Terakhir diperbarui: <span class="text-slate-600 dark:text-slate-300">' + data.updated_at + '</span>';
                }

                if (!board || data.signature === lastSignature) {
                    return;
                }

                board.innerHTML = data.html;
                lastSignature = data.signature;
                board.dataset.signature = data.signature;
            } catch (e) {
                console.error('Gagal memuat jadwal preventive', e);
            }
        }

        refreshSchedules();
        setInterval(refreshSchedules, 5000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                refreshSchedules();
            }
        });
    </script>
    @endpush
</x-app-layout>
