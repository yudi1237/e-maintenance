@props(['active' => false])

@php
$classes = $active
    ? 'em-nav-current inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-sm font-semibold leading-5 shadow-sm focus:outline-none'
    : 'inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-sm font-medium leading-5 text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button type="button" @click="open = ! open" {{ $attributes->merge(['class' => $classes]) }} :aria-expanded="open.toString()">
        {{ $trigger }}
        <svg class="h-4 w-4 transition" :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute start-0 z-50 mt-2 w-64"
        @click="open = false"
    >
        <div class="rounded-lg bg-white py-1 shadow-lg ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-white/10">
            {{ $content }}
        </div>
    </div>
</div>
