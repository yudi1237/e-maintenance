<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative em-shell py-5 sm:py-6">
                <a href="{{ route('preventive.menu') }}" class="inline-flex items-center gap-1 text-sm text-emerald-200/90 hover:text-white">← Kembali ke menu preventive</a>
                <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-emerald-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                        <h1 class="mt-1 text-xl sm:text-2xl font-bold text-white leading-tight">
                            @if ($lockedCategory)
                                {{ \App\Support\PreventiveCategory::formTitle($lockedCategory) }}
                            @else
                                Preventive Mesin
                            @endif
                        </h1>
                        <p class="mt-1 text-slate-300 text-sm">
                            @if ($lockedCategory)
                                Pilih nomor mesin, lalu isi {{ \App\Support\PreventiveCategory::numberLabel($lockedCategory) }} di form.
                            @else
                                Pilih mesin dari Master Data Mesin, lalu pilih kategori preventive.
                            @endif
                        </p>
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

            <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-2 sm:gap-3">
                @forelse ($machineNames as $machine)
                    <a href="{{ $lockedCategory
                        ? route('preventive.form', [$machine->machine_number, $lockedCategory])
                        : route('preventive.categories', $machine->machine_number) }}"
                       title="{{ $machine->view_label }}"
                       class="em-machine-card em-machine-card--idle">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500">Mesin</span>
                        <span class="em-machine-num text-2xl font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $machine->machine_number }}</span>
                        <span class="mt-1 text-[10px] text-slate-500 dark:text-slate-400 max-w-full truncate">{{ $machine->view_label }}</span>
                    </a>
                @empty
                    <div class="col-span-full em-glass rounded-2xl p-6 text-center text-sm text-slate-400">
                        Belum ada master mesin. Tambahkan data di menu Asset → Data Mesin.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
