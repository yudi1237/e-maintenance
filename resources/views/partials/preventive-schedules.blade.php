<div class="em-glass rounded-2xl overflow-hidden">
    <div class="px-5 py-4 sm:px-6 border-b border-slate-200 dark:border-white/10">
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Jadwal Preventive</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Jadwal dari admin. Ambil pekerjaan: buka form sesuai mesin dan tipe preventive.</p>
    </div>
    <div class="w-full overflow-x-auto">
        <table class="w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[8%]">
                <col class="w-[22%]">
                <col class="w-[14%]">
                <col class="w-[14%]">
                <col class="w-[16%]">
                <col class="w-[12%]">
                <col class="w-[14%]">
            </colgroup>
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-white/10 dark:text-slate-400">
                    <th class="px-3 py-3 sm:px-4">No</th>
                    <th class="px-3 py-3 sm:px-4">Mesin</th>
                    <th class="px-3 py-3 sm:px-4">Plan Tanggal PM</th>
                    <th class="px-3 py-3 sm:px-4">Tipe Preventive</th>
                    <th class="px-3 py-3 sm:px-4">Catatan</th>
                    <th class="px-3 py-3 sm:px-4">Status</th>
                    <th class="min-w-[8rem] px-3 py-3 sm:px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($schedules as $schedule)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="em-schedule-no px-3 py-3.5 font-semibold text-slate-900 sm:px-4 dark:text-white">{{ $schedules->firstItem() + $loop->index }}</td>
                        <td class="px-3 py-3.5 text-slate-700 sm:px-4 dark:text-slate-200">
                            <span class="block">Mesin {{ $schedule->machine_number }}</span>
                            <span class="mt-0.5 block break-words text-xs text-slate-500 dark:text-slate-400">{{ $schedule->machine_name_display }}</span>
                            @if (filled($schedule->machine_type))
                                <span class="mt-0.5 block break-words text-xs text-slate-500 dark:text-slate-400">{{ $schedule->machine_type }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">{{ $schedule->plan_date?->format('d/m/Y') }}</td>
                        <td class="px-3 py-3.5 text-slate-700 sm:px-4 dark:text-slate-200">{{ $schedule->preventive_type_label }}</td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">
                            <span class="break-words">{{ $schedule->notes ?: '—' }}</span>
                        </td>
                        <td class="px-3 py-3.5 sm:px-4">
                            <span class="em-status-badge {{ $schedule->technician_status_badge_class }}">
                                {{ $schedule->technician_status_label }}
                            </span>
                        </td>
                        <td class="min-w-[8rem] px-3 py-3.5 sm:px-4">
                            <a
                                href="{{ route('preventive.form', ['machineNumber' => $schedule->machine_number, 'category' => $schedule->preventive_type, 'from' => 'schedule']) }}"
                                class="em-btn-primary w-full justify-center px-3 py-2 text-sm whitespace-nowrap sm:w-auto"
                            >Isi</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                            Belum ada jadwal preventive dari admin.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($schedules->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $schedules->links() }}</div>
    @endif
</div>
