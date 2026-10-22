<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Master Data Mesin</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.machine-names.export') }}" class="em-qr-btn w-fit">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Excel
                </a>
                <a href="{{ route('admin.machines.qr.download-all') }}" class="em-qr-btn w-fit">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download semua QR
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showForm: {{ old('create_machine') ? 'true' : 'false' }} }">
        <div class="em-shell space-y-6">
            <x-alert />

            <div>
                <button type="button" class="em-btn-primary w-fit" @click="showForm = ! showForm">Tambah Mesin</button>
            </div>

            <div class="em-glass rounded-2xl p-4 sm:p-6" x-show="showForm" x-cloak>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Form Master Data Mesin</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Isi satu mesin per form. Nomor di sini selalu 1.</p>
                </div>
                <form method="POST" action="{{ route('admin.machine-names.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="create_machine" value="1">
                    @include('admin.machine-names.fields')
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="button" class="em-btn-secondary w-full justify-center sm:w-auto" @click="showForm = false">Batal</button>
                        <button type="submit" class="em-btn-primary w-full justify-center sm:w-auto">Simpan Mesin</button>
                    </div>
                </form>
            </div>


            <div class="em-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full text-sm">
                        <thead class="em-table-head">
                            <tr class="text-xs uppercase tracking-wider text-gray-500 dark:text-slate-400">
                                <th class="px-4 py-3 text-left whitespace-nowrap font-semibold">No Mesin</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap font-semibold">QR Code</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap font-semibold">Brand</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap font-semibold">Machine Model</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap font-semibold">Tonage</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap font-semibold">Tahun</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap font-semibold">Status</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                            @forelse ($machineNames as $machine)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-3 text-gray-900 whitespace-nowrap font-medium dark:text-white">{{ $machine->machine_number ?? '—' }}</td>
                                    <td class="px-4 py-3 align-top">
                                        @if ($machine->machine_number)
                                            <div class="flex flex-col items-start gap-2 min-w-[11rem]">
                                                <img src="{{ route('admin.machines.qr', $machine->machine_number) }}" alt="QR Mesin {{ $machine->machine_number }}" class="h-20 w-20 rounded-lg bg-white p-0.5 ring-1 ring-slate-200 dark:ring-white/10">
                                                <div class="text-[11px] leading-4 text-slate-600 dark:text-slate-300 space-y-0.5">
                                                    @foreach ($machine->qrCaptionLines() as $line)
                                                        <p>{{ $line }}</p>
                                                    @endforeach
                                                </div>
                                                <a href="{{ route('admin.machines.qr.download', $machine->machine_number) }}" class="em-qr-btn">
                                                    Download
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-800 whitespace-nowrap dark:text-slate-200">{{ $machine->brand ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-800 whitespace-nowrap dark:text-slate-200">{{ $machine->machine_model ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-800 whitespace-nowrap dark:text-slate-200">{{ $machine->tonnage ? $machine->tonnage.'T' : '—' }}</td>
                                    <td class="px-4 py-3 text-gray-800 whitespace-nowrap dark:text-slate-200">{{ $machine->manufacture_year ?? '—' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $machine->is_active ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                            {{ $machine->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-3">
                                            @if ($machine->machine_number)
                                                <a href="{{ route('admin.machines.show', $machine->machine_number) }}" class="text-cyan-600 hover:underline dark:text-cyan-400 text-xs font-medium">Halaman</a>
                                            @endif
                                            <a href="{{ route('admin.machine-names.edit', $machine) }}" class="text-cyan-600 hover:underline dark:text-cyan-400 text-xs font-medium">Edit</a>
                                            <form method="POST" action="{{ route('admin.machine-names.update', $machine) }}" class="inline-flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="toggle_active" value="1">
                                                <label class="inline-flex items-center gap-2 text-xs text-gray-700 dark:text-slate-300">
                                                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-900" @checked($machine->is_active) onchange="this.form.submit()">
                                                    Aktif
                                                </label>
                                            </form>
                                            <form method="POST" action="{{ route('admin.machine-names.destroy', $machine) }}" class="inline" onsubmit="return confirm('Hapus nama mesin ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 text-xs font-medium hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500">
                                        Belum ada data mesin. Silakan tambah dari form di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($machineNames->hasPages())
                    <div class="p-4">{{ $machineNames->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
