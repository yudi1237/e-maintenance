@props(['name'])

@php
    $icons = [
        'mesin' => <<<'SVG'
            <path d="M4 20h16"/>
            <path d="M6 20V10.5h12V20"/>
            <path d="M9 10.5V6.2L12 3.5l3 2.7V10.5"/>
            <path d="M9 6.4h6"/>
            <path d="M8.2 14.2h3.1M12.7 14.2h3.1"/>
            <path d="M12 10.5v2.2"/>
            <path d="M7.2 17.2h9.6"/>
        SVG,
        'conveyor' => <<<'SVG'
            <path d="M3.5 13.8h17"/>
            <path d="M3.5 17.6h17"/>
            <circle cx="6.2" cy="15.7" r="1.85"/>
            <circle cx="12" cy="15.7" r="1.85"/>
            <circle cx="17.8" cy="15.7" r="1.85"/>
            <rect x="8.4" y="6.2" width="7.2" height="5.2" rx="1"/>
            <path d="M4.2 13.8 7 8.8"/>
            <path d="M15.6 8.8h3.2"/>
        SVG,
        'mtc' => <<<'SVG'
            <rect x="3.5" y="4" width="11.5" height="16" rx="2"/>
            <rect x="5.6" y="6.6" width="7.3" height="4.6" rx="0.8"/>
            <path d="M6.7 13.6h5.2M6.7 16.2h3.4"/>
            <path d="M15 7.2h4.2v3.2h-2.4v5.2H20v3.2h-6.4"/>
            <circle cx="9.3" cy="8.9" r="0.7" fill="currentColor" stroke="none"/>
        SVG,
        'hopper' => <<<'SVG'
            <path d="M8 4.2h8"/>
            <path d="M8.8 4.2V3.2h6.4v1"/>
            <path d="M7.2 4.2h9.6v5.8L13.6 17.6h-3.2L7.2 10V4.2z"/>
            <circle cx="12" cy="8.6" r="1.6"/>
            <path d="M10.4 17.6v3.2M13.6 17.6v3.2M9.2 20.8h5.6"/>
        SVG,
        'dehum' => <<<'SVG'
            <circle cx="10.6" cy="11" r="6.4"/>
            <circle cx="10.6" cy="11" r="1.4"/>
            <path d="M10.6 6.2v2.2M10.6 13.6v2.2M5.8 11h2.2M13.2 11h2.2"/>
            <path d="M10.6 11 13.8 8.2M10.6 11l2.8 3.6M10.6 11 7 12.4"/>
            <path d="M18.6 13.2s-2.4 3-2.4 4.6a2.4 2.4 0 004.8 0c0-1.6-2.4-4.6-2.4-4.6z"/>
        SVG,
        'robot' => <<<'SVG'
            <path d="M4.8 20.2h6.4v-2.6H4.8z"/>
            <path d="M7.2 17.6V11.2"/>
            <circle cx="7.2" cy="11.2" r="1.45"/>
            <path d="M8.6 11.2h7.2"/>
            <circle cx="17.2" cy="11.2" r="1.45"/>
            <path d="M17.2 9.8V6.4"/>
            <circle cx="17.2" cy="5.2" r="1.35"/>
            <path d="M15.4 5.2h-1.7M19 5.2h1.7"/>
            <path d="M13.7 3.6v3.2M20.7 3.6v3.2"/>
        SVG,
        'hot' => <<<'SVG'
            <path d="M12 3.2c.3 3.6-3.2 5.2-3.2 8.7a3.2 3.2 0 006.4 0c0-3.5-3.5-5.1-3.2-8.7z"/>
            <path d="M12 9.6c.15 1.5-1.15 2.2-1.15 3.6a1.15 1.15 0 002.3 0c0-1.4-1.3-2.1-1.15-3.6z"/>
            <path d="M8.2 20.5h7.6"/>
            <path d="M9.2 16.8h5.6v3.7H9.2z"/>
            <path d="M10.6 16.8v-1.8M13.4 16.8v-1.8"/>
        SVG,
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'h-7 w-7']) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $icons[$name] ?? '' !!}
</svg>
