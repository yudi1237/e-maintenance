<x-app-layout>
    <div class="em-shell py-8">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke Dashboard</a>

        <x-alert />

        <div class="mt-4 em-glass rounded-2xl p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Tiket Input OP</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Tiket #{{ $ticket->id }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $ticket->stop_date_time_label }} · Mesin {{ $ticket->machine_number }} · {{ $ticket->machine_name_display }}</p>
                </div>
                <div class="text-right space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Status</p>
                        @if ($report)
                            <x-admin.report-status-form :report="$report" :status-labels="$statusLabels" />
                        @else
                            <span class="inline-flex items-center rounded-lg border px-3 py-2 text-sm font-semibold {{ $ticket->status_badge_class }}" style="color: {{ \App\Support\RepairStatus::legendColor($ticket->status ?? \App\Support\RepairStatus::OPEN) }};">
                                {{ $ticket->status_label }}
                            </span>
                        @endif
                    </div>
                    <x-admin.approval-progress :status="$report->status_perbaikan ?? $ticket->status" :report="$report" class="ml-auto" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-8">
                <div><span class="text-slate-500">Nama Barang</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $ticket->item_name }}</p></div>
                <div><span class="text-slate-500">Nama Mesin</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $ticket->machine_name_display }}</p></div>
                <div>
                    <span class="text-slate-500">Operator</span>
                    <p class="mt-0.5 text-slate-900 dark:text-white">{{ $ticket->operator_name }} <span class="text-slate-500">· NPK {{ $ticket->npk }}</span></p>
                </div>
                <div>
                    <span class="text-slate-500">PIC Teknisi</span>
                    <p class="mt-0.5 text-slate-900 dark:text-white">{{ $ticket->pic_name }}</p>
                </div>
            </div>

            <div class="space-y-5 border-t border-slate-200 pt-6 dark:border-white/10">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Masalah</h3>
                    <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $ticket->problem }}</p>
                </div>
                @if ($ticket->chronology)
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Kronologi</h3>
                        <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $ticket->chronology }}</p>
                    </div>
                @endif
            </div>

            @if ($report)
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Laporan perbaikan {{ $report->isStep2Completed() ? 'sudah dilengkapi teknisi.' : 'sudah dibuat, Form 2 masih belum lengkap.' }}
                    </p>
                    <a href="{{ route('admin.reports.show', $report) }}" class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-sm font-medium">Lihat laporan →</a>
                </div>
            @else
                <p class="mt-6 text-sm rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                    Tiket masih menunggu teknisi. {{ $ticket->isOpen() ? 'Belum ada yang mengambil tiket ini.' : 'Tiket sudah On Progress, laporan perbaikan belum diisi.' }}
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
