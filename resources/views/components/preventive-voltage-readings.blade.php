@php
    $fields = $group['items'];
@endphp
<div class="overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border-collapse">
            <tbody>
                <tr class="bg-yellow-200 dark:bg-yellow-500/25">
                    <th class="px-3 py-3 text-left font-semibold text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-yellow-500/30 whitespace-nowrap">Tegangan Utama</th>
                    @foreach (['voltage_rs' => 'R-S', 'voltage_st' => 'S-T', 'voltage_rt' => 'R-T', 'voltage_pe_n' => 'PE-N'] as $key => $label)
                        <td class="px-3 py-3 border border-slate-300 dark:border-yellow-500/30 text-slate-800 dark:text-white">
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $label }} :</span>
                            {{ $readings[$key] ?? '—' }}
                            <span class="text-xs text-slate-500">VAC</span>
                        </td>
                    @endforeach
                </tr>
                <tr class="bg-yellow-200 dark:bg-yellow-500/25">
                    <th class="px-3 py-3 text-left font-semibold text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-yellow-500/30 whitespace-nowrap">Tegangan Power Supply</th>
                    <td class="px-3 py-3 border border-slate-300 dark:border-yellow-500/30 text-slate-800 dark:text-white" colspan="4">
                        {{ $readings['voltage_power_supply'] ?? '—' }}
                        <span class="text-xs text-slate-500">VDC</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
