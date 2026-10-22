<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative em-shell py-5 sm:py-6">
                <a href="{{ route('preventive.create') }}" class="inline-flex items-center gap-1 text-sm text-emerald-200/90 hover:text-white">← Kembali ke pilih mesin</a>
                <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-emerald-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                        <h1 class="mt-1 text-xl sm:text-2xl font-bold text-white leading-tight">Kategori Preventive</h1>
                        <p class="mt-1 text-slate-300 text-sm">Mesin {{ $machine->machine_number }} — {{ $machine->view_label }}. Pilih kategori preventive yang akan diisi.</p>
                    </div>
                    <div class="em-chip-row sm:justify-end">
                        <div class="em-chip bg-white/10 backdrop-blur border border-white/10 text-white">
                            <span class="text-slate-300">Tanggal:</span>
                            <strong>{{ $currentDateTime }}</strong>
                        </div>
                        <div class="em-chip bg-white/10 backdrop-blur border border-white/10 text-white">
                            <span class="text-slate-300">PIC:</span>
                            <strong>{{ $picName }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="em-shell pt-5 pb-10">
            <x-alert-light />

            <div class="grid grid-cols-5 gap-2 sm:gap-3">
                @foreach ($categories as $category => $label)
                    <a href="{{ route('preventive.form', [$machine->machine_number, $category]) }}"
                       class="em-machine-card em-machine-card--idle min-h-[3.5rem] sm:min-h-[4.25rem]">
                        <span class="text-base font-bold tracking-wide text-slate-800 dark:text-slate-200 sm:text-lg">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
