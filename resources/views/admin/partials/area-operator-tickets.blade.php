<div class="em-glass rounded-2xl overflow-hidden">
    <div class="px-4 py-4 sm:px-6 border-b border-slate-200 dark:border-white/10">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Laporan OP Area</h2>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Laporan di luar mesin. Tidak masuk ke kartu mesin dan tidak mengubah grafik mesin.</p>
            </div>
            <div class="flex min-w-0 items-center gap-2">
                <code id="area-op-link" class="block max-w-[16rem] truncate rounded-lg bg-slate-100 px-2 py-1 text-xs text-slate-700 dark:bg-slate-800 dark:text-slate-200 sm:max-w-xs">{{ route('area-op.create') }}</code>
                <button type="button" id="copy-area-op-link" style="border:0;border-radius:0.6rem;background:#0891b2;color:#fff;font-size:12px;font-weight:700;padding:0.4rem 0.65rem;cursor:pointer;white-space:nowrap;">Salin link</button>
            </div>
        </div>
        <script>
            document.getElementById('copy-area-op-link')?.addEventListener('click', async () => {
                const link = document.getElementById('area-op-link')?.textContent?.trim() || '';
                const button = document.getElementById('copy-area-op-link');
                try {
                    await navigator.clipboard.writeText(link);
                } catch (error) {
                    const field = document.createElement('input');
                    field.value = link;
                    document.body.appendChild(field);
                    field.select();
                    document.execCommand('copy');
                    field.remove();
                }
                if (button) {
                    button.textContent = 'Tersalin';
                    setTimeout(() => { button.textContent = 'Salin link'; }, 1500);
                }
            });
        </script>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                    <th class="px-4 py-3">Kejadian</th>
                    <th class="px-4 py-3">Tiket</th>
                    <th class="px-4 py-3">Area</th>
                    <th class="px-4 py-3">Pelapor</th>
                    <th class="px-4 py-3">Part</th>
                    <th class="px-4 py-3">Equipment</th>
                    <th class="px-4 py-3">Masalah</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">PIC</th>
                    @if (auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($areaTickets as $ticket)
                    <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $ticket->stop_date_time_label }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap dark:text-slate-100">#{{ $ticket->id }}</td>
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $ticket->area }}</td>
                        <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">{{ $ticket->reporter_name }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-[160px] truncate dark:text-slate-300">{{ $ticket->part_name ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-[160px] truncate dark:text-slate-300">{{ $ticket->equipment_name ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-[220px] dark:text-slate-300">
                            <span class="block truncate" title="{{ $ticket->problem }}">{{ $ticket->problem }}</span>
                            @if (filled($ticket->chronology))
                                <span class="mt-1 block truncate text-xs text-slate-500" title="{{ $ticket->chronology }}">{{ $ticket->chronology }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $ticket->status_badge_class }}">
                                {{ $ticket->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $ticket->claimed_at ? $ticket->pic_name : '—' }}</td>
                        @if (auth()->user()->isAdmin())
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.area-operator-reports.destroy', $ticket) }}" class="inline" onsubmit="return confirm('Hapus tiket OP Area #{{ $ticket->id }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 10 : 9 }}" class="px-4 py-12 text-center text-slate-500">Tidak ada tiket OP Area.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($areaTickets->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $areaTickets->links() }}</div>
    @endif
</div>
