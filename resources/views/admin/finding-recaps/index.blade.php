<x-app-layout>
    <div class="em-shell py-8 space-y-8" x-data="{ showForm: {{ ($errors->any() || old('create_recap')) ? 'true' : 'false' }}, machineName: @js(old('machine_name_id') ? ($machines->firstWhere('id', (int) old('machine_name_id'))?->view_label ?? '') : '') }">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Log Temuan</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Catat temuan mesin, due date, dan riwayat catatan setiap perubahan status.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.finding-recaps.export', array_filter(['status' => $statusFilter ?? ''])) }}" class="em-qr-btn w-fit">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Excel
                </a>
                <button type="button" class="em-btn-primary w-fit" @click="showForm = ! showForm">Input Log Temuan</button>
            </div>
        </div>

        <x-alert />

        @php
            $cellInput = 'em-input !mt-0 min-h-[40px] rounded-lg px-2 py-1.5 text-sm';
            $cellReadonly = $cellInput.' bg-slate-50 dark:bg-slate-950/40';
        @endphp
        <div class="em-glass rounded-2xl p-4 sm:p-6" x-show="showForm" x-cloak>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Form Log Temuan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tanggal terisi otomatis hari ini dan bisa diubah manual. Nama mesin mengikuti no mesin yang dipilih.</p>
            </div>

            <form method="POST" action="{{ route('admin.finding-recaps.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="create_recap" value="1">

                <div class="md:overflow-x-auto md:rounded-xl md:border md:border-slate-200 dark:md:border-white/10">
                    <table class="w-full min-w-0 border-separate border-spacing-0 text-sm md:min-w-[1080px]">
                        <thead class="hidden md:table-header-group">
                            <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                                <x-admin.spreadsheet-th>No</x-admin.spreadsheet-th>
                                <x-admin.spreadsheet-th>Tanggal</x-admin.spreadsheet-th>
                                <x-admin.spreadsheet-th>Due Date</x-admin.spreadsheet-th>
                                <x-admin.spreadsheet-th>No Mesin</x-admin.spreadsheet-th>
                                <x-admin.spreadsheet-th>Nama Mesin</x-admin.spreadsheet-th>
                                <x-admin.spreadsheet-th>Nama Equipment</x-admin.spreadsheet-th>
                                <x-admin.spreadsheet-th>Status</x-admin.spreadsheet-th>
                                <x-admin.spreadsheet-th>Catatan Status</x-admin.spreadsheet-th>
                            </tr>
                        </thead>
                        <tbody class="block space-y-4 md:table-row-group md:space-y-0">
                            <tr class="block rounded-xl border border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                                <x-admin.spreadsheet-td label="No">
                                    <span class="inline-flex min-h-[40px] min-w-[2.5rem] items-center justify-center font-semibold text-slate-800 dark:text-slate-100">1</span>
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Tanggal">
                                    <input id="found_date" name="found_date" type="date" class="{{ $cellInput }}" value="{{ old('found_date', $today) }}" required>
                                    @error('found_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Due Date">
                                    <input id="due_date" name="due_date" type="date" class="{{ $cellInput }}" value="{{ old('due_date') }}" required>
                                    @error('due_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="No Mesin">
                                    <select
                                        id="machine_name_id"
                                        name="machine_name_id"
                                        class="{{ $cellInput }}"
                                        required
                                        @change="machineName = $el.selectedOptions[0]?.dataset.name || ''"
                                    >
                                        <option value="">Pilih no mesin</option>
                                        @foreach ($machines as $machine)
                                            <option
                                                value="{{ $machine->id }}"
                                                data-name="{{ $machine->view_label }}"
                                                @selected((string) old('machine_name_id') === (string) $machine->id)
                                            >
                                                {{ $machine->machine_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('machine_name_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Nama Mesin">
                                    <input id="machine_name" type="text" class="{{ $cellReadonly }}" x-model="machineName" placeholder="Otomatis" readonly>
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Nama Equipment">
                                    <input id="equipment_name" name="equipment_name" type="text" class="{{ $cellInput }}" value="{{ old('equipment_name') }}" placeholder="Heater band, oil pump..." required maxlength="255">
                                    @error('equipment_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Status">
                                    <select id="status" name="status" class="{{ $cellInput }}" required>
                                        @foreach ($statusLabels as $value => $label)
                                            <option value="{{ $value }}" @selected(old('status', \App\Support\FindingRecapStatus::OPEN) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </x-admin.spreadsheet-td>
                                <x-admin.spreadsheet-td label="Catatan Status">
                                    <input id="note" name="note" type="text" class="{{ $cellInput }}" value="{{ old('note') }}" placeholder="Catatan untuk status yang dipilih..." required>
                                    @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </x-admin.spreadsheet-td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="em-btn-secondary w-full justify-center sm:w-auto" @click="showForm = false">Batal</button>
                    <button type="submit" class="em-btn-primary w-full justify-center sm:w-auto">Simpan Log Temuan</button>
                </div>
            </form>
        </div>

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-white/10">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar Log Temuan</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ubah status hanya setelah mengisi catatan. Semua catatan per status bisa dilihat.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.finding-recaps.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                        <div class="min-w-[12rem]">
                            <label for="status_filter" class="em-label">Filter Status</label>
                            <select id="status_filter" name="status" class="em-input !mt-0">
                                <option value="">Semua status</option>
                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(($statusFilter ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="em-btn-primary">Filter</button>
                            @if (! empty($statusFilter))
                                <a href="{{ route('admin.finding-recaps.index') }}" class="em-btn-secondary">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <x-admin.finding-recap-table :recaps="$recaps" :status-labels="$statusLabels" paginated show-actions />
        </div>
    </div>
</x-app-layout>
