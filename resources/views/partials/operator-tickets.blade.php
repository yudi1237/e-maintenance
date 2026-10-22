<div class="em-glass rounded-2xl overflow-hidden">
    <div class="px-5 py-4 sm:px-6 border-b border-slate-200 dark:border-white/10">
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Tiket Input OP</h2>
    </div>
    <div class="w-full overflow-x-auto">
        <table class="w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[7%]">
                <col class="w-[15%]">
                <col class="w-[13%]">
                <col class="w-[13%]">
                <col class="w-[13%]">
                <col class="w-[15%]">
                <col class="w-[11%]">
                <col class="w-[14%]">
            </colgroup>
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-white/10 dark:text-slate-400">
                    <th class="px-3 py-3 sm:px-4">Tiket</th>
                    <th class="px-3 py-3 sm:px-4">Mesin</th>
                    <th class="px-3 py-3 sm:px-4">Barang</th>
                    <th class="px-3 py-3 sm:px-4">Operator</th>
                    <th class="px-3 py-3 sm:px-4">Stop</th>
                    <th class="px-3 py-3 sm:px-4">Masalah</th>
                    <th class="px-3 py-3 sm:px-4">Status</th>
                    <th class="min-w-[8rem] px-3 py-3 sm:px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($operatorReports as $ticket)
                    @php
                        $canAttemptClose = $ticket->canAttemptCloseByTechnician(auth()->user());
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-3 py-3.5 font-semibold text-slate-900 sm:px-4 dark:text-white">#{{ $ticket->id }}</td>
                        <td class="px-3 py-3.5 text-slate-700 sm:px-4 dark:text-slate-200">
                            <span class="block">Mesin {{ $ticket->machine_number }}</span>
                            <span class="mt-0.5 block break-words text-xs text-slate-500">{{ $ticket->machine_name_display }}</span>
                        </td>
                        <td class="px-3 py-3.5 text-slate-700 sm:px-4 dark:text-slate-200">
                            <span class="break-words">{{ $ticket->item_name }}</span>
                        </td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">
                            <span class="block break-words">{{ $ticket->operator_name }}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">NPK {{ $ticket->npk }}</span>
                        </td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">
                            <span class="block">{{ $ticket->stop_date?->format('d/m/Y') }}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">{{ $ticket->stop_time_label }}</span>
                        </td>
                        <td class="px-3 py-3.5 text-slate-600 sm:px-4 dark:text-slate-300">
                            <span class="break-words">{{ $ticket->problem }}</span>
                        </td>
                        <td class="px-3 py-3.5 sm:px-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $ticket->status_badge_class }}">
                                {{ $ticket->status_label }}
                            </span>
                            @if ($ticket->isOnProgress() && $ticket->pic_name !== 'Belum diambil')
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
                                    data-action="{{ route('reports.tickets.claim', $ticket) }}"
                                >Ambil</button>
                            @elseif ($canAttemptClose)
                                <select
                                    class="em-input !mt-0 !min-h-0 w-full min-w-0 !rounded-lg !px-2 !py-1.5 !text-xs"
                                    data-pic-pin-close
                                    data-action="{{ route('reports.tickets.close', $ticket) }}"
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
                        <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                            Belum ada tiket Input OP.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($operatorReports->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $operatorReports->links() }}</div>
    @endif
</div>
