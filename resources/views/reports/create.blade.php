<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        {{-- Hero header --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900 em-page-hero">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-cyan-200/90 hover:text-white">← Kembali ke pilihan</a>
                <div class="flex items-center gap-2 mt-4 mb-4">
                    <span class="rounded-full bg-cyan-500/20 border border-cyan-400/40 px-3 py-1 text-xs font-semibold text-cyan-200">Langkah 1 dari 2</span>
                </div>
                <div class="flex items-start gap-4">
                    <div class="hidden sm:flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 backdrop-blur border border-white/20">
                        <svg class="h-7 w-7 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-cyan-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                        <h1 class="mt-1 text-xl sm:text-3xl font-bold text-white leading-tight">Form Laporan Problem Mesin</h1>
                        <p class="mt-2 text-slate-300 text-sm max-w-lg">Isi data kerusakan dan waktu perbaikan. Tanggal & PIC terisi otomatis.</p>
                    </div>
                </div>

                {{-- Quick info chips --}}
                <div class="mt-6 em-chip-row">
                    <div class="em-chip bg-white/10 backdrop-blur border border-white/10 text-white">
                        <svg class="h-4 w-4 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-slate-300">Tanggal:</span>
                        <strong>{{ $currentDateTime }}</strong>
                    </div>
                    <div class="em-chip bg-white/10 backdrop-blur border border-white/10 text-white">
                        <svg class="h-4 w-4 text-emerald-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-slate-300">PIC:</span>
                        <strong>{{ $picName }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="em-shell pb-10 -mt-4 sm:pb-12 sm:-mt-6">
            <x-flash-popup />

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

            <form method="POST" action="{{ route('reports.store') }}" id="report-form" class="space-y-5">
                @csrf
                <input type="hidden" name="report_date" value="{{ $currentDate }}">

                {{-- Tiket OP --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Pilih Tiket</h2>
                            <p class="text-xs text-slate-500">Hanya tiket On Progress yang sudah Anda ambil</p>
                        </div>
                    </div>
                    <div>
                        <label for="operator_report_id" class="em-label-light">Pilih tiket On Progress *</label>
                        <select id="operator_report_id" name="operator_report_id" class="em-input-light" required>
                            <option value="">— Pilih tiket On Progress —</option>
                            @forelse ($openTickets as $ticket)
                                @php
                                    $claimed = $ticket->claimed_at?->timezone('Asia/Jakarta');
                                @endphp
                                <option
                                    value="{{ $ticket->id }}"
                                    data-machine-name-id="{{ $ticket->machine_name_id }}"
                                    data-item-name="{{ $ticket->item_name }}"
                                    data-claimed-date="{{ $claimed?->format('Y-m-d') }}"
                                    data-claimed-hour="{{ $claimed ? (int) $claimed->format('H') : '' }}"
                                    data-claimed-minute="{{ $claimed ? (int) $claimed->format('i') : '' }}"
                                    @selected((string) $selectedTicketId === (string) $ticket->id)
                                >
                                    {{ $ticket->ticket_label }}
                                </option>
                            @empty
                                <option value="" disabled>Belum ada tiket On Progress. Ambil tiket Open di daftar atas.</option>
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('operator_report_id')" class="mt-2" />
                    </div>
                </div>

                {{-- Section: Mesin --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Informasi Mesin</h2>
                            <p class="text-xs text-slate-500">Terisi otomatis dari tiket Input OP</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label for="machine_name_id" class="em-label-light">No Mesin & Nama Mesin *</label>
                            <select id="machine_name_id" name="machine_name_id" class="em-input-light bg-slate-50 dark:bg-slate-900/80" required>
                                <option value="">— Pilih tiket terlebih dahulu —</option>
                                @forelse ($machineNames as $machine)
                                    <option value="{{ $machine->id }}" @selected(old('machine_name_id', optional($openTickets->firstWhere('id', (int) $selectedTicketId))->machine_name_id) == $machine->id)>
                                        Mesin {{ $machine->machine_number }} — {{ $machine->view_label }}
                                    </option>
                                @empty
                                    <option value="" disabled>Belum ada data master mesin. Tambahkan dari menu Admin → Master Data Mesin.</option>
                                @endforelse
                            </select>
                            <p class="mt-2 text-xs text-slate-500">Field ini mengikuti tiket yang dipilih.</p>
                            <x-input-error :messages="$errors->get('machine_name_id')" class="mt-2" />
                            <x-input-error :messages="$errors->get('machine_number')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Section: Kerusakan --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Detail Kerusakan</h2>
                            <p class="text-xs text-slate-500">Kategori dan komponen terkait</p>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label for="damage_category_id" class="em-label-light">Kategori Kerusakan *</label>
                            <select id="damage_category_id" name="damage_category_id" class="em-input-light" required>
                                <option value="">— Pilih Kategori Kerusakan —</option>
                                @foreach ($damageCategories as $category)
                                    <option value="{{ $category->id }}" data-allows-other="{{ $category->allows_other ? '1' : '0' }}" @selected(old('damage_category_id', $draftReport?->damage_category_id) == $category->id)>
                                        {{ $category->displayLabel() }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('damage_category_id')" class="mt-2" />
                        </div>
                        <div class="hidden rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10" id="other-category-wrap">
                            <label for="damage_category_other" class="em-label-light text-amber-700 dark:text-amber-300">Keterangan Other *</label>
                            <input id="damage_category_other" name="damage_category_other" type="text" class="em-input-light mt-2 border-amber-200 focus:border-amber-400 focus:ring-amber-400/20" value="{{ old('damage_category_other', $draftReport?->damage_category_other) }}" placeholder="Jelaskan kategori other...">
                        </div>
                        <div>
                            <label for="mold_assy_utility" class="em-label-light">Nama Mold / Assy / Utility</label>
                            <input id="mold_assy_utility" name="mold_assy_utility" type="text" class="em-input-light" value="{{ old('mold_assy_utility', $draftReport?->mold_assy_utility) }}" placeholder="Contoh: Mold ABC, Hot Runner, dll">
                        </div>
                    </div>
                </div>

                {{-- Section: Shift & Waktu --}}
                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Shift & Waktu Perbaikan</h2>
                            <p class="text-xs text-slate-500">Default mulai dari waktu OP diambil. Default selesai dari waktu Close by teknisi. Keduanya masih bisa diubah.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label for="work_shift" class="em-label-light">Shift Kerja *</label>
                            <select id="work_shift" name="work_shift" class="em-input-light" required>
                                @foreach ($shiftLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('work_shift', $detectedShift) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 text-cyan-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                Dapat diubah manual jika shift berbeda
                            </p>
                        </div>
                        <div>
                            <label class="em-label-light">Waktu Mulai *</label>
                            <x-time-24h
                                prefix="repair_started"
                                class="mt-1.5"
                                :date-value="$startedDate"
                                :hour-value="$startedHour"
                                :minute-value="$startedMinute"
                            />
                            <p class="mt-1 text-xs text-slate-500">Default: waktu OP diambil. Masih bisa diubah.</p>
                            <x-input-error :messages="$errors->get('repair_started_date')" class="mt-1" />
                            <x-input-error :messages="$errors->get('repair_started_hour')" class="mt-1" />
                            <x-input-error :messages="$errors->get('repair_started_at')" class="mt-1" />
                        </div>
                        <div>
                            <label class="em-label-light">Waktu Selesai *</label>
                            <x-time-24h
                                prefix="repair_finished"
                                class="mt-1.5"
                                :date-value="$finishedDate"
                                :hour-value="$finishedHour"
                                :minute-value="$finishedMinute"
                            />
                            <p class="mt-1 text-xs text-slate-500">Default: waktu OP Close by teknisi. Masih bisa diubah.</p>
                            <x-input-error :messages="$errors->get('repair_finished_date')" class="mt-1" />
                            <x-input-error :messages="$errors->get('repair_finished_hour')" class="mt-1" />
                            <x-input-error :messages="$errors->get('repair_finished_at')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-2 sticky bottom-0 z-20 em-form-sticky backdrop-blur py-3 -mx-3 px-3 sm:static sm:z-auto sm:bg-transparent sm:p-0 sm:mx-0 border-t sm:border-0">
                    <p class="text-xs text-slate-500 text-center sm:text-left">
                        Setelah Form 1, Anda akan diarahkan ke <strong>Form 2</strong>
                    </p>
                    <button type="submit" class="em-btn-primary sm:min-w-[200px]">
                        Lanjut ke Form 2 →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-data-terkirim-modal />

    @push('scripts')
    <script>
        const categorySelect = document.getElementById('damage_category_id');
        const otherWrap = document.getElementById('other-category-wrap');
        const otherInput = document.getElementById('damage_category_other');
        const ticketSelect = document.getElementById('operator_report_id');
        const machineSelect = document.getElementById('machine_name_id');
        const moldInput = document.getElementById('mold_assy_utility');

        function toggleOtherField() {
            const selected = categorySelect.options[categorySelect.selectedIndex];
            const allowsOther = selected?.dataset.allowsOther === '1';
            otherWrap.classList.toggle('hidden', !allowsOther);
            otherInput.required = allowsOther;
        }

        const preserveRepairTimes = {{ $preserveRepairTimes ? 'true' : 'false' }};

        function setTimeFields(prefix, dateValue, hourValue, minuteValue) {
            const dateEl = document.getElementById(prefix + '_date');
            const hourEl = document.getElementById(prefix + '_hour');
            const minuteEl = document.getElementById(prefix + '_minute');
            if (!dateEl || !hourEl || !minuteEl || !dateValue) {
                return;
            }

            dateEl.value = dateValue;
            hourEl.value = String(hourValue);
            minuteEl.value = String(minuteValue);
        }

        function applyTicketSelection(forceTimes = false) {
            const selected = ticketSelect?.options[ticketSelect.selectedIndex];
            const machineId = selected?.dataset.machineNameId || '';
            const itemName = selected?.dataset.itemName || '';

            if (machineSelect && machineId) {
                machineSelect.value = machineId;
            }

            if (moldInput && itemName && !moldInput.value) {
                moldInput.value = itemName;
            }

            if (forceTimes || !preserveRepairTimes) {
                const claimedDate = selected?.dataset.claimedDate || '';
                const claimedHour = selected?.dataset.claimedHour ?? '';
                const claimedMinute = selected?.dataset.claimedMinute ?? '';
                if (claimedDate !== '') {
                    setTimeFields('repair_started', claimedDate, claimedHour, claimedMinute);
                }
            }
        }

        categorySelect.addEventListener('change', toggleOtherField);
        toggleOtherField();

        ticketSelect?.addEventListener('change', function () {
            if (moldInput) {
                moldInput.value = '';
            }
            applyTicketSelection(true);
        });
        applyTicketSelection(false);

        if (document.getElementById('form-errors')) {
            document.getElementById('form-errors').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
    @endpush
</x-app-layout>
