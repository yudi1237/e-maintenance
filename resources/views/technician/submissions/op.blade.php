<x-app-layout>
    <div class="em-shell py-8">
        <a href="{{ route('home') }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke halaman teknisi</a>

        <x-alert-light />

        <div class="mt-4 em-glass rounded-2xl p-6 sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Tiket OP</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Detail pengisian</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $report->step2_completed_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                · Mesin {{ $report->machine_number }}
                · {{ $report->pic_name }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Data ini bisa dibuka dan diedit lagi selama 24 jam setelah disimpan.</p>
                </div>
                @if ($report->status_perbaikan === \App\Support\RepairStatus::CLOSE_BY_TECHNICIAN)
                    <a href="{{ route('home.submissions.op.edit', $report) }}" class="em-btn-primary justify-center">Edit</a>
                @endif
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div><span class="text-slate-500">Nama Mesin</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->machine_name_display }}</p></div>
                <div><span class="text-slate-500">Shift</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->work_shift_label }}</p></div>
                <div class="sm:col-span-2"><span class="text-slate-500">Waktu Perbaikan</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->repair_started_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} — {{ $report->repair_finished_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p></div>
            </div>

            @if ($report->operatorReport)
                <div class="mt-6 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-4 dark:border-cyan-500/30 dark:bg-cyan-500/10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700 dark:text-cyan-300">Tiket Input OP #{{ $report->operatorReport->id }}</p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $report->operatorReport->item_name }} · {{ $report->operatorReport->operator_name }}</p>
                    <p class="mt-2 text-sm whitespace-pre-wrap text-slate-600 dark:text-slate-300">{{ $report->operatorReport->problem }}</p>
                </div>
            @endif

            <div class="mt-6 space-y-5 border-t border-slate-200 pt-6 dark:border-white/10">
                <div>
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Masalah</h3>
                    <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-200">{{ $report->masalah }}</p>
                </div>
                <div>
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Analisa Penyebab Kerusakan</h3>
                    <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-200">{{ $report->analisa_penyebab_kerusakan }}</p>
                </div>
                <div>
                    <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Tindakan Perbaikan</h3>
                    <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-200">{{ $report->tindakan_perbaikan }}</p>
                </div>
                @if ($report->penggunaan_sparepart)
                    <div>
                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Penggunaan Sparepart</h3>
                        <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-200">{{ $report->penggunaan_sparepart }}</p>
                    </div>
                @endif
                @if ($report->workers_label !== '')
                    <div>
                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Yang Mengerjakan</h3>
                        <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-200">{{ $report->workers_label }}</p>
                    </div>
                @endif
                @if ($report->evidence_url)
                    <div>
                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Evidence</h3>
                        <a href="{{ $report->evidence_url }}" target="_blank" class="text-cyan-600 hover:underline dark:text-cyan-400">Lihat file evidence →</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
