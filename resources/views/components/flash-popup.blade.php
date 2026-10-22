@php
    $success = session('success');
    $errorMessages = collect($errors->messages())
        ->only(['pin', 'technician_id', 'ticket'])
        ->flatten()
        ->filter()
        ->values();
    $isError = $errorMessages->isNotEmpty();
    $message = $isError ? $errorMessages->implode(' ') : (is_string($success) ? $success : null);
    $show = filled($message) && ! session('data_terkirim');

    $isPinError = $isError && $errorMessages->contains(fn (string $item): bool => str_contains(mb_strtolower($item), 'pin'));
    $isPinSuccess = ! $isError && is_string($success) && str_contains(mb_strtolower($success), 'terverifikasi');
    $title = $isPinError
        ? 'PIN tidak sesuai'
        : ($isPinSuccess ? 'PIN terverifikasi' : ($isError ? 'Tidak dapat diproses' : 'Berhasil'));
@endphp

@if ($show)
    <div
        id="flash-popup"
        data-flash-popup
        role="dialog"
        aria-modal="true"
        aria-labelledby="flash-popup-title"
        class="flex items-center justify-center p-4"
        style="position:fixed;inset:0;z-index:2147483646;background:rgba(15,23,42,.6);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);"
        onclick="if (event.target === this) this.remove();"
    >
        <div
            class="w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 shadow-2xl text-center dark:border dark:border-white/10 dark:bg-slate-900"
            style="position:relative;z-index:1;pointer-events:auto;"
            onclick="event.stopPropagation();"
        >
            @if ($isError)
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/20">
                    <svg class="h-8 w-8 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            @else
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20">
                    <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            @endif
            <h2 id="flash-popup-title" class="mt-5 text-2xl font-bold text-slate-800 dark:text-white">{{ $title }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $message }}</p>
            <button
                type="button"
                class="mt-6 em-btn-primary w-full justify-center"
                style="position:relative;z-index:2;pointer-events:auto;cursor:pointer;"
                onclick="document.getElementById('flash-popup')?.remove();"
            >
                OK
            </button>
        </div>
    </div>
    <script>
        (function () {
            var popup = document.getElementById('flash-popup');
            if (popup && popup.parentElement !== document.body) {
                document.body.appendChild(popup);
            }
        })();
    </script>
@endif
