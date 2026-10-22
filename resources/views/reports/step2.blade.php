<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        {{-- Hero header (selaras Form 1) --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 em-page-hero">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-violet-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative">
                <div class="em-step-bar mb-6">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 border border-emerald-400/50 text-emerald-300 text-sm font-bold">✓</span>
                        <span class="text-sm text-emerald-200/90 hidden sm:inline">Form 1</span>
                    </div>
                    <div class="h-px flex-1 max-w-[80px] bg-gradient-to-r from-emerald-400/50 to-cyan-400/50"></div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-500/30 border-2 border-cyan-400 text-cyan-100 text-sm font-bold">2</span>
                        <span class="text-sm font-semibold text-white hidden sm:inline">Form 2</span>
                    </div>
                    <span class="w-full sm:w-auto sm:ml-auto text-center rounded-full bg-cyan-500/20 border border-cyan-400/40 px-3 py-1 text-xs font-semibold text-cyan-200">Langkah 2 dari 2</span>
                </div>

                <div class="flex items-start gap-4">
                    <div class="hidden sm:flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 backdrop-blur border border-white/20">
                        <svg class="h-7 w-7 text-violet-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-cyan-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                        <h1 class="mt-1 text-xl sm:text-3xl font-bold text-white leading-tight">Form Analisa & Tindakan Perbaikan</h1>
                        <p class="mt-2 text-slate-300 text-sm max-w-lg">Lengkapi masalah, analisa akar penyebab, tindakan perbaikan, dan status.</p>
                    </div>
                </div>

                {{-- Ringkasan Form 1 --}}
                <div class="mt-6 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4 sm:gap-3">
                    <div class="flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 px-4 py-3 text-sm text-white">
                        <svg class="h-4 w-4 text-cyan-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div class="min-w-0">
                            <span class="text-slate-400 text-xs block">Mesin</span>
                            <strong class="truncate block">#{{ $report->machine_number }} — {{ $report->machine_name_display }}</strong>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 px-4 py-2.5 text-sm text-white">
                        <svg class="h-4 w-4 text-amber-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="min-w-0">
                            <span class="text-slate-400 text-xs block">Kategori</span>
                            <strong class="truncate block text-xs leading-tight">{{ $report->damage_category_display }}</strong>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 px-4 py-2.5 text-sm text-white">
                        <svg class="h-4 w-4 text-emerald-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <div>
                            <span class="text-slate-400 text-xs block">PIC</span>
                            <strong>{{ $report->pic_name }}</strong>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 px-4 py-2.5 text-sm text-white">
                        <svg class="h-4 w-4 text-indigo-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div>
                            <span class="text-slate-400 text-xs block">Tanggal</span>
                            <strong>{{ $report->report_date->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="em-shell pb-10 -mt-4 sm:pb-12 sm:-mt-6">
            <x-flash-popup />
            <x-alert-light omit-success :omit-error-keys="['pin']" />

            <form method="POST" action="{{ route('reports.step2.store', $report) }}" enctype="multipart/form-data" class="space-y-5" id="step2-form">
                @csrf

                {{-- Masalah & Analisa --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Masalah & Analisa</h2>
                            <p class="text-xs text-slate-500">Deskripsi masalah dan akar penyebab kerusakan</p>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label for="masalah" class="em-label-light">1. Masalah *</label>
                            <textarea id="masalah" name="masalah" rows="4" class="em-input-light" placeholder="Jelaskan masalah yang terjadi secara detail..." required>{{ old('masalah', $report->operatorReport?->problem) }}</textarea>
                        </div>
                        <div>
                            <label for="analisa_penyebab_kerusakan" class="em-label-light">2. Analisa Penyebab Kerusakan *</label>
                            <textarea id="analisa_penyebab_kerusakan" name="analisa_penyebab_kerusakan" rows="4" class="em-input-light" placeholder="Analisa akar penyebab (root cause)..." required>{{ old('analisa_penyebab_kerusakan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Tindakan Perbaikan --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Tindakan Perbaikan</h2>
                            <p class="text-xs text-slate-500">Langkah perbaikan yang dilakukan</p>
                        </div>
                    </div>
                    <div>
                        <label for="tindakan_perbaikan" class="em-label-light">3. Tindakan Perbaikan *</label>
                        <textarea id="tindakan_perbaikan" name="tindakan_perbaikan" rows="5" class="em-input-light" placeholder="Jelaskan tindakan perbaikan yang dilakukan..." required>{{ old('tindakan_perbaikan') }}</textarea>
                    </div>
                </div>

                {{-- Evidence & Sparepart --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Evidence & Sparepart</h2>
                            <p class="text-xs text-slate-500">Evidence opsional. Penggunaan sparepart wajib dipilih.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="em-label-light">4. Upload Evidence (Jika Ada)</label>
                            <label for="evidence" class="mt-2 flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer bg-slate-50 hover:bg-cyan-50/50 hover:border-cyan-300 transition group dark:border-slate-600 dark:bg-slate-900/50 dark:hover:bg-cyan-500/10 dark:hover:border-cyan-400">
                                <div class="flex flex-col items-center justify-center py-4 px-4 text-center" id="evidence-placeholder">
                                    <svg class="h-10 w-10 text-slate-300 group-hover:text-cyan-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300"><span class="font-semibold text-cyan-600 dark:text-cyan-400">Klik untuk upload</span> atau drag file</p>
                                    <p class="text-xs text-slate-400 mt-1">JPG, PNG, PDF — maks. 5 MB</p>
                                </div>
                                <p class="hidden text-sm font-medium text-cyan-700 pb-4" id="evidence-filename"></p>
                                <input id="evidence" name="evidence" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" class="hidden">
                            </label>
                        </div>
                        <div>
                            <label for="penggunaan_sparepart_choice" class="em-label-light">5. Penggunaan Sparepart *</label>
                            <select id="penggunaan_sparepart_choice" name="penggunaan_sparepart_choice" class="em-input-light" required>
                                <option value="">— Pilih —</option>
                                <option value="sparepart" @selected(old('penggunaan_sparepart_choice') === 'sparepart')>Sparepart</option>
                                <option value="none" @selected(old('penggunaan_sparepart_choice') === 'none')>Tidak ada</option>
                            </select>
                            <x-input-error :messages="$errors->get('penggunaan_sparepart_choice')" class="mt-2" />
                            <x-input-error :messages="$errors->get('sparepart_items')" class="mt-2" />

                            @php
                                $oldItems = old('sparepart_items', [['name' => '', 'qty' => '']]);
                                if (! is_array($oldItems) || count($oldItems) === 0) {
                                    $oldItems = [['name' => '', 'qty' => '']];
                                }
                            @endphp
                            <div id="sparepart-items-wrap" class="mt-4 space-y-3 {{ old('penggunaan_sparepart_choice') === 'sparepart' ? '' : 'hidden' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs text-slate-500">Isi nama sparepart dan qty. Minimal satu baris.</p>
                                    <button type="button" id="sparepart-add-row" class="em-btn-secondary !min-h-0 !px-3 !py-1.5 text-xs">+ Tambah baris</button>
                                </div>
                                <div id="sparepart-rows" class="space-y-2">
                                    @foreach ($oldItems as $index => $item)
                                        <div class="sparepart-row grid grid-cols-1 gap-2 sm:grid-cols-[1fr_7rem_auto] sm:items-start">
                                            <div>
                                                <label class="sr-only">Nama sparepart</label>
                                                <input type="text" name="sparepart_items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" class="em-input-light sparepart-name" placeholder="Nama sparepart" maxlength="255">
                                                <x-input-error :messages="$errors->get('sparepart_items.'.$index.'.name')" class="mt-1" />
                                            </div>
                                            <div>
                                                <label class="sr-only">Qty</label>
                                                <input type="number" name="sparepart_items[{{ $index }}][qty]" value="{{ $item['qty'] ?? '' }}" min="1" step="1" class="em-input-light sparepart-qty" placeholder="Qty">
                                                <x-input-error :messages="$errors->get('sparepart_items.'.$index.'.qty')" class="mt-1" />
                                            </div>
                                            <button type="button" class="sparepart-remove em-btn-secondary !min-h-0 !px-3 !py-2 text-xs sm:mt-0">Hapus</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Perbaikan --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Status Perbaikan</h2>
                            <p class="text-xs text-slate-500">Setelah laporan disimpan, tiket menjadi Close by teknisi. Approval berikutnya: Produksi Injection → Quality → Admin Maintenance.</p>
                        </div>
                    </div>
                    <div class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800 dark:border-cyan-500/30 dark:bg-cyan-500/10 dark:text-cyan-200">
                        Status akan diubah menjadi <strong>Close by teknisi</strong> setelah Form 2 tersimpan.
                    </div>
                    <div class="mt-4">
                        <label for="close-pin" class="em-label-light">PIN PIC {{ $report->operatorReport?->pic_name ?? $report->pic_name }} *</label>
                        <input
                            id="close-pin"
                            name="pin"
                            type="password"
                            inputmode="numeric"
                            maxlength="6"
                            autocomplete="one-time-code"
                            data-1p-ignore="true"
                            data-lpignore="true"
                            data-form-type="other"
                            required
                            class="em-input-light mt-1 w-full max-w-xs tracking-[0.35em]"
                            placeholder="••••"
                        >
                        <p class="mt-1 text-xs text-slate-500">Masukkan PIN PIC yang mengambil tiket OP ini untuk Close ticket.</p>
                        <x-input-error :messages="$errors->get('pin')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-2 sticky bottom-0 z-20 em-form-sticky backdrop-blur py-3 -mx-3 px-3 sm:static sm:z-auto sm:bg-transparent sm:p-0 sm:mx-0 border-t sm:border-0">
                    <p class="text-xs text-slate-500 text-center sm:text-left">
                        Data tersimpan ke <strong>dashboard admin</strong>
                    </p>
                    <button type="submit" class="em-btn-primary sm:min-w-[220px]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan & Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const evidenceInput = document.getElementById('evidence');
        const evidenceFilename = document.getElementById('evidence-filename');
        const evidencePlaceholder = document.getElementById('evidence-placeholder');

        evidenceInput?.addEventListener('change', function () {
            if (this.files?.[0]) {
                evidenceFilename.textContent = '📎 ' + this.files[0].name;
                evidenceFilename.classList.remove('hidden');
                evidencePlaceholder.classList.add('hidden');
            }
        });

        const sparepartChoice = document.getElementById('penggunaan_sparepart_choice');
        const sparepartWrap = document.getElementById('sparepart-items-wrap');
        const sparepartRows = document.getElementById('sparepart-rows');
        const sparepartAdd = document.getElementById('sparepart-add-row');

        function toggleSparepartFields() {
            const show = sparepartChoice?.value === 'sparepart';
            sparepartWrap?.classList.toggle('hidden', !show);
            sparepartRows?.querySelectorAll('.sparepart-name, .sparepart-qty').forEach((el) => {
                el.required = show;
            });
        }

        function reindexSparepartRows() {
            sparepartRows?.querySelectorAll('.sparepart-row').forEach((row, index) => {
                const nameInput = row.querySelector('.sparepart-name');
                const qtyInput = row.querySelector('.sparepart-qty');
                if (nameInput) nameInput.name = `sparepart_items[${index}][name]`;
                if (qtyInput) qtyInput.name = `sparepart_items[${index}][qty]`;
            });
        }

        sparepartChoice?.addEventListener('change', toggleSparepartFields);
        toggleSparepartFields();

        sparepartAdd?.addEventListener('click', function () {
            const first = sparepartRows?.querySelector('.sparepart-row');
            if (!first) return;
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach((input) => {
                input.value = '';
            });
            clone.querySelectorAll('p').forEach((el) => el.remove());
            sparepartRows.appendChild(clone);
            reindexSparepartRows();
            toggleSparepartFields();
        });

        sparepartRows?.addEventListener('click', function (event) {
            const button = event.target.closest('.sparepart-remove');
            if (!button) return;
            const rows = sparepartRows.querySelectorAll('.sparepart-row');
            if (rows.length <= 1) {
                rows[0].querySelectorAll('input').forEach((input) => { input.value = ''; });
                return;
            }
            button.closest('.sparepart-row')?.remove();
            reindexSparepartRows();
        });
    </script>
    @endpush
</x-app-layout>
