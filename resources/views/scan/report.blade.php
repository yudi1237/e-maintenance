<x-scan-layout>
    <x-slot name="title">Detail Laporan Mesin {{ $machineNumber }} · e-Maintenance</x-slot>

    <div class="em-shell py-8">
        <a href="{{ route('scan.machines.info', [$machineNumber, $token]) }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke Info Mesin {{ $machineNumber }}</a>

        <div class="mt-4 em-glass rounded-2xl p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Detail Laporan</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $report->report_date->format('d/m/Y') }} · Mesin {{ $report->machine_number }} · {{ $report->pic_name }}</p>
                </div>
                <div class="text-right space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Status Perbaikan</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $report->status_badge_class }}">
                            {{ $statusLabels[$report->status_perbaikan] ?? $report->status_perbaikan }}
                        </span>
                        <p class="mt-2 text-[11px] text-slate-400">Tidak dapat diubah di mode lihat</p>
                    </div>
                    <x-admin.approval-progress :status="$report->status_perbaikan" class="ml-auto" />
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
                    @if ($report->workers_label !== '')
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Yang Mengerjakan</h3>
                            <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->workers_label }}</p>
                        </div>
                    @endif
                    @if ($report->evidence_url)
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-2">Evidence</h3>
                            <a href="{{ $report->evidence_url }}" target="_blank" rel="noopener noreferrer" class="text-cyan-600 hover:underline dark:text-cyan-400">Lihat file evidence →</a>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-sm rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">Form 2 belum diisi oleh teknisi.</p>
            @endif
        </div>
    </div>
</x-scan-layout>
