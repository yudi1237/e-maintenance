<x-scan-layout>
    <x-slot name="title">Input OP Mesin {{ $machineNumber }} · e-Maintenance</x-slot>

    <div class="em-shell py-8">
        <a href="{{ route('scan.machines.show', [$machineNumber, $token]) }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali</a>

        <div class="mt-4 mb-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-300">Input OP</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Laporan Stop Mesin</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Mesin No mengikuti barcode yang discan. Tidak perlu diisi manual.</p>
        </div>

        <x-alert-light />

        <form method="POST" action="{{ route('scan.operator-reports.store', [$machineNumber, $token]) }}" class="em-glass rounded-2xl p-5 sm:p-6 space-y-5" accept-charset="UTF-8" autocomplete="off">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4 items-start">
                <div class="min-w-0">
                    <label for="operator_name" class="em-label min-h-[2.5rem] flex items-end">Nama (Wajib Diisi)</label>
                    <input id="operator_name" name="operator_name" type="text" inputmode="text" maxlength="150" class="em-input" value="{{ old('operator_name') }}" required autocomplete="name">
                    <x-input-error :messages="$errors->get('operator_name')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="npk" class="em-label min-h-[2.5rem] flex items-end">NPK (Wajib Diisi)</label>
                    <input id="npk" name="npk" type="text" inputmode="numeric" maxlength="20" pattern="[A-Za-z0-9]+" class="em-input" value="{{ old('npk') }}" required autocomplete="off">
                    <x-input-error :messages="$errors->get('npk')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="item_name" class="em-label min-h-[2.5rem] flex items-end">Nama Barang (Wajib Diisi)</label>
                    <input id="item_name" name="item_name" type="text" inputmode="text" maxlength="200" class="em-input" value="{{ old('item_name') }}" required>
                    <x-input-error :messages="$errors->get('item_name')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label class="em-label min-h-[2.5rem] flex items-end">Mesin No</label>
                    <div class="em-input flex items-center bg-slate-50 font-medium text-slate-800 dark:bg-slate-900/80 dark:text-white">
                        Mesin {{ $machineNumber }}{{ $scannedMachine?->view_label ? ' — '.$scannedMachine->view_label : '' }}
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Otomatis dari barcode yang discan</p>
                </div>
                <div class="min-w-0">
                    <label for="stop_date" class="em-label min-h-[2.5rem] flex items-end">Stop Tanggal (Wajib Diisi)</label>
                    <input id="stop_date" name="stop_date" type="date" class="em-input" value="{{ old('stop_date', $defaultDate) }}" required>
                    <x-input-error :messages="$errors->get('stop_date')" class="mt-1" />
                </div>
                <div class="min-w-0">
                    <label for="stop_time" class="em-label min-h-[2.5rem] flex items-end">Jam (Wajib Diisi)</label>
                    <input id="stop_time" name="stop_time" type="time" class="em-input" value="{{ old('stop_time', $defaultTime) }}" required>
                    <x-input-error :messages="$errors->get('stop_time')" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="problem" class="em-label">Masalah (Wajib Diisi)</label>
                <textarea id="problem" name="problem" rows="3" maxlength="2000" class="em-input min-h-[6.5rem]" required>{{ old('problem') }}</textarea>
                <x-input-error :messages="$errors->get('problem')" class="mt-1" />
            </div>

            <div>
                <label for="chronology" class="em-label">Kronologi (Opsional)</label>
                <textarea id="chronology" name="chronology" rows="3" maxlength="5000" class="em-input min-h-[6.5rem]">{{ old('chronology') }}</textarea>
                <x-input-error :messages="$errors->get('chronology')" class="mt-1" />
            </div>

            <button type="submit" class="em-btn-primary w-full justify-center">Kirim Input OP</button>
        </form>
    </div>
</x-scan-layout>
