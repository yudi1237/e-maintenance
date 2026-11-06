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
                <h1 class="mt-2 text-2xl sm:text-4xl font-bold text-white leading-tight">Tiket OP</h1>
                <p id="last-updated" class="mt-3 text-xs text-slate-400">Memuat data...</p>
            </div>
        </div>

        <div class="em-shell pb-10 -mt-4 sm:pb-12 sm:-mt-6">
            <x-flash-popup />
            <x-data-terkirim-modal />

            <section class="mb-6 em-glass rounded-2xl p-5 sm:p-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Pengisian 24 jam terakhir</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tiket OP dan Preventive yang Anda simpan bisa dibuka dan diedit lagi selama 24 jam.</p>
                @if ($recentSubmissions->isEmpty())
                    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Belum ada pengisian dalam 24 jam terakhir.</p>
                @else
                    <ul class="mt-4 divide-y divide-slate-200 dark:divide-white/10">
                        @foreach ($recentSubmissions as $item)
                            <li class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600 dark:text-cyan-300">{{ $item['kind'] }}</p>
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $item['title'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $item['at']?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} · {{ $item['meta'] }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ $item['url'] }}" class="em-btn-secondary justify-center">Lihat</a>
                                    @if ($item['edit_url'])
                                        <a href="{{ $item['edit_url'] }}" class="em-btn-primary justify-center">Edit</a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <div id="operator-tickets-board" data-signature="{{ $ticketsSignature }}">
                @include('partials.operator-tickets', ['operatorReports' => $operatorReports])
            </div>
            <div id="area-tickets-board" class="mt-6" data-signature="{{ $areaSignature }}">
                @include('partials.area-operator-tickets', ['areaReports' => $areaReports])
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const ticketsUrl = @json(route('home.tickets'));
        const board = document.getElementById('operator-tickets-board');
        const areaBoard = document.getElementById('area-tickets-board');
        const lastUpdatedEl = document.getElementById('last-updated');
        let lastSignature = board?.dataset.signature || null;
        let lastAreaSignature = areaBoard?.dataset.signature || null;

        function boardIsBusy() {
            const modal = document.getElementById('pic-pin-modal');
            if (modal && !modal.classList.contains('hidden')) {
                return true;
            }
            const active = document.activeElement;
            const inside = (node) => Boolean(active && node && node.contains(active) && (active.tagName === 'SELECT' || active.tagName === 'BUTTON' || active.tagName === 'INPUT'));
            return inside(board) || inside(areaBoard);
        }

        async function refreshTickets() {
            if (document.hidden || boardIsBusy()) {
                return;
            }

            try {
                const params = new URLSearchParams(window.location.search);
                const url = params.toString() ? `${ticketsUrl}?${params.toString()}` : ticketsUrl;
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                });
                if (!response.ok) return;

                const data = await response.json();
                if (lastUpdatedEl && data.updated_at) {
                    lastUpdatedEl.innerHTML = '<span class="text-cyan-300">●</span> Terakhir diperbarui: <span class="text-slate-200">' + data.updated_at + '</span>';
                }

                if (board && data.signature !== lastSignature) {
                    board.innerHTML = data.html;
                    lastSignature = data.signature;
                    board.dataset.signature = data.signature;
                }
                if (areaBoard && data.area_signature !== lastAreaSignature) {
                    areaBoard.innerHTML = data.area_html;
                    lastAreaSignature = data.area_signature;
                    areaBoard.dataset.signature = data.area_signature;
                }
            } catch (e) {
                console.error('Gagal memuat tiket OP', e);
            }
        }

        refreshTickets();
        setInterval(refreshTickets, 5000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                refreshTickets();
            }
        });
    </script>
    @endpush
</x-app-layout>
