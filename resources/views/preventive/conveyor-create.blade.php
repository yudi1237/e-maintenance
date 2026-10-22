<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-amber-900 em-page-hero">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-amber-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative max-w-4xl mx-auto">
                <a href="{{ route('preventive.menu') }}" class="inline-flex items-center gap-1 text-sm text-amber-200/90 hover:text-white">← Kembali ke menu preventive</a>
                <p class="mt-4 text-amber-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                <h1 class="mt-1 text-xl sm:text-3xl font-bold text-white leading-tight">Preventive Conveyor</h1>
                <p class="mt-2 text-slate-300 text-sm max-w-lg">Checklist perawatan berkala conveyor. Tanggal & PIC terisi otomatis.</p>

                <div class="mt-6 em-chip-row">
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

        <div class="em-page-body">
            <x-alert-light />

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200" id="form-errors">
                    <p class="font-semibold mb-1">Form belum bisa disimpan. Periksa isian berikut:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('preventive-conveyor.store') }}" class="space-y-5" enctype="multipart/form-data">
                @csrf

                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Informasi Conveyor</h2>
                            <p class="text-xs text-slate-500">Isi conveyor yang dicek preventive</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label for="conveyor_name" class="em-label-light">Nama / No Conveyor *</label>
                            <input id="conveyor_name" name="conveyor_name" type="text" class="em-input-light" value="{{ old('conveyor_name') }}" placeholder="Contoh: Conveyor Line 1" required maxlength="200">
                            <x-input-error :messages="$errors->get('conveyor_name')" class="mt-2" />
                        </div>
                        <div>
                            <label for="work_shift" class="em-label-light">Shift Kerja *</label>
                            <select id="work_shift" name="work_shift" class="em-input-light" required>
                                @foreach ($shiftLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('work_shift', $detectedShift) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach ($checklistGroups as $group)
                        <div class="em-form-section overflow-hidden !p-0">
                            <div class="px-4 py-3 border-b border-amber-200 bg-amber-50 dark:border-amber-500/20 dark:bg-amber-500/10">
                                <h2 class="text-base font-semibold text-amber-900 dark:text-amber-200">{{ $group['label'] }}</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-400">
                                            <th class="px-3 py-2 w-12">No</th>
                                            <th class="px-3 py-2 min-w-[14rem]">Item Pemeriksaan</th>
                                            <th class="px-3 py-2 min-w-[16rem]">Standar/ Metode Cek</th>
                                            <th class="px-3 py-2 text-center w-20">OK</th>
                                            <th class="px-3 py-2 text-center w-24">Not OK</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                        @foreach ($group['items'] as $key => $item)
                                            <tr class="align-top">
                                                <td class="px-3 py-3 text-slate-500 tabular-nums">{{ $loop->iteration }}</td>
                                                <td class="px-3 py-3 font-medium text-slate-800 dark:text-white">{{ $item['label'] }}</td>
                                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">{{ $item['standard'] }}</td>
                                                @foreach ($checklistValues as $value => $valueLabel)
                                                    <td class="px-3 py-3 text-center">
                                                        <label class="inline-flex cursor-pointer items-center justify-center">
                                                            <span class="sr-only">{{ $valueLabel }} — {{ $item['label'] }}</span>
                                                            <input
                                                                type="radio"
                                                                name="items[{{ $key }}]"
                                                                value="{{ $value }}"
                                                                class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-500 dark:bg-slate-900"
                                                                @checked(old('items.'.$key) === $value)
                                                            >
                                                        </label>
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @error('items.'.$key)
                                                <tr>
                                                    <td colspan="5" class="px-3 pb-3 pt-0">
                                                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                    </td>
                                                </tr>
                                            @enderror
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="em-form-section space-y-5">
                    <x-preventive.evidence-upload />
                    <div>
                        <label for="notes" class="em-label-light">Catatan (opsional)</label>
                        <textarea id="notes" name="notes" rows="4" class="em-input-light" placeholder="Temuan, sparepart, atau tindak lanjut...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-2 sticky bottom-0 z-20 em-form-sticky backdrop-blur py-3 -mx-3 px-3 sm:static sm:z-auto sm:bg-transparent sm:p-0 sm:mx-0 border-t sm:border-0">
                    <button type="submit" class="em-btn-primary sm:min-w-[220px]">
                        Simpan Preventive
                    </button>
                </div>
            </form>

            @if ($recentReports->isNotEmpty())
                <div class="mt-8 em-form-section">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white mb-4">Preventive terakhir Anda</h2>
                    <div class="space-y-3">
                        @foreach ($recentReports as $report)
                            <div class="flex flex-col gap-1 rounded-xl border border-slate-200 px-4 py-3 text-sm dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $report->conveyor_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $report->work_date->format('d/m/Y') }} · {{ $report->work_shift_label }}</p>
                                </div>
                                <span @class([
                                    'inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-medium',
                                    'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300' => $report->result === 'ok',
                                    'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' => $report->result !== 'ok',
                                ])>{{ $report->result_label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
