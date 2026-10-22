@props(['report', 'compact' => false])

@php
    $current = $report->status ?? \App\Support\PreventiveStatus::CLOSE_BY_TECHNICIAN;
    $user = auth()->user();
    $currentLabel = \App\Support\PreventiveStatus::labels()[$current] ?? $current;
    $next = \App\Support\PreventiveStatus::nextStatus($current);
    $canApprove = $next !== null && \App\Support\PreventiveStatus::canTransition($user, $current, $next);
    $statusColor = \App\Support\PreventiveStatus::legendColor($current);
@endphp

@once
    <style>
        .em-status-approve-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            border: 0;
            border-radius: 0.5rem;
            background: #22c55e;
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.28);
        }
        .em-status-approve-btn:hover { background: #16a34a; }
        .em-status-approve-btn svg { width: 12px; height: 12px; display: block; }
    </style>
@endonce

<div class="inline-flex {{ $compact ? 'flex-col items-start gap-1.5' : 'flex-wrap items-center gap-2' }}">
    <span class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold {{ $compact ? '' : 'text-sm px-3 py-2' }} {{ \App\Support\PreventiveStatus::badgeClassDark($current) }}" style="color: {{ $statusColor }};">
        {{ $currentLabel }}
    </span>

    @if ($canApprove)
        <form method="POST" action="{{ route('admin.preventive.update-status', $report) }}" class="inline-flex">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $next }}">
            <button
                type="submit"
                class="em-status-approve-btn {{ $compact ? 'px-2.5 py-1 text-[11px]' : 'px-3 py-1.5 text-xs' }}"
                data-preventive-approve-step="{{ $next }}"
                aria-label="Klik untuk approve {{ \App\Support\PreventiveStatus::labels()[$next] ?? $next }}"
            >
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Approve
            </button>
        </form>
    @endif
</div>
