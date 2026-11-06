@props(['report', 'statusLabels' => [], 'compact' => false])

@php
    $current = $report->status_perbaikan ?? \App\Support\RepairStatus::OPEN;
    $user = auth()->user();
    $currentLabel = \App\Support\RepairStatus::labels()[$current] ?? $current;
    $next = \App\Support\RepairStatus::nextStatus($current);
    $canApprove = $next !== null && \App\Support\RepairStatus::canTransition($user, $current, $next);
    $isCloseStep = $next === \App\Support\RepairStatus::CLOSE;
    $statusColor = \App\Support\RepairStatus::legendColor($current);
    $approveLabel = $next === \App\Support\RepairStatus::ON_PROGRESS
        ? (\App\Support\RepairStatus::labels()[$next] ?? 'On Progress')
        : 'Approve';
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
    <span class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold {{ $compact ? '' : 'text-sm px-3 py-2' }} {{ \App\Support\RepairStatus::badgeClassDark($current) }}" style="color: {{ $statusColor }};">
        {{ $currentLabel }}
    </span>

    @if ($canApprove)
        <form method="POST" action="{{ route('admin.reports.update-status', $report) }}" class="inline-flex">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status_perbaikan" value="{{ $next }}">
            <input type="hidden" name="tindakan_perbaikan" value="{{ $report->tindakan_perbaikan }}" data-tindakan-perbaikan-input>
            <input type="hidden" name="damage_cause_category" value="{{ $report->damage_cause_category }}" data-damage-cause-input>
            <input type="hidden" name="line_stop_mesin" value="{{ $report->line_stop_mesin === null ? '' : ($report->line_stop_mesin ? 'yes' : 'no') }}" data-line-stop-input>
            <input type="hidden" name="sub_damage_category_id" value="{{ $report->sub_damage_category_id }}" data-sub-damage-input>
            <button
                type="{{ $isCloseStep ? 'button' : 'submit' }}"
                class="em-status-approve-btn {{ $compact ? 'px-2.5 py-1 text-[11px]' : 'px-3 py-1.5 text-xs' }}"
                data-approve-step="{{ $next }}"
                @if ($isCloseStep)
                    data-approve-close
                    data-report-machine="{{ $report->machine_number }}"
                @endif
                aria-label="Klik untuk {{ strtolower($approveLabel) }} {{ \App\Support\RepairStatus::labels()[$next] ?? $next }}"
            >
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $approveLabel }}
            </button>
        </form>
    @endif
</div>

<x-admin.close-status-modal />
