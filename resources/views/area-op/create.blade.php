<x-scan-layout>
    <x-slot name="title">Input OP Area · e-Maintenance</x-slot>

    <div class="em-shell py-8">
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-300">Input OP Area</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Laporan di luar mesin</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Untuk area di luar mesin injection. Tiket ini terpisah dari OP mesin.</p>
        </div>

        <x-alert-light />

        <form method="POST" action="{{ route('area-op.store') }}" class="em-glass rounded-2xl p-5 sm:p-6 space-y-5" accept-charset="UTF-8" autocomplete="off">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4 items-start">
                <div class="min-w-0 sm:col-span-2">
                    <label for="area" class="em-label min-h-[2.5rem] flex items-end">Pilih Area (Wajib Diisi)</label>
                    <select id="area" name="area" class="em-input" required>
                        <option value="">Pilih area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area }}" @selected(old('area') === $area)>{{ $area }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('area')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="reporter_name" class="em-label min-h-[2.5rem] flex items-end">Nama Pelapor (Wajib Diisi)</label>
                    <input id="reporter_name" name="reporter_name" type="text" maxlength="150" class="em-input" value="{{ old('reporter_name') }}" required autocomplete="name">
                    <x-input-error :messages="$errors->get('reporter_name')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="part_name" class="em-label min-h-[2.5rem] flex items-end">Nama Part</label>
                    <input id="part_name" name="part_name" type="text" maxlength="200" class="em-input" value="{{ old('part_name') }}">
                    <x-input-error :messages="$errors->get('part_name')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="equipment_name" class="em-label min-h-[2.5rem] flex items-end">Nama Equipment</label>
                    <input id="equipment_name" name="equipment_name" type="text" maxlength="200" class="em-input" value="{{ old('equipment_name') }}">
                    <x-input-error :messages="$errors->get('equipment_name')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="stop_date" class="em-label min-h-[2.5rem] flex items-end">Stop Tanggal (Wajib Diisi)</label>
                    <input id="stop_date" name="stop_date" type="date" class="em-input" value="{{ $defaultDate }}" required>
                    <x-input-error :messages="$errors->get('stop_date')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="stop_time" class="em-label min-h-[2.5rem] flex items-end">Jam Kejadian (Wajib Diisi)</label>
                    <input id="stop_time" name="stop_time" type="time" class="em-input" value="{{ $defaultTime }}" required>
                    <x-input-error :messages="$errors->get('stop_time')" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="problem" class="em-label">Masalah (Tulis Detail, Wajib Diisi)</label>
                <textarea id="problem" name="problem" rows="3" maxlength="2000" class="em-input min-h-[6.5rem]" required>{{ old('problem') }}</textarea>
                <x-input-error :messages="$errors->get('problem')" class="mt-1" />
            </div>

            <div>
                <label for="chronology" class="em-label">Kronologi</label>
                <textarea id="chronology" name="chronology" rows="3" maxlength="5000" class="em-input min-h-[6.5rem]">{{ old('chronology') }}</textarea>
                <x-input-error :messages="$errors->get('chronology')" class="mt-1" />
            </div>

            <button type="submit" class="em-btn-primary w-full justify-center">Kirim Input OP Area</button>
        </form>
    </div>
</x-scan-layout>
