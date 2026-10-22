<x-app-layout>
    @php
        $cellInput = 'em-input !mt-0 min-h-[40px] rounded-lg px-2 py-1.5 text-sm';
    @endphp
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Master Data Equipment</h2>
    </x-slot>

    <div class="py-8">
        <div class="em-shell space-y-6">
            <x-alert />

            <div class="flex flex-wrap gap-2">
                @foreach ($categories as $value => $label)
                    <a href="{{ route('admin.robots.index', ['category' => $value]) }}"
                       class="rounded-full border px-3 py-1.5 text-xs font-semibold {{ $category === $value ? 'border-cyan-500 bg-cyan-500/15 text-cyan-700 dark:text-cyan-200' : 'border-slate-200 text-slate-600 hover:border-cyan-400 dark:border-white/10 dark:text-slate-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="em-glass rounded-2xl p-4 sm:p-6">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $formTitle }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Isi nomor dan type. Nomor dipakai sebagai pilihan saat check sheet preventive.</p>
                    </div>
                    <a href="{{ route('admin.robots.export', ['category' => $category]) }}" class="em-qr-btn w-fit shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Excel
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.robots.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="category" value="{{ $category }}">

                    <div class="md:overflow-x-auto md:rounded-xl md:border md:border-slate-200 dark:md:border-white/10">
                        <table class="w-full min-w-0 border-separate border-spacing-0 text-sm md:min-w-[640px]">
                            <thead class="hidden md:table-header-group">
                                <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                                    <x-admin.spreadsheet-th>No</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Nomor</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Type</x-admin.spreadsheet-th>
                                </tr>
                            </thead>
                            <tbody class="block space-y-4 md:table-row-group md:space-y-0">
                                <tr class="block rounded-xl border border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                                    <x-admin.spreadsheet-td label="No">
                                        <span class="inline-flex min-h-[40px] min-w-[2.5rem] items-center justify-center font-semibold text-slate-800 dark:text-slate-100">1</span>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Nomor">
                                        <input id="number" name="number" type="text" class="{{ $cellInput }}" value="{{ old('number', old('robot_number')) }}" placeholder="Contoh: {{ $category === 'robot' ? 'R01' : '01' }}" required>
                                        @error('number')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @error('robot_number')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Type">
                                        <input id="type" name="type" type="text" class="{{ $cellInput }}" value="{{ old('type', old('robot_type')) }}" placeholder="Type equipment" required>
                                        @error('type')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @error('robot_type')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </x-admin.spreadsheet-td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <a href="{{ route('admin.robots.export', ['category' => $category]) }}" class="em-btn-secondary w-full justify-center sm:w-auto">Download Excel</a>
                        <button type="submit" class="em-btn-primary w-full justify-center sm:w-auto">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="em-glass rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-700 dark:text-slate-200">
                        <thead class="em-table-head">
                            <tr>
                                <th class="px-4 py-3 text-left">Nomor</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <form id="equipment-{{ $item->id }}" method="POST" action="{{ $item->update_url }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="category" value="{{ $category }}">
                                        </form>
                                        <x-text-input form="equipment-{{ $item->id }}" name="number" class="!mt-0 w-full min-w-[7rem]" :value="$item->number" required />
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-text-input form="equipment-{{ $item->id }}" name="type" class="!mt-0 w-full min-w-[10rem]" :value="$item->type" required />
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <label class="inline-flex items-center gap-2 text-xs">
                                            <input form="equipment-{{ $item->id }}" type="hidden" name="is_active" value="0">
                                            <input form="equipment-{{ $item->id }}" type="checkbox" name="is_active" value="1" @checked($item->is_active)>
                                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </label>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <button form="equipment-{{ $item->id }}" type="submit" class="text-cyan-600 text-xs font-semibold hover:underline">Simpan</button>
                                        <form method="POST" action="{{ $item->destroy_url }}" class="inline ms-3" onsubmit="return confirm('Hapus data ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 text-xs hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada master data. Isi nomor dan type di form atas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($items->hasPages())
                    <div class="p-4">{{ $items->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
