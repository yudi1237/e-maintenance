@php
    $fields = $group['items'];
@endphp
<div class="em-form-section overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border-collapse">
            <tbody>
                <tr class="bg-yellow-200 dark:bg-yellow-500/25">
                    <th class="px-3 py-3 text-left font-semibold text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-yellow-500/30 whitespace-nowrap">Tegangan Utama</th>
                    @foreach (['voltage_rs' => 'R-S', 'voltage_st' => 'S-T', 'voltage_rt' => 'R-T', 'voltage_pe_n' => 'PE-N'] as $key => $label)
                        <td class="px-2 py-2 border border-slate-300 dark:border-yellow-500/30">
                            <label class="flex items-center gap-1.5">
                                <span class="shrink-0 text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $label }} :</span>
                                <input
                                    type="text"
                                    name="readings[{{ $key }}]"
                                    value="{{ old('readings.'.$key) }}"
                                    placeholder="{{ $fields[$key]['input']['placeholder'] ?? '' }}"
                                    maxlength="50"
                                    required
                                    class="em-input-light w-20 py-1 text-sm bg-yellow-50 dark:bg-yellow-500/10"
                                >
                                <span class="shrink-0 text-xs font-medium text-slate-600">VAC</span>
                            </label>
                            @error('readings.'.$key)
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </td>
                    @endforeach
                </tr>
                <tr class="bg-yellow-200 dark:bg-yellow-500/25">
                    <th class="px-3 py-3 text-left font-semibold text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-yellow-500/30 whitespace-nowrap">Tegangan Power Supply</th>
                    <td class="px-2 py-2 border border-slate-300 dark:border-yellow-500/30" colspan="4">
                        <label class="flex max-w-xs items-center gap-1.5">
                            <input
                                type="text"
                                name="readings[voltage_power_supply]"
                                value="{{ old('readings.voltage_power_supply') }}"
                                placeholder="{{ $fields['voltage_power_supply']['input']['placeholder'] ?? '' }}"
                                maxlength="50"
                                required
                                class="em-input-light w-24 py-1 text-sm bg-yellow-50 dark:bg-yellow-500/10"
                            >
                            <span class="shrink-0 text-xs font-medium text-slate-600">VDC</span>
                        </label>
                        @error('readings.voltage_power_supply')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
