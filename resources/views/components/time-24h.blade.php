@props([
    'prefix',
    'dateValue' => '',
    'hourValue' => 0,
    'minuteValue' => 0,
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <input
        id="{{ $prefix }}_date"
        name="{{ $prefix }}_date"
        type="date"
        class="em-input-light w-full"
        value="{{ old($prefix.'_date', $dateValue) }}"
        required
    >
    <div class="grid grid-cols-[1fr_auto_1fr_auto] items-center gap-2 max-w-full">
        <div>
            <label for="{{ $prefix }}_hour" class="block text-[10px] text-slate-500 mb-1 text-center sm:sr-only">Jam</label>
            <select
                id="{{ $prefix }}_hour"
                name="{{ $prefix }}_hour"
                class="em-input-light w-full font-mono text-center min-h-[44px]"
                required
            >
                @for ($h = 0; $h < 24; $h++)
                    <option value="{{ $h }}" @selected((int) old($prefix.'_hour', $hourValue) === $h)>
                        {{ str_pad((string) $h, 2, '0', STR_PAD_LEFT) }}
                    </option>
                @endfor
            </select>
        </div>
        <span class="text-lg font-bold text-slate-400 pt-4 sm:pt-0">:</span>
        <div>
            <label for="{{ $prefix }}_minute" class="block text-[10px] text-slate-500 mb-1 text-center sm:sr-only">Menit</label>
            <select
                id="{{ $prefix }}_minute"
                name="{{ $prefix }}_minute"
                class="em-input-light w-full font-mono text-center min-h-[44px]"
                required
            >
                @for ($m = 0; $m < 60; $m++)
                    <option value="{{ $m }}" @selected((int) old($prefix.'_minute', $minuteValue) === $m)>
                        {{ str_pad((string) $m, 2, '0', STR_PAD_LEFT) }}
                    </option>
                @endfor
            </select>
        </div>
        <span class="text-xs font-medium text-slate-500 whitespace-nowrap pt-4 sm:pt-0">24j</span>
    </div>
    <p class="text-xs text-slate-400">Format 24 jam (00:00 – 23:59)</p>
</div>
