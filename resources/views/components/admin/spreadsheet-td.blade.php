@props(['label' => null])

<td {{ $attributes->merge(['class' => 'block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10']) }}>
    @if (filled($label))
        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">{{ $label }}</span>
    @endif
    {{ $slot }}
</td>
