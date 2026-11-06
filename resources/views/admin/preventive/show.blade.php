<x-app-layout>
    <div class="em-shell py-8">
        @if (auth()->user()?->seesFullDashboard())
            <a href="{{ route('admin.preventive.index') }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke Preventive Mesin</a>
        @else
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke Dashboard</a>
        @endif

        <x-alert />

        <div class="mt-4 em-glass rounded-2xl p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Detail Preventive</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $report->work_date->format('d/m/Y') }} · Mesin {{ $report->machine_number }} · {{ $report->category_label }} · {{ $report->pic_name }}</p>
                    <div class="mt-4 flex flex-wrap items-start gap-4">
                        <x-admin.preventive-status-form :report="$report" />
                        <x-admin.approval-progress :status="$report->status" variant="preventive" class="ml-auto" />
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Hasil</p>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $report->result_badge_class }}">
                        {{ $report->result_label }}
                    </span>
                    <div class="mt-3">
                        <a href="{{ route('admin.preventive.export', $report) }}" class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20">Download PDF</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-8">
                <div><span class="text-slate-500">Nama Mesin</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->machine_name_display }}</p></div>
                <div><span class="text-slate-500">Kategori Preventive</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->schedule_group_label }}</p></div>
                <div><span class="text-slate-500">Tipe Preventive</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->category_label }}</p></div>
                @if (\App\Support\PreventiveCategory::isUtility($report->category))
                    <div><span class="text-slate-500">{{ \App\Support\PreventiveCategory::numberLabel($report->category) }}</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->equipment_label }}</p></div>
                @endif
                <div><span class="text-slate-500">Anggota Tim</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->team_members_label }}</p></div>
                <div><span class="text-slate-500">Shift</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->work_shift_label }}</p></div>
                <div><span class="text-slate-500">PIC</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->pic_name }}</p></div>
                <div><span class="text-slate-500">Tanggal</span><p class="mt-0.5 text-slate-900 dark:text-white">{{ $report->work_date->format('d/m/Y') }}</p></div>
            </div>

            <x-preventive.evidence-gallery :urls="$report->evidence_urls" />

            <div class="border-t border-slate-200 pt-6 dark:border-white/10">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-4">Checklist</h3>
                <x-preventive-checklist-result :groups="$report->checklistGroups()" :items="$report->items ?? []" :readings="$report->readings ?? []" :remarks="$report->item_remarks ?? []" />
            </div>

            @if (filled($report->penggunaan_sparepart))
                <div class="mt-6 border-t border-slate-200 pt-6 dark:border-white/10">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">Pemakaian Sparepart</h3>
                    <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->penggunaan_sparepart }}</p>
                </div>
            @endif

            @if ($report->notes)
                <div class="mt-6 border-t border-slate-200 pt-6 dark:border-white/10">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">Catatan</h3>
                    <p class="text-slate-700 whitespace-pre-wrap dark:text-slate-200">{{ $report->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
