<div
    id="op-created-popup"
    role="dialog"
    aria-modal="true"
    aria-labelledby="op-created-title"
    class="flex items-center justify-center p-4"
    style="position:fixed;inset:0;z-index:2147483646;background:rgba(15,23,42,.6);"
>
    <div class="w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 text-center shadow-2xl dark:border dark:border-white/10 dark:bg-slate-900">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20">
            <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 id="op-created-title" class="mt-5 text-2xl font-bold text-slate-800 dark:text-white">OP Berhasil Dibuat</h2>
        <a href="{{ route('operator.home') }}" class="mt-6 em-btn-primary w-full justify-center">Oke</a>
    </div>
</div>
