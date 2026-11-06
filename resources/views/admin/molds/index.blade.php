<x-app-layout>
    @php
        $cellInput = 'em-input !mt-0 min-h-[40px] rounded-lg px-2 py-1.5 text-sm';
        $nextNo = ($molds->total() ?? 0) + 1;
    @endphp
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Master Data Mold</h2>
    </x-slot>

    <div class="py-8">
        <div class="em-shell space-y-6">
            <x-alert />

            <div class="em-glass rounded-2xl p-4 sm:p-6">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Input Master Data Mold</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Isi nama mold, customer, dan ukuran HxVxT.</p>
                    </div>
                    <a href="{{ route('admin.molds.export') }}" class="em-qr-btn w-fit shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Excel
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.molds.store') }}" class="space-y-4">
                    @csrf

                    <div class="md:overflow-x-auto md:rounded-xl md:border md:border-slate-200 dark:md:border-white/10">
                        <table class="w-full min-w-0 border-separate border-spacing-0 text-sm md:min-w-[820px]">
                            <thead class="hidden md:table-header-group">
                                <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                                    <x-admin.spreadsheet-th>No</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Nama Mold</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Customer</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>HxVxT (mm)</x-admin.spreadsheet-th>
                                </tr>
                            </thead>
                            <tbody class="block space-y-4 md:table-row-group md:space-y-0">
                                <tr class="block rounded-xl border border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                                    <x-admin.spreadsheet-td label="No">
                                        <span class="inline-flex min-h-[40px] min-w-[2.5rem] items-center justify-center font-semibold text-slate-800 dark:text-slate-100">{{ $nextNo }}</span>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Nama Mold">
                                        <input id="name" name="name" type="text" class="{{ $cellInput }}" value="{{ old('name') }}" placeholder="Nama mold" required>
                                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Customer">
                                        <input id="customer" name="customer" type="text" class="{{ $cellInput }}" value="{{ old('customer') }}" placeholder="Customer" required>
                                        <x-input-error :messages="$errors->get('customer')" class="mt-1" />
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="HxVxT (mm)">
                                        <input id="dimension" name="dimension" type="text" class="{{ $cellInput }}" value="{{ old('dimension') }}" placeholder="320 x 180 x 2.5" required>
                                        <x-input-error :messages="$errors->get('dimension')" class="mt-1" />
                                    </x-admin.spreadsheet-td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <a href="{{ route('admin.molds.export') }}" class="em-btn-secondary w-full justify-center sm:w-auto">Download Excel</a>
                        <button type="submit" class="em-btn-primary w-full justify-center sm:w-auto">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="em-glass rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-700 dark:text-slate-200">
                        <thead class="em-table-head">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Nama Mold</th>
                                <th class="px-4 py-3 text-left">Customer</th>
                                <th class="px-4 py-3 text-left">HxVxT (mm)</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($molds as $mold)
                                <tr>
                                    <td class="px-4 py-3 align-middle font-semibold">{{ $molds->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3 align-middle">
                                        <form id="mold-{{ $mold->id }}" method="POST" action="{{ route('admin.molds.update', $mold) }}">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <x-text-input form="mold-{{ $mold->id }}" name="name" class="!mt-0 w-full min-w-[10rem]" :value="$mold->name" required />
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <x-text-input form="mold-{{ $mold->id }}" name="customer" class="!mt-0 w-full min-w-[8rem]" :value="$mold->customer" required />
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <x-text-input form="mold-{{ $mold->id }}" name="dimension" class="!mt-0 w-full min-w-[8rem]" :value="$mold->dimension" required />
                                    </td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">
                                        <label class="inline-flex items-center gap-2 text-xs">
                                            <input form="mold-{{ $mold->id }}" type="hidden" name="is_active" value="0">
                                            <input form="mold-{{ $mold->id }}" type="checkbox" name="is_active" value="1" @checked($mold->is_active)>
                                            {{ $mold->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </label>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-right whitespace-nowrap">
                                        <button form="mold-{{ $mold->id }}" type="submit" class="text-cyan-600 text-xs font-semibold hover:underline">Simpan</button>
                                        <form method="POST" action="{{ route('admin.molds.destroy', $mold) }}" class="inline ms-3" onsubmit="return confirm('Hapus data ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 text-xs hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada master data mold.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($molds->hasPages())
                    <div class="p-4">{{ $molds->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
