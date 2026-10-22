@props([
    'status' => null,
    'report' => null,
    'compact' => false,
    'variant' => 'op',
])

@php
    $steps = $variant === 'preventive'
        ? \App\Support\PreventiveStatus::approvalProgress($status)
        : \App\Support\RepairStatus::approvalProgress($status);
    $compactWidth = count($steps) === 2 ? 'w-[10.5rem] min-w-[10.5rem]' : 'w-[15rem] min-w-[15rem]';
@endphp

@once
    <style>
        .em-approval-table { border-collapse: collapse; text-align: center; overflow: hidden; border-radius: 6px; }
        .em-approval-table th,
        .em-approval-table td { border: 1px solid #cbd5e1; }
        .em-approval-table th { background: #d1fae5; color: #334155; font-weight: 700; text-transform: uppercase; }
        .em-approval-mark { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9999px; border: 0; padding: 0; line-height: 0; }
        .em-approval-mark svg { width: 16px; height: 16px; display: block; }
        .em-approve-done { background: #22c55e; color: #fff; }
        .em-approve-loading { background: #fff; color: #f59e0b; border: 2px solid #fbbf24; }
        .em-approve-spinner { animation: em-spin 0.8s linear infinite; }
        @keyframes em-spin { to { transform: rotate(360deg); } }
        html.dark .em-approval-table th { background: rgba(16, 185, 129, 0.25); color: #d1fae5; }
        html.dark .em-approval-table th,
        html.dark .em-approval-table td { border-color: #475569; }
        html.dark .em-approve-loading { background: #0f172a; }
    </style>
@endonce

<table {{ $attributes->class(['em-approval-table', $compact ? $compactWidth : 'w-full max-w-md']) }}>
    <thead>
        <tr>
            @foreach ($steps as $step)
                <th class="{{ $compact ? 'px-1 py-1 text-[9px] leading-tight' : 'px-3 py-2 text-xs' }}">
                    <span class="mx-auto flex flex-col items-center gap-0.5">
                        @if ($step['icon'] === 'injection')
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#f97316" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        @elseif ($step['icon'] === 'qc')
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#8b5cf6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        @else
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#16a34a" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @endif
                        <span>{{ $step['label'] }}</span>
                    </span>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr>
            @foreach ($steps as $step)
                @php
                    $title = $step['done']
                        ? $step['label'].' sudah approve'
                        : ($step['current'] ? 'Menunggu approve '.$step['label'] : 'Belum '.$step['label']);
                @endphp
                <td class="{{ $compact ? 'px-1 py-2' : 'px-3 py-3' }}" title="{{ $title }}">
                    @if ($step['done'])
                        <span class="em-approval-mark em-approve-done" aria-label="{{ $step['label'] }} sudah approve">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    @else
                        <span class="em-approval-mark em-approve-loading" aria-label="{{ $title }}">
                            <svg class="em-approve-spinner" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.25"/>
                                <path d="M21 12a9 9 0 00-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </span>
                    @endif
                </td>
            @endforeach
        </tr>
    </tbody>
</table>
