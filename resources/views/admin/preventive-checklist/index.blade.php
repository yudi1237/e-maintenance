<x-app-layout>
    <style>
        .em-checklist-compact { padding-top: 1rem; padding-bottom: 1rem; }
        .em-checklist-compact .em-shell { row-gap: 0.75rem; }
        .em-checklist-compact table.text-xs input,
        .em-checklist-compact table.text-xs select {
            min-height: 28px !important;
            height: 28px;
            margin-top: 0 !important;
            padding: 0 0.4rem !important;
            font-size: 12px !important;
            line-height: 1.2 !important;
            border-radius: 6px !important;
        }
        .em-checklist-compact .em-card table td,
        .em-checklist-compact .em-card table th {
            padding: 0.2rem 0.45rem;
        }
    </style>

    @php
        $denseInput = 'em-input !mt-0 !min-h-0 h-7 rounded-md px-1.5 py-0 text-xs';
        $denseText = '!mt-0 !min-h-0 h-7 w-full rounded-md px-1.5 py-0 text-xs shadow-none';
    @endphp

    <div
        class="em-checklist-compact"
        x-data="{ showForm: {{ old('create_item') ? 'true' : 'false' }}, showCreateCategory: {{ old('create_category') || $errors->has('label') ? 'true' : 'false' }}, showEditCategory: {{ old('edit_category') || $errors->has('edit_label') ? 'true' : 'false' }} }"
    >
        <div class="border-b border-slate-200 bg-white dark:border-white/5 dark:bg-slate-900/60">
            <div class="em-shell flex flex-wrap items-center justify-between gap-3 py-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Check Sheet</h2>
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button type="button" class="em-btn-secondary !min-h-0 !w-auto !px-3 !py-1.5 !text-xs" @click="showCreateCategory = ! showCreateCategory; if (showCreateCategory) { showEditCategory = false; showForm = false }">Tambah Kategori Preventive</button>
                    <button type="button" class="em-btn-secondary !min-h-0 !w-auto !px-3 !py-1.5 !text-xs" @click="showEditCategory = ! showEditCategory; if (showEditCategory) { showCreateCategory = false; showForm = false }">Edit Kategori Preventive</button>
                    @if ($category !== '')
                        <form method="POST" action="{{ route('admin.preventive-checklist.categories.destroy') }}" onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus kategori preventive '.($categories[$category] ?? $category).' beserta item check sheet-nya?') }})">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="code" value="{{ $category }}">
                            <button type="submit" class="em-btn-secondary !min-h-0 !w-auto !px-3 !py-1.5 !text-xs" style="color:#dc2626;border-color:#fca5a5">Delete Kategori</button>
                        </form>
                    @endif
                    @if ($category !== '')
                        <a href="{{ route('admin.preventive-checklist.export', ['category' => $category]) }}" class="em-btn-secondary !min-h-0 !w-auto !px-3 !py-1.5 !text-xs">Download Check Sheet</a>
                    @endif
                    <button type="button" class="em-btn-primary !min-h-0 !w-auto !px-3 !py-1.5 !text-xs" @click="showForm = ! showForm; if (showForm) { showCreateCategory = false; showEditCategory = false }">Tambah Item Check Sheet</button>
                </div>
            </div>
        </div>
        <div class="py-4">
        <div class="em-shell space-y-3">
            <x-alert />

            <div>
                <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kategori Preventive</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($categories as $value => $label)
                        <a href="{{ route('admin.preventive-checklist.index', ['category' => $value]) }}"
                           class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $category === $value ? 'border-cyan-500 bg-cyan-500/15 text-cyan-700 dark:text-cyan-200' : 'border-slate-200 text-slate-600 hover:border-cyan-400 dark:border-white/10 dark:text-slate-300' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="em-glass rounded-xl p-3" x-show="showCreateCategory" x-cloak>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Tambah Kategori Preventive</h3>
                <form method="POST" action="{{ route('admin.preventive-checklist.categories.store') }}" class="mt-2 flex flex-wrap items-end gap-2">
                    @csrf
                    <input type="hidden" name="create_category" value="1">
                    <div class="min-w-[16rem] flex-1">
                        <label for="new_category_label" class="text-xs font-medium text-slate-600 dark:text-slate-300">Nama kategori preventive</label>
                        <input id="new_category_label" name="label" value="{{ old('label') }}" required maxlength="150" class="{{ $denseInput }} mt-1 w-full">
                        @error('label')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="em-btn-primary !min-h-0 !w-auto !px-3 !py-1.5 !text-xs">Simpan</button>
                </form>
            </div>

            <div class="em-glass rounded-xl p-3" x-show="showEditCategory" x-cloak>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Edit Kategori Preventive</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Mengubah nama yang tampil. Item check sheet pada kategori ini tetap tersimpan.</p>
                <form method="POST" action="{{ route('admin.preventive-checklist.categories.update') }}" class="mt-2 flex flex-wrap items-end gap-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_category" value="1">
                    <input type="hidden" name="code" value="{{ $category }}">
                    <div class="min-w-[16rem] flex-1">
                        <label for="edit_category_label" class="text-xs font-medium text-slate-600 dark:text-slate-300">Nama kategori preventive</label>
                        <input id="edit_category_label" name="edit_label" value="{{ old('edit_label', $categories[$category] ?? $category) }}" required maxlength="150" class="{{ $denseInput }} mt-1 w-full">
                        @error('edit_label')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="em-btn-primary !min-h-0 !w-auto !px-3 !py-1.5 !text-xs">Simpan Nama</button>
                </form>
            </div>

            <div class="em-glass rounded-xl p-3" x-show="showForm" x-cloak>
                <div class="mb-2">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Tambah Item Check Sheet {{ $categories[$category] ?? $category }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Isi satu item per form. Nomor di sini selalu 1.</p>
                </div>
                <form method="POST" action="{{ route('admin.preventive-checklist.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="create_item" value="1">
                    <input type="hidden" name="category" value="{{ $category }}">

                    <div class="md:overflow-x-auto md:rounded-lg md:border md:border-slate-200 dark:md:border-white/10">
                        <table class="w-full min-w-0 border-separate border-spacing-0 text-xs md:min-w-[1280px]">
                            <thead class="hidden md:table-header-group">
                                <tr class="bg-sky-200 text-center text-[11px] font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                                    <x-admin.spreadsheet-th>No</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Kode grup</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Nama grup</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Tipe jawaban</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Layout (opsional)</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Nama item</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Standar</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Kode item (opsional)</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Ada isian angka</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Placeholder isian</x-admin.spreadsheet-th>
                                    <x-admin.spreadsheet-th>Satuan</x-admin.spreadsheet-th>
                                </tr>
                            </thead>
                            <tbody class="block space-y-3 md:table-row-group md:space-y-0">
                                <tr class="block rounded-lg border border-slate-200 bg-white p-2 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                                    <x-admin.spreadsheet-td label="No">
                                        <span class="inline-flex h-7 min-w-[2rem] items-center justify-center font-semibold text-slate-800 dark:text-slate-100">1</span>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Kode grup">
                                        <input id="group_key" name="group_key" type="text" class="{{ $denseInput }}" value="{{ old('group_key') }}" list="group-key-options" placeholder="electrical" required>
                                        <datalist id="group-key-options">
                                            @foreach ($groupKeys as $groupKey)
                                                <option value="{{ $groupKey }}"></option>
                                            @endforeach
                                        </datalist>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Nama grup">
                                        <input id="group_label" name="group_label" type="text" class="{{ $denseInput }}" value="{{ old('group_label') }}" placeholder="Electrical Section Check" required>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Tipe jawaban">
                                        <select id="item_type" name="item_type" class="{{ $denseInput }}">
                                            @foreach ($itemTypes as $type => $typeLabel)
                                                <option value="{{ $type }}" @selected(old('item_type', 'condition') === $type)>{{ $typeLabel }}</option>
                                            @endforeach
                                        </select>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Layout (opsional)">
                                        <input id="layout" name="layout" type="text" class="{{ $denseInput }}" value="{{ old('layout') }}" placeholder="voltage">
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Nama item">
                                        <input id="label" name="label" type="text" class="{{ $denseInput }}" value="{{ old('label') }}" required>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Standar">
                                        <input id="standard" name="standard" type="text" class="{{ $denseInput }}" value="{{ old('standard') }}">
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Kode item (opsional)">
                                        <input id="item_key" name="item_key" type="text" class="{{ $denseInput }}" value="{{ old('item_key') }}" placeholder="otomatis dari nama">
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Ada isian angka">
                                        <label class="inline-flex h-7 items-center gap-1.5 text-xs text-slate-700 dark:text-slate-200">
                                            <input type="checkbox" name="has_input" value="1" class="rounded border-gray-300 text-cyan-600" @checked(old('has_input'))>
                                            Ya
                                        </label>
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Placeholder isian">
                                        <input id="input_placeholder" name="input_placeholder" type="text" class="{{ $denseInput }}" value="{{ old('input_placeholder') }}">
                                    </x-admin.spreadsheet-td>
                                    <x-admin.spreadsheet-td label="Satuan">
                                        <input id="input_suffix" name="input_suffix" type="text" class="{{ $denseInput }}" value="{{ old('input_suffix') }}" placeholder="°C">
                                    </x-admin.spreadsheet-td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="em-btn-secondary !min-h-0 !px-3 !py-1.5 !text-xs w-full justify-center sm:w-auto" @click="showForm = false">Batal</button>
                        <button type="submit" class="em-btn-primary !min-h-0 !px-3 !py-1.5 !text-xs w-full justify-center sm:w-auto">Simpan Item</button>
                    </div>
                </form>
            </div>

            <div class="em-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs leading-tight">
                        <thead class="em-table-head">
                            <tr>
                                <th class="px-2 py-1.5 text-left">Grup</th>
                                <th class="px-2 py-1.5 text-left">Item</th>
                                <th class="px-2 py-1.5 text-left">Standar</th>
                                <th class="px-2 py-1.5 text-left">Tipe</th>
                                <th class="px-2 py-1.5 text-left">Status</th>
                                <th class="px-2 py-1.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($items as $item)
                                <tr>
                                    <td class="px-2 py-1 align-middle">
                                        <form id="item-{{ $item->id }}" method="POST" action="{{ route('admin.preventive-checklist.update', $item) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="category" value="{{ $item->category }}">
                                            <input type="hidden" name="item_key" value="{{ $item->item_key }}">
                                            <input type="hidden" name="layout" value="{{ $item->layout }}">
                                            <input type="hidden" name="has_input" value="{{ $item->has_input ? '1' : '0' }}">
                                            <input type="hidden" name="input_placeholder" value="{{ $item->input_placeholder }}">
                                            <input type="hidden" name="input_suffix" value="{{ $item->input_suffix }}">
                                        </form>
                                        <x-text-input form="item-{{ $item->id }}" name="group_label" class="{{ $denseText }} min-w-[8rem]" :value="$item->group_label" :title="$item->group_key.' · '.$item->item_key" required />
                                        <input form="item-{{ $item->id }}" type="hidden" name="group_key" value="{{ $item->group_key }}">
                                    </td>
                                    <td class="px-2 py-1 align-middle">
                                        <x-text-input form="item-{{ $item->id }}" name="label" class="{{ $denseText }} min-w-[10rem]" :value="$item->label" required />
                                    </td>
                                    <td class="px-2 py-1 align-middle">
                                        <x-text-input form="item-{{ $item->id }}" name="standard" class="{{ $denseText }} min-w-[8rem]" :value="$item->standard" />
                                    </td>
                                    <td class="px-2 py-1 align-middle">
                                        <select form="item-{{ $item->id }}" name="item_type" class="{{ $denseInput }} min-w-[8rem]">
                                            @foreach ($itemTypes as $type => $typeLabel)
                                                <option value="{{ $type }}" @selected($item->item_type === $type)>{{ $typeLabel }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-1 align-middle whitespace-nowrap">
                                        <label class="inline-flex items-center gap-1 text-[11px] text-slate-600 dark:text-slate-300">
                                            <input form="item-{{ $item->id }}" type="checkbox" name="is_active" value="1" class="h-3.5 w-3.5 rounded border-gray-300 text-cyan-600" @checked($item->is_active)>
                                            Aktif
                                        </label>
                                    </td>
                                    <td class="px-2 py-1 text-right whitespace-nowrap align-middle">
                                        <button form="item-{{ $item->id }}" type="submit" class="text-cyan-600 text-[11px] font-medium hover:underline">Simpan</button>
                                        <form method="POST" action="{{ route('admin.preventive-checklist.destroy', $item) }}" class="inline ms-2" onsubmit="return confirm('Hapus item ini dari check sheet?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 text-[11px] font-medium hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-6 text-center text-slate-500">Belum ada item. Tambah dari form di atas, atau jalankan seeder check sheet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</x-app-layout>
