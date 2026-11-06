@props(['groups', 'items', 'readings' => [], 'remarks' => []])

<div class="space-y-5">
    @foreach ($groups as $group)
        @if (($group['layout'] ?? '') === 'voltage')
            <x-preventive-voltage-readings :group="$group" :readings="$readings" :remarks="$remarks" />
            @continue
        @endif
        @php $groupHasInput = (bool) ($group['has_input'] ?? false); @endphp
        <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
            <div class="px-4 py-3 border-b border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                <h3 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200">{{ $group['label'] }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-400">
                            <th class="px-3 py-2 w-12">No</th>
                            <th class="px-3 py-2 min-w-[14rem]">Item Pemeriksaan</th>
                            <th class="px-3 py-2 min-w-[12rem]">Standar</th>
                            @if ($groupHasInput)
                                <th class="px-3 py-2 min-w-[8rem]">Isi Manual</th>
                            @endif
                            <th class="px-3 py-2 text-right">Hasil</th>
                            <th class="px-3 py-2" style="min-width:12rem;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($group['items'] as $key => $item)
                            @php
                                $value = $items[$key] ?? null;
                                $valueLabel = $group['answers'][$value] ?? '-';
                                $hasInput = ! empty($item['input']);
                                $reading = $readings[$key] ?? null;
                                $remark = $remarks[$key] ?? null;
                            @endphp
                            <tr @class(['bg-yellow-100 dark:bg-yellow-500/15' => $hasInput])>
                                <td class="px-3 py-3 text-slate-500 tabular-nums">{{ $loop->iteration }}</td>
                                <td class="px-3 py-3 font-medium text-slate-800 dark:text-white">{{ $item['label'] }}</td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">{{ $item['standard'] !== '' ? $item['standard'] : '—' }}</td>
                                @if ($groupHasInput)
                                    <td class="px-3 py-3 font-medium text-slate-800 dark:text-white">
                                        @if ($hasInput)
                                            {{ $reading ?: '—' }}
                                            @if ($reading && ($item['input']['suffix'] ?? '') !== '')
                                                <span class="text-xs font-normal text-slate-500">{{ $item['input']['suffix'] }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-3 py-3 text-right">
                                    <span @class([
                                        'inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-green-100 text-green-800' => $value === 'ok',
                                        'bg-red-100 text-red-800' => $value === 'not_ok',
                                        'bg-slate-100 text-slate-700' => $value === 'na' || $value === null,
                                    ])>{{ $valueLabel }}</span>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ filled($remark) ? $remark : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
