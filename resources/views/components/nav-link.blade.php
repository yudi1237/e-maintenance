@props(['active'])

@php
$classes = ($active ?? false)
            ? 'em-nav-current inline-flex items-center rounded-lg px-2.5 py-1.5 text-sm font-semibold leading-5 shadow-sm focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded-lg px-2.5 py-1.5 text-sm font-medium leading-5 text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
