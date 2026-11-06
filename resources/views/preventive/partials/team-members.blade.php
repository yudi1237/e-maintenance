@php
    $selectedTechnicians = array_map('strval', (array) old('technician_ids', $savedTeamIds ?? []));
@endphp
<div class="em-form-section overflow-hidden !p-0">
    <div class="px-4 py-3 border-b border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10">
        <h2 class="text-base font-semibold text-emerald-900 dark:text-emerald-200">Anggota Tim</h2>
        <p class="text-xs text-emerald-800/80 dark:text-emerald-200/80">Pilih anggota tim yang mengerjakan (jika ada)</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-400">
                    <th class="px-3 py-2 w-12">No</th>
                    <th class="px-3 py-2">Nama Teknisi</th>
                    <th class="px-3 py-2 text-center w-24">Pilih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($technicians as $technician)
                    <tr>
                        <td class="px-3 py-3 text-slate-500 tabular-nums">{{ $loop->iteration }}</td>
                        <td class="px-3 py-3 font-medium text-slate-800 dark:text-white">{{ $technician->optionLabel() }}</td>
                        <td class="px-3 py-3 text-center">
                            <input
                                type="checkbox"
                                name="technician_ids[]"
                                value="{{ $technician->id }}"
                                class="rounded border-slate-300 text-emerald-600"
                                @checked(in_array((string) $technician->id, $selectedTechnicians, true))
                            >
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-3 py-6 text-center text-sm text-amber-700">Belum ada PIC teknisi aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($errors->has('technician_ids'))
        <div class="px-4 py-3">
            <x-input-error :messages="$errors->get('technician_ids')" />
        </div>
    @endif
</div>
