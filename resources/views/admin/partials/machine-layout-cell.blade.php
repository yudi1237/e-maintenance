@php
    $number = (int) $number;
    $machine = $machinesByNumber[$number] ?? null;
    $stat = $machineStats[$number] ?? ['op' => 0, 'findings' => 0, 'pm_due' => null, 'alert' => 'idle'];
    $alert = $stat['alert'] ?? 'idle';
    $alertClass = match ($alert) {
        'active' => 'em-machine-card--active',
        'pending' => 'em-machine-card--pending',
        'approval' => 'em-machine-card--approval',
        default => 'em-machine-card--idle',
    };
    $isRunning = in_array($number, $runningMachines ?? [], true);
    $isBreakdown = in_array($number, $breakdownMachines ?? [], true);
    $isStandby = in_array($number, $standbyMachines ?? [], true);
    $markStatus = $isBreakdown ? 'breakdown' : ($isRunning ? 'running' : ($isStandby ? 'standby' : ''));
    $markClass = match ($markStatus) {
        'running' => 'is-running',
        'breakdown' => 'is-breakdown',
        'standby' => 'is-standby',
        default => '',
    };
    $markTitle = match ($markStatus) {
        'running' => 'Hijau (Running)',
        'breakdown' => 'Merah (Breakdown)',
        'standby' => 'Standby',
        default => 'Pilih Hijau (Running), Merah (Breakdown), atau Standby',
    };
    $label = $machine
        ? trim($machine->view_label.($machine->mold ? ' · '.$machine->mold->name : ''))
        : 'Mesin '.$number;
    $href = auth()->user()->isOperator()
        ? \App\Support\MachineQrCode::inputOpUrl($number)
        : route('admin.machines.show', $number);
@endphp
<div
    data-machine="{{ $number }}"
    data-alert="{{ $alert }}"
    @if (auth()->user()->canToggleMachineRunning()) data-href="{{ route('admin.machines.show', $number) }}" @endif
    title="{{ $label }}"
    class="em-layout-cell machine-card {{ $alertClass }} {{ auth()->user()->canToggleMachineRunning() ? 'machine-running-toggle' : '' }}"
>
    @if (auth()->user()->canToggleMachineRunning())
        <button
            type="button"
            class="machine-running-mark {{ $markClass }}"
            data-machine="{{ $number }}"
            data-status="{{ $markStatus }}"
            title="{{ $markTitle }}"
            aria-haspopup="menu"
            aria-pressed="{{ $markStatus !== '' ? 'true' : 'false' }}"
        ></button>
    @else
        <span class="machine-running-mark {{ $markClass !== '' ? $markClass : 'hidden' }}" data-machine="{{ $number }}" data-status="{{ $markStatus }}" title="{{ $markTitle }}"></span>
    @endif
    @if (auth()->user()->canToggleMachineRunning())
        <div class="em-layout-hit">
    @else
        <a href="{{ $href }}" class="em-layout-hit">
    @endif
        <img src="{{ asset('images/injection-machine.png') }}?v=yellow" alt="Mesin injection {{ $number }}" draggable="false">
    @if (auth()->user()->canToggleMachineRunning())
        </div>
    @else
        </a>
    @endif
    <span class="em-layout-no">{{ $number }}</span>
    @if (! empty($caption))
        <span class="em-layout-caption">{{ $caption }}</span>
    @endif
</div>
