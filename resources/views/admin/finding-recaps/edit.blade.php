<x-app-layout>
    <div class="em-shell py-8 space-y-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Log Temuan</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ubah due date, mesin, dan nama equipment. Status tetap diubah dari daftar.</p>
            </div>
            <a href="{{ route('admin.finding-recaps.index') }}" class="text-sm font-medium text-cyan-600 hover:text-cyan-500 dark:text-cyan-400">← Kembali ke daftar</a>
        </div>

        <x-alert />

        <div class="em-glass rounded-2xl p-6" x-data="{ machineName: @js(old('machine_name_id') ? ($machines->firstWhere('id', (int) old('machine_name_id'))?->view_label ?? $recap->machine_name_display) : $recap->machine_name_display) }">
            <form method="POST" action="{{ route('admin.finding-recaps.update', $recap) }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf
                @method('PUT')
                <div>
                    <label for="found_date" class="em-label">Tanggal</label>
                    <input id="found_date" type="text" class="em-input bg-slate-50 dark:bg-slate-950/40" value="{{ $recap->found_date->format('d/m/Y') }}" readonly>
                </div>
                <div>
                    <label for="due_date" class="em-label">Due Date</label>
                    <input id="due_date" name="due_date" type="date" class="em-input" value="{{ old('due_date', $recap->due_date->format('Y-m-d')) }}" required>
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="machine_name_id" class="em-label">No Mesin</label>
                    <select
                        id="machine_name_id"
                        name="machine_name_id"
                        class="em-input"
                        required
                        @change="machineName = $el.selectedOptions[0]?.dataset.name || ''"
                    >
                        <option value="">Pilih no mesin</option>
                        @foreach ($machines as $machine)
                            <option
                                value="{{ $machine->id }}"
                                data-name="{{ $machine->view_label }}"
                                @selected((string) old('machine_name_id', $recap->machine_name_id) === (string) $machine->id)
                            >
                                {{ $machine->machine_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('machine_name_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="machine_name" class="em-label">Nama Mesin</label>
                    <input id="machine_name" type="text" class="em-input bg-slate-50 dark:bg-slate-950/40" x-model="machineName" placeholder="Otomatis setelah pilih no mesin" readonly>
                </div>
                <div class="md:col-span-2">
                    <label for="equipment_name" class="em-label">Nama Equipment</label>
                    <input id="equipment_name" name="equipment_name" type="text" class="em-input" value="{{ old('equipment_name', $recap->equipment_name) }}" required maxlength="255">
                    @error('equipment_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2 flex justify-end gap-3">
                    <a href="{{ route('admin.finding-recaps.index') }}" class="em-btn-secondary">Batal</a>
                    <button type="submit" class="em-btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
