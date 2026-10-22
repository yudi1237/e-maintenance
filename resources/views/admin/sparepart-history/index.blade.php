<x-app-layout>
    <div class="em-shell py-8 space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">History Spare Part</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Penggunaan spare part dari tiket OP yang sudah Close dan lulus semua approval, urut tanggal terbaru.</p>
            </div>
            <a href="{{ route('admin.sparepart-history.export', $filters) }}" class="em-qr-btn w-fit">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Excel
            </a>
        </div>

        <x-alert />

        @error('export')
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200">{{ $message }}</div>
        @enderror

        <form method="GET" action="{{ route('admin.sparepart-history.index') }}" class="em-glass rounded-2xl p-4 sm:p-5">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div>
                    <label for="start_date" class="em-label">Tanggal Mulai</label>
                    <input id="start_date" name="start_date" type="date" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ request('start_date') }}">
                </div>
                <div>
                    <label for="end_date" class="em-label">Tanggal Akhir</label>
                    <input id="end_date" name="end_date" type="date" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ request('end_date') }}">
                </div>
                <div>
                    <label for="machine_number" class="em-label">Mesin No</label>
                    <select id="machine_number" name="machine_number" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm">
                        <option value="">Semua</option>
                        @foreach ($machineNumbers as $num)
                            <option value="{{ $num }}" @selected((string) request('machine_number') === (string) $num)>{{ $num }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="sparepart" class="em-label">Sparepart</label>
                    <input id="sparepart" name="sparepart" type="text" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ request('sparepart') }}" placeholder="Cari nama" maxlength="100">
                </div>
                <div>
                    <label for="pic" class="em-label">PIC Teknisi</label>
                    <input id="pic" name="pic" type="text" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ request('pic') }}" placeholder="Cari PIC" maxlength="100">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="em-btn-primary min-h-[40px] flex-1 justify-center">Filter</button>
                    <a href="{{ route('admin.sparepart-history.index') }}" class="em-btn-secondary min-h-[40px] justify-center px-3">Reset</a>
                </div>
            </div>
        </form>

        <div class="em-glass overflow-hidden rounded-2xl">
            <style>
                .em-sparepart-sheet { border-collapse: collapse; width: 100%; }
                .em-sparepart-sheet th,
                .em-sparepart-sheet td {
                    border-left: 1px solid #94a3b8;
                    border-right: 1px solid #94a3b8;
                }
                .dark .em-sparepart-sheet th { border-left-color: #38bdf8; border-right-color: #38bdf8; }
                .dark .em-sparepart-sheet td { border-left-color: #64748b; border-right-color: #64748b; }
            </style>
            <div class="overflow-x-auto">
                <table class="em-sparepart-sheet min-w-full text-sm">
                    <thead>
                        <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                            <x-admin.spreadsheet-th>No</x-admin.spreadsheet-th>
                            <x-admin.spreadsheet-th>Tanggal</x-admin.spreadsheet-th>
                            <x-admin.spreadsheet-th>Sparepart</x-admin.spreadsheet-th>
                            <x-admin.spreadsheet-th>Qty</x-admin.spreadsheet-th>
                            <x-admin.spreadsheet-th>Mesin No</x-admin.spreadsheet-th>
                            <x-admin.spreadsheet-th>PIC Teknisi</x-admin.spreadsheet-th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $index => $row)
                            <tr class="bg-white hover:bg-slate-50 dark:bg-slate-900/40 dark:hover:bg-white/5">
                                <x-admin.spreadsheet-td label="No">
                                    <span class="inline-flex min-h-[36px] items-center justify-center font-semibold tabular-nums text-slate-800 dark:text-slate-100">{{ $rows->firstItem() + $index }}</span>
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Tanggal">
                                    <span class="inline-flex min-h-[36px] items-center text-slate-700 dark:text-slate-200">{{ $row['tanggal'] }}</span>
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Sparepart">
                                    <span class="inline-flex min-h-[36px] items-center font-medium text-slate-800 dark:text-white">{{ $row['sparepart'] }}</span>
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Qty">
                                    <span class="inline-flex min-h-[36px] items-center tabular-nums text-slate-700 dark:text-slate-200">{{ $row['qty'] }}</span>
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Mesin No">
                                    <span class="inline-flex min-h-[36px] items-center font-medium text-slate-800 dark:text-slate-100">{{ $row['machine_number'] }}</span>
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="PIC Teknisi">
                                    <span class="inline-flex min-h-[36px] items-center text-slate-700 dark:text-slate-200">{{ $row['pic_name'] }}</span>
                                </x-admin.spreadsheet-td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada penggunaan spare part dari tiket OP.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows->hasPages())
                <div class="border-t border-slate-200 p-4 dark:border-white/10">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
