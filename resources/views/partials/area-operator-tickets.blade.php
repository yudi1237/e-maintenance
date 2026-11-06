<div class="em-glass rounded-2xl overflow-hidden">
    <div class="px-5 py-4 sm:px-6 border-b border-slate-200 dark:border-white/10">
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">OP Area</h2>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Laporan di luar mesin. Terpisah dari tiket OP mesin.</p>
    </div>
    <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[920px] text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-white/10 dark:text-slate-400">
                    <th class="px-3 py-3 sm:px-4">Tiket</th>
                    <th class="px-3 py-3 sm:px-4">Area</th>
                    <th class="px-3 py-3 sm:px-4">Part</th>
                    <th class="px-3 py-3 sm:px-4">Equipment</th>
                    <th class="px-3 py-3 sm:px-4">Pelapor</th>
                    <th class="px-3 py-3 sm:px-4">Kejadian</th>
                    <th class="px-3 py-3 sm:px-4">Masalah</th>
                    <th class="px-3 py-3 sm:px-4">Status</th>
                    <th class="min-w-[8rem] px-3 py-3 sm:px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($areaReports as $ticket)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-3 py-3.5 font-semibold text-slate-900 sm:px-4 dark:text-white">#{{ $ticket->id }}</td>
                        <td class="px-3 py-3.5 text-slate-700 sm:px-4 dark:text-slate-200">{{ $ticket->area }}</td>
                        <td class="px-3 py-3.5 text-slate-700 sm:px-4 dark:text-slate-200">{{ $ticket->part_name ?: '—' }}</td>
                        <td class="px-3 py-3.5 text-slate-700 sm:px-4 dark:text-slate-200">{{ $ticket->equipment_name ?: '—' }}</td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">{{ $ticket->reporter_name }}</td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">
                            <span class="block">{{ $ticket->stop_date?->format('d/m/Y') }}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">{{ $ticket->stop_time_label }}</span>
                        </td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">
                            <span class="break-words">{{ $ticket->problem }}</span>
                            @if (filled($ticket->chronology))
                                <span class="mt-1 block text-xs text-slate-500">{{ $ticket->chronology }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 sm:px-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $ticket->status_badge_class }}">
                                {{ $ticket->status_label }}
                            </span>
                            @if ($ticket->isOnProgress())
                                <span class="mt-1 block break-words text-xs text-slate-500">({{ $ticket->pic_name }})</span>
                            @endif
                        </td>
                        <td class="min-w-[8rem] px-3 py-3.5 sm:px-4">
                            @if ($ticket->isOpen())
                                <button
                                    type="button"
                                    class="em-btn-primary w-full justify-center px-3 py-2 text-sm whitespace-nowrap sm:w-auto"
                                    data-pic-pin-open
                                    data-mode="claim"
                                    data-action="{{ route('area-op.claim', $ticket) }}"
                                >Ambil</button>
                            @elseif ($ticket->canAttemptCloseByTechnician(auth()->user()))
                                <select
                                    class="em-input !mt-0 !min-h-0 w-full min-w-0 !rounded-lg !px-2 !py-1.5 !text-xs"
                                    data-pic-pin-close
                                    data-action="{{ route('area-op.close', $ticket) }}"
                                    data-technician-id="{{ $ticket->claimed_technician_id }}"
                                    data-technician-name="{{ $ticket->pic_name }}"
                                >
                                    <option value="on_progress" selected>On Progress</option>
                                    <option value="close_by_technician">Close by teknisi</option>
                                </select>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-slate-500">Belum ada tiket OP Area.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($areaReports->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $areaReports->links() }}</div>
    @endif
</div>
