<x-scan-layout>
    <x-slot name="title">Hasil Preventive Mesin {{ $machineNumber }} · e-Maintenance</x-slot>

    <div class="em-shell py-8">
        <a href="{{ route('scan.machines.show', [$machineNumber, $token]) }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali</a>

        <div class="mt-4 mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Hasil Preventive Mesin {{ $machineNumber }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ $machine?->view_label ? $machine->view_label.' · ' : '' }}{{ $filteredReportCount }} hasil ditampilkan dari {{ $reportCount }} preventive tercatat
                </p>
            </div>
            <p class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                Halaman ini hanya untuk melihat hasil. Pengisian preventive dilakukan setelah login teknisi.
            </p>
        </div>

        <form method="GET" action="{{ route('scan.preventive.index', [$machineNumber, $token]) }}" class="em-glass mb-6 rounded-2xl p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Filter Hasil Preventive</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tampilkan hasil berdasarkan periode tanggal</p>
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
                <a href="{{ route('scan.preventive.index', [$machineNumber, $token]) }}" class="em-btn-secondary w-full">Reset</a>
                <button type="submit" class="em-btn-primary w-full justify-center">Filter Hasil</button>
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
                            <th class="px-4 py-3">Shift</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Hasil</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->work_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->machine_name_display }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->category_label }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $report->work_shift_label }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $report->pic_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $report->result_badge_class }}">
                                        {{ $report->result_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('scan.preventive.show', [$machineNumber, $token, $report]) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium">Lihat →</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-500">Belum ada hasil preventive untuk mesin ini.</td>
                            </tr>
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
