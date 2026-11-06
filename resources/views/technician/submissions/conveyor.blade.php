<x-app-layout>
    <div class="em-shell py-8">
        <a href="{{ route('home') }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke halaman teknisi</a>

        <x-alert-light />

        <div class="mt-4 em-glass rounded-2xl p-6 sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Preventive Conveyor</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ $report->conveyor_name }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $report->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                · {{ $report->pic_name }}
                · {{ $report->result_label }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Data ini bisa dibuka dan diedit lagi selama 24 jam setelah disimpan.</p>
                </div>
                <a href="{{ route('home.submissions.conveyor.edit', $report) }}" class="em-btn-primary justify-center">Edit</a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div><span class="text-slate-500">Shift</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->work_shift_label }}</p></div>
                <div><span class="text-slate-500">Tanggal Kerja</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->work_date?->format('d/m/Y') }}</p></div>
                <div class="sm:col-span-2"><span class="text-slate-500">Anggota Tim</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->team_members_label }}</p></div>
            </div>

            <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-400">
                            <th class="px-3 py-2">Item Pemeriksaan</th>
                            <th class="px-3 py-2 text-right">Hasil</th>
                            <th class="px-3 py-2">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($report->items ?? [] as $key => $value)
                            <tr>
                                <td class="px-3 py-3 text-slate-800 dark:text-white">{{ $report->itemLabel((string) $key) }}</td>
                                <td class="px-3 py-3 text-right text-slate-700 dark:text-slate-200">{{ $report->itemValueLabel((string) $key) }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ filled($report->item_remarks[$key] ?? null) ? $report->item_remarks[$key] : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-slate-500">Tidak ada item tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (filled($report->penggunaan_sparepart))
                <div class="mt-6">
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Pemakaian Sparepart</h3>
                    <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-200">{{ $report->penggunaan_sparepart }}</p>
                </div>
            @endif

            @if (filled($report->notes))
                <div class="mt-6">
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Catatan</h3>
                    <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-200">{{ $report->notes }}</p>
                </div>
            @endif

            @if ($report->evidence_urls !== [])
                <div class="mt-6">
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Evidence</h3>
                    <ul class="space-y-1">
                        @foreach ($report->evidence_urls as $url)
                            <li><a href="{{ $url }}" target="_blank" class="text-sm text-cyan-600 hover:underline dark:text-cyan-400">Lihat file evidence →</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
