@props(['active' => false])

@php
$classes = $active
    ? 'em-nav-sub-current block w-full px-4 py-2 text-start text-sm font-semibold leading-5'
    : 'block w-full px-4 py-2 text-start text-sm leading-5 text-slate-700 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700 dark:focus:bg-slate-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
