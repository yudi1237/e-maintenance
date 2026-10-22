@props(['urls' => []])

@if (count($urls) > 0)
    <div {{ $attributes->merge(['class' => 'mt-6 border-t border-slate-200 pt-6 dark:border-white/10']) }}>
        <h3 class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-3">Evidence</h3>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach ($urls as $url)
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50 hover:border-emerald-300 dark:border-white/10 dark:bg-slate-900/40">
                    <img src="{{ $url }}" alt="Evidence preventive" class="h-36 w-full object-cover">
                </a>
            @endforeach
        </div>
    </div>
@endif
