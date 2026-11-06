<x-scan-layout>
    <x-slot name="title">Mesin {{ $machineNumber }} · e-Maintenance</x-slot>

    <div class="em-shell py-8">
        <a href="{{ route('scan.machines.show', [$machineNumber, $token]) }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali</a>

        <div class="mt-4 mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mesin {{ $machineNumber }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ $filteredReportCount }} laporan ditampilkan dari {{ $reportCount }} laporan tercatat
                </p>
            </div>
            <p class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                Halaman ini hanya untuk melihat data. Perubahan status hanya bisa dilakukan setelah login admin.
            </p>
        </div>

        @include('partials.machine-specs', ['machine' => $machine, 'machineNumber' => $machineNumber, 'public' => true])

        <form method="GET" action="{{ route('scan.machines.info', [$machineNumber, $token]) }}" class="em-glass mb-6 rounded-2xl p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Filter Riwayat Problem</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tampilkan riwayat berdasarkan periode tanggal</p>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_1fr_1fr_2fr] md:items-end">
                <div>
                    <label for="start_date" class="em-label">Periode Mulai</label>
                    <input id="start_date" name="start_date" type="date" class="em-input" value="{{ $startDate }}">
                </div>
                <div>
                    <label for="end_date" class="em-label">Periode Akhir</label>
                    <input id="end_date" name="end_date" type="date" class="em-input" value="{{ $endDate }}">
                </div>
                <a href="{{ route('scan.machines.info', [$machineNumber, $token]) }}" class="em-btn-secondary w-full">Reset</a>
                <button type="submit" class="em-btn-primary w-full justify-center">Filter Riwayat</button>
            </div>
            @error('end_date')
                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </form>

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Nama Mesin</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Form 2</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($operatorTickets as $ticket)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $ticket->stop_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $ticket->machine_name_display }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[200px] whitespace-pre-wrap break-words dark:text-slate-300">{{ $ticket->problem }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $ticket->pic_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $ticket->status_badge_class }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-amber-600 text-xs dark:text-amber-400">Belum</span>
                                </td>
                                <td class="px-4 py-3 text-right text-slate-400">—</td>
                            </tr>
                        @endforeach
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->report_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->machine_name_display }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[200px] truncate dark:text-slate-300">{{ $report->damage_category_display }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $report->pic_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $report->status_badge_class }}">
                                        {{ $statusLabels[$report->status_perbaikan] ?? $report->status_perbaikan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($report->isStep2Completed())
                                        <span class="text-emerald-600 text-xs dark:text-emerald-400">✓ Lengkap</span>
                                    @else
                                        <span class="text-amber-600 text-xs dark:text-amber-400">Belum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('scan.reports.show', [$machineNumber, $token, $report]) }}" class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium">Lihat →</a>
                                </td>
                            </tr>
                        @empty
                            @if ($operatorTickets->isEmpty())
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">Tidak ada laporan untuk periode yang dipilih.</td>
                                </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($reports->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $reports->links() }}</div>
            @endif
        </div>
    </div>
</x-scan-layout>
