@props([
    'omitSuccess' => false,
    'omitErrorKeys' => [],
])

@php
    $success = $omitSuccess ? null : session('success');
    $visibleErrors = collect($errors->messages())
        ->except($omitErrorKeys)
        ->flatten()
        ->filter()
        ->values();
@endphp

@if (filled($success))
    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
        <svg class="h-5 w-5 shrink-0 text-emerald-500 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ $success }}</span>
    </div>
@endif

@if ($visibleErrors->isNotEmpty())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($visibleErrors as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
