@once
    <div
        id="close-status-modal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center overflow-y-auto p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="close-status-modal-title"
    >
        <div class="absolute inset-0 z-0 bg-slate-950/60 backdrop-blur-[4px]" data-close-status-cancel></div>
        <div class="relative z-10 my-auto w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl sm:p-8 dark:border dark:border-white/10 dark:bg-slate-900">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h2 id="close-status-modal-title" class="mt-5 text-2xl font-bold text-slate-800 dark:text-white">Tindakan Perbaikan</h2>
            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500 dark:text-slate-400">
                Lengkapi tindakan perbaikan untuk memproses tiket <span class="font-semibold text-slate-700 dark:text-slate-200" data-close-status-machine></span> menjadi Maintenance.
            </p>

            <div class="mt-6 space-y-4 text-left">
                <div>
                    <label for="close-status-damage-cause" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Kategori Penyebab Kerusakan <span class="text-red-500">*</span></label>
                    <select
                        id="close-status-damage-cause"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 shadow-inner outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-950 dark:text-white dark:focus:bg-slate-900 dark:focus:ring-cyan-500/20"
                        data-close-status-damage-cause
                    >
                        <option value="">Pilih kategori penyebab kerusakan</option>
                        @foreach (\App\Support\DamageCauseCategory::labels() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 hidden text-sm font-medium text-red-500" data-close-status-damage-error>Kategori Penyebab Kerusakan wajib dipilih sebelum tiket diproses Maintenance.</p>
                </div>
                <div>
                    <label for="close-status-line-stop" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Line Stop Mesin <span class="text-red-500">*</span></label>
                    <select
                        id="close-status-line-stop"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 shadow-inner outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-950 dark:text-white dark:focus:bg-slate-900 dark:focus:ring-cyan-500/20"
                        data-close-status-line-stop
                    >
                        <option value="">Pilih line stop mesin</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                    <p class="mt-2 hidden text-sm font-medium text-red-500" data-close-status-line-stop-error>Line Stop Mesin wajib dipilih sebelum tiket diproses Maintenance.</p>
                </div>
                <div>
                    <label for="close-status-sub-damage" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Sub Kerusakan <span class="text-red-500">*</span></label>
                    <select
                        id="close-status-sub-damage"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 shadow-inner outline-none transition focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-950 dark:text-white dark:focus:bg-slate-900 dark:focus:ring-cyan-500/20"
                        data-close-status-sub-damage
                    >
                        <option value="">Pilih sub kerusakan</option>
                        @foreach (\App\Models\DamageCategory::query()->active()->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->displayLabel() }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 hidden text-sm font-medium text-red-500" data-close-status-sub-damage-error>Sub Kerusakan wajib dipilih sebelum tiket diproses Maintenance.</p>
                </div>
                <div>
                    <label for="close-status-tindakan" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Tindakan Perbaikan <span class="text-red-500">*</span></label>
                    <textarea
                        id="close-status-tindakan"
                        rows="5"
                        class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 shadow-inner outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-100 dark:border-slate-600 dark:bg-slate-950 dark:text-white dark:focus:bg-slate-900 dark:focus:ring-cyan-500/20"
                        placeholder="Jelaskan tindakan perbaikan yang sudah dilakukan..."
                        data-close-status-textarea
                    ></textarea>
                    <p class="mt-2 hidden text-sm font-medium text-red-500" data-close-status-error>Tindakan Perbaikan wajib diisi sebelum tiket diproses Maintenance.</p>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" class="w-1/2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white" data-close-status-cancel>Batal</button>
                <button type="button" class="w-1/2 rounded-xl px-5 py-3 text-sm font-semibold transition" style="background: linear-gradient(135deg, #06b6d4 0%, #14b8a6 100%); color: #ffffff; box-shadow: 0 14px 28px rgba(20, 184, 166, 0.28);" data-close-status-submit>
                    Simpan
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const closeStatusValue = @json(\App\Support\RepairStatus::CLOSE);
                const statusColors = @json(\App\Support\RepairStatus::legendColors());
                const modal = document.getElementById('close-status-modal');
                const textarea = modal?.querySelector('[data-close-status-textarea]');
                const damageCause = modal?.querySelector('[data-close-status-damage-cause]');
                const lineStop = modal?.querySelector('[data-close-status-line-stop]');
                const subDamage = modal?.querySelector('[data-close-status-sub-damage]');
                const error = modal?.querySelector('[data-close-status-error]');
                const damageError = modal?.querySelector('[data-close-status-damage-error]');
                const lineStopError = modal?.querySelector('[data-close-status-line-stop-error]');
                const subDamageError = modal?.querySelector('[data-close-status-sub-damage-error]');
                const machine = modal?.querySelector('[data-close-status-machine]');
                let activeForm = null;
                let activeSelect = null;

                if (modal && modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }

                function applyStatusColor(select) {
                    select.style.color = statusColors[select.value] || statusColors.open;
                }

                function openModal({ form, machineNumber, select = null }) {
                    activeForm = form;
                    activeSelect = select;
                    const input = form?.querySelector('[data-tindakan-perbaikan-input]');
                    const damageInput = form?.querySelector('[data-damage-cause-input]');
                    const lineStopInput = form?.querySelector('[data-line-stop-input]');
                    const subDamageInput = form?.querySelector('[data-sub-damage-input]');
                    textarea.value = input?.value || '';
                    if (damageCause) {
                        damageCause.value = damageInput?.value || '';
                    }
                    if (lineStop) {
                        lineStop.value = lineStopInput?.value || '';
                    }
                    if (subDamage) {
                        subDamage.value = subDamageInput?.value || '';
                    }
                    error.classList.add('hidden');
                    damageError?.classList.add('hidden');
                    lineStopError?.classList.add('hidden');
                    subDamageError?.classList.add('hidden');
                    machine.textContent = 'Mesin #' + machineNumber;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    setTimeout(() => (damageCause || textarea).focus(), 50);
                }

                function closeModal(resetSelect = true) {
                    if (resetSelect && activeSelect) {
                        activeSelect.value = activeSelect.dataset.currentStatus;
                        applyStatusColor(activeSelect);
                    }
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                    activeForm = null;
                    activeSelect = null;
                }

                document.querySelectorAll('[data-status-select]').forEach(select => {
                    applyStatusColor(select);
                    select.addEventListener('change', () => {
                        applyStatusColor(select);
                        if (select.value === closeStatusValue) {
                            openModal({
                                form: select.form,
                                machineNumber: select.dataset.reportMachine,
                                select,
                            });
                            return;
                        }

                        select.form.submit();
                    });
                });

                document.querySelectorAll('[data-approve-close]').forEach(button => {
                    button.addEventListener('click', () => {
                        openModal({
                            form: button.closest('form'),
                            machineNumber: button.dataset.reportMachine,
                        });
                    });
                });

                modal?.querySelectorAll('[data-close-status-cancel]').forEach(button => {
                    button.addEventListener('click', () => closeModal());
                });

                modal?.querySelector('[data-close-status-submit]')?.addEventListener('click', () => {
                    const category = (damageCause?.value || '').trim();
                    const lineStopValue = (lineStop?.value || '').trim();
                    const subDamageValue = (subDamage?.value || '').trim();
                    const tindakanPerbaikan = textarea.value.trim();
                    let hasError = false;

                    if (!category) {
                        damageError?.classList.remove('hidden');
                        hasError = true;
                    } else {
                        damageError?.classList.add('hidden');
                    }

                    if (!lineStopValue) {
                        lineStopError?.classList.remove('hidden');
                        hasError = true;
                    } else {
                        lineStopError?.classList.add('hidden');
                    }

                    if (!subDamageValue) {
                        subDamageError?.classList.remove('hidden');
                        hasError = true;
                    } else {
                        subDamageError?.classList.add('hidden');
                    }

                    if (!tindakanPerbaikan) {
                        error.classList.remove('hidden');
                        hasError = true;
                    } else {
                        error.classList.add('hidden');
                    }

                    if (hasError) {
                        if (!category) {
                            damageCause?.focus();
                        } else if (!lineStopValue) {
                            lineStop?.focus();
                        } else if (!subDamageValue) {
                            subDamage?.focus();
                        } else {
                            textarea.focus();
                        }
                        return;
                    }

                    const form = activeForm;
                    const input = form?.querySelector('[data-tindakan-perbaikan-input]');
                    const damageInput = form?.querySelector('[data-damage-cause-input]');
                    const lineStopInput = form?.querySelector('[data-line-stop-input]');
                    const subDamageInput = form?.querySelector('[data-sub-damage-input]');
                    if (!form || !input || !damageInput || !lineStopInput || !subDamageInput) {
                        return;
                    }

                    input.value = tindakanPerbaikan;
                    damageInput.value = category;
                    lineStopInput.value = lineStopValue;
                    subDamageInput.value = subDamageValue;
                    closeModal(false);
                    form.submit();
                });

                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });
            })();
        </script>
    @endpush
@endonce
