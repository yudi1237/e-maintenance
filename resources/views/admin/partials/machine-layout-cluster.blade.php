@php
    $layoutNumbers = array_values(array_filter(
        $cluster['numbers'],
        fn ($layoutNumber) => isset($machinesByNumber[(int) $layoutNumber])
    ));
@endphp
@if ($layoutNumbers !== [])
<section class="em-cluster em-cluster--{{ $cluster['tone'] }} {{ ! empty($cluster['compact']) ? 'em-cluster--compact' : '' }}">
    <header class="em-cluster-head">
        @if (auth()->user()->isAdmin())
            <form method="POST" action="{{ route('admin.layout-clusters.update') }}" class="em-cluster-rename">
                @csrf
                <input type="hidden" name="key" value="{{ $cluster['key'] }}">
                <input
                    type="text"
                    name="name"
                    value="{{ $clusterNames[$cluster['key']] ?? '' }}"
                    maxlength="80"
                    required
                    aria-label="Nama {{ $clusterNames[$cluster['key']] ?? 'cluster' }}"
                >
                <button type="submit">Simpan</button>
            </form>
        @else
            <span class="em-cluster-kicker">{{ $clusterNames[$cluster['key']] ?? '' }}</span>
        @endif
    </header>
    <div class="em-cluster-cells" style="grid-template-columns: {{ ! empty($cluster['compact']) ? 'repeat('.count($layoutNumbers).', 4.5rem)' : 'repeat('.count($layoutNumbers).', minmax(0, 1fr))' }};">
        @foreach ($layoutNumbers as $layoutNumber)
            @include('admin.partials.machine-layout-cell', [
                'number' => $layoutNumber,
                'machinesByNumber' => $machinesByNumber,
                'machineStats' => $machineStats,
                'runningMachines' => $runningMachines,
                'breakdownMachines' => $breakdownMachines ?? [],
                'standbyMachines' => $standbyMachines ?? [],
            ])
        @endforeach
    </div>
</section>
@endif
