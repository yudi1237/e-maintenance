<x-scan-layout>
    <x-slot name="title">Mesin {{ $machineNumber }} · e-Maintenance</x-slot>

    <div class="em-shell py-8 space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Scan QR</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Mesin {{ $machineNumber }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ $machine?->view_label ?: 'Data mesin, hasil preventive, dan log temuan ditampilkan di halaman ini.' }}
                </p>
            </div>
            <a href="{{ route('scan.operator-reports.create', [$machineNumber, $token]) }}" class="em-btn-primary w-fit">
                INPUT OP
            </a>
        </div>

        <x-alert-light />

        @include('partials.machine-specs', ['machine' => $machine, 'machineNumber' => $machineNumber, 'public' => true])

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-white/10">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Riwayat Problem</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Semua tiket OP mesin ini, apa pun statusnya. Perubahan status hanya setelah login.</p>
            </div>
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
                        @forelse ($problemReports as $report)
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
                                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">Belum ada riwayat problem untuk mesin ini.</td>
                                </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($problemReports->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $problemReports->links() }}</div>
            @endif
        </div>

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-white/10">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Hasil Preventive</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Checklist perawatan berkala mesin ini.</p>
            </div>
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
                        @forelse ($preventiveReports as $report)
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
            @if ($preventiveReports->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $preventiveReports->links() }}</div>
            @endif
        </div>

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-white/10">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Log Temuan</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Hanya menampilkan temuan yang sudah Close.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Due Date</th>
                            <th class="px-4 py-3">Nama Mesin</th>
                            <th class="px-4 py-3">Nama Equipment</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($recaps as $recap)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $recap->found_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $recap->due_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-700 max-w-[180px] truncate dark:text-slate-200">{{ $recap->machine_name_display }}</td>
                                <td class="px-4 py-3 text-slate-700 max-w-[200px] truncate dark:text-slate-200">{{ $recap->equipment_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold {{ $recap->status_badge_class }}">
                                        {{ $recap->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs whitespace-pre-wrap dark:text-slate-300">{{ $recap->notesExportText() !== '' ? $recap->notesExportText() : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">Belum ada log temuan Close untuk mesin ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($recaps->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $recaps->links() }}</div>
            @endif
        </div>
    </div>
</x-scan-layout>
