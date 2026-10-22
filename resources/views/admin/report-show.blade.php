<x-app-layout>
    <div class="em-shell py-8">
        <a href="{{ route('admin.machines.show', $report->machine_number) }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke Mesin {{ $report->machine_number }}</a>

        <x-alert />

        <div class="mt-4 em-glass rounded-2xl p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Detail Laporan</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $report->report_date->format('d/m/Y') }} · Mesin {{ $report->machine_number }} · {{ $report->pic_name }}</p>
                </div>
                <div class="text-right space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Status Perbaikan</p>
                        <x-admin.report-status-form :report="$report" :status-labels="$statusLabels" />
                    </div>
                    <x-admin.approval-progress :status="$report->status_perbaikan" :report="$report" class="ml-auto" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-8">
                <div><span class="text-slate-500">Nama Mesin</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->machine_name_display }}</p></div>
                <div><span class="text-slate-500">Kategori</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->damage_category_display }}</p></div>
                <div><span class="text-slate-500">Mold/Assy/Utility</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->mold_assy_utility ?? '-' }}</p></div>
                <div><span class="text-slate-500">Shift</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->work_shift_label }}</p></div>
                <div><span class="text-slate-500">Waktu Perbaikan</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->repair_started_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} — {{ $report->repair_finished_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p></div>
                <div><span class="text-slate-500">Ada OP</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->ada_op_label }}</p></div>
            </div>

            @if ($report->operatorReport)
                <div class="mb-8 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-4 dark:border-cyan-500/30 dark:bg-cyan-500/10">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700 dark:text-cyan-300">Tiket Input OP #{{ $report->operatorReport->id }}</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $report->operatorReport->item_name }} · {{ $report->operatorReport->operator_name }} · {{ $report->operatorReport->stop_date_time_label }}</p>
                            <p class="mt-2 text-sm text-slate-600 whitespace-pre-wrap dark:text-slate-300">{{ $report->operatorReport->problem }}</p>
                        </div>
                        <a href="{{ route('admin.operator-reports.show', $report->operatorReport) }}" class="text-cyan-600 hover:underline dark:text-cyan-400 text-xs font-medium">Detail tiket →</a>
                    </div>
                </div>
            @endif

            @if ($report->isStep2Completed())
                <div class="space-y-5 border-t border-slate-200 pt-6 dark:border-white/10">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Masalah</h3>
                        <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->masalah }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Analisa Penyebab Kerusakan</h3>
                        <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->analisa_penyebab_kerusakan }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Kategori Penyebab Kerusakan</h3>
                        <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->damage_cause_category_label }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Tindakan Perbaikan</h3>
                        <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->tindakan_perbaikan }}</p>
                    </div>
                    @if ($report->penggunaan_sparepart)
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Penggunaan Sparepart</h3>
                            <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->penggunaan_sparepart }}</p>
                        </div>
                    @endif
                    @if ($report->evidence_url)
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Evidence</h3>
                            <a href="{{ $report->evidence_url }}" target="_blank" class="text-cyan-600 hover:underline dark:text-cyan-400">Lihat file evidence →</a>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-sm rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">Form 2 belum diisi oleh teknisi.</p>
            @endif
        </div>
    </div>
</x-app-layout>
