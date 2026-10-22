@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-cyan-500 text-sm font-medium leading-5 text-cyan-700 dark:text-white focus:outline-none focus:border-cyan-400 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 hover:text-slate-800 hover:border-slate-300 dark:text-slate-300 dark:hover:text-white dark:hover:border-slate-500 focus:outline-none focus:text-slate-800 dark:focus:text-white focus:border-slate-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
