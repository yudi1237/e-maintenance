<x-scan-layout>
    <x-slot name="title">Log Temuan Mesin {{ $machineNumber }} · e-Maintenance</x-slot>

    <div class="em-shell py-8">
        <a href="{{ route('scan.machines.show', [$machineNumber, $token]) }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali</a>

        <div class="mt-4 mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Log Temuan Mesin {{ $machineNumber }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $machine?->view_label ? $machine->view_label.' · ' : '' }}Hanya menampilkan temuan yang sudah Close.
            </p>
        </div>

        <div class="em-glass rounded-2xl overflow-hidden">
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
