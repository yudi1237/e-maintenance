@props(['technicians'])

<div
    id="pic-pin-modal"
    class="hidden items-center justify-center overflow-y-auto p-4 sm:p-6"
    style="position:fixed;inset:0;z-index:2147483645;background:rgba(2,6,23,.6);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);"
    role="dialog"
    aria-modal="true"
    aria-labelledby="pic-pin-modal-title"
>
    <div class="relative my-auto w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl sm:p-8 dark:border dark:border-white/10 dark:bg-slate-900" data-pic-pin-panel style="pointer-events:auto;">
        <h2 id="pic-pin-modal-title" class="text-xl font-bold text-slate-800 dark:text-white">Pilih PIC Teknisi</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400" data-pic-pin-copy>
            Hanya teknisi pada shift yang sedang berjalan yang bisa mengambil OP, Preventive, dan tugas teknisi lain.
        </p>

        <form method="POST" class="mt-5 space-y-4" data-pic-pin-form>
            @csrf
            <input type="hidden" name="destination" value="" data-pic-pin-destination>
            <input type="hidden" value="" data-pic-pin-hidden-id>
            <div data-pic-pin-select-wrap>
                <label for="pic-pin-technician" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">PIC Teknisi</label>
                @if ($technicians->isEmpty())
                    <p class="text-sm text-amber-600 dark:text-amber-300">Tidak ada teknisi pada shift yang sedang berjalan. Minta admin memetakan shift di dashboard.</p>
                @else
                    <select
                        id="pic-pin-technician"
                        name="technician_id"
                        class="em-input !mt-0 w-full"
                        data-pic-pin-select
                    >
                        <option value="">— Pilih PIC teknisi —</option>
                        @foreach ($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->optionLabel() }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div data-pic-pin-locked class="hidden">
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">PIC Teknisi</p>
                <p class="mt-1 text-slate-900 dark:text-white" data-pic-pin-locked-name></p>
            </div>
            <div>
                <label for="pic-pin-input" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">PIN</label>
                <input
                    id="pic-pin-input"
                    name="pin"
                    type="password"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="one-time-code"
                    data-1p-ignore="true"
                    data-lpignore="true"
                    data-form-type="other"
                    class="em-input !mt-0 w-full tracking-[0.35em]"
                    placeholder="••••"
                    data-pic-pin-input
                    @disabled($technicians->isEmpty())
                >
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" class="w-1/2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700" data-pic-pin-cancel>Batal</button>
                <button type="submit" class="w-1/2 rounded-xl px-5 py-3 text-sm font-semibold text-white" style="background: linear-gradient(135deg, #06b6d4 0%, #14b8a6 100%);" data-pic-pin-submit @disabled($technicians->isEmpty())>
                    Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const modal = document.getElementById('pic-pin-modal');
            if (!modal) return;

            const form = modal.querySelector('[data-pic-pin-form]');
            const select = modal.querySelector('[data-pic-pin-select]');
            const hiddenId = modal.querySelector('[data-pic-pin-hidden-id]');
            const pinInput = modal.querySelector('[data-pic-pin-input]');
            const copy = modal.querySelector('[data-pic-pin-copy]');
            const title = modal.querySelector('#pic-pin-modal-title');
            const submitBtn = modal.querySelector('[data-pic-pin-submit]');
            const selectWrap = modal.querySelector('[data-pic-pin-select-wrap]');
            const lockedWrap = modal.querySelector('[data-pic-pin-locked]');
            const lockedName = modal.querySelector('[data-pic-pin-locked-name]');
            const destinationInput = modal.querySelector('[data-pic-pin-destination]');

            const panel = modal.querySelector('[data-pic-pin-panel]');
            let ignoreBackdropUntil = 0;

            document.body.appendChild(modal);

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
                if (form) form.reset();
                if (hiddenId) hiddenId.value = '';
                if (select) select.value = '';
                if (destinationInput) destinationInput.value = '';
            }

            function openModal({ action, mode, technicianId, technicianName, destination }) {
                form.action = action;
                pinInput.value = '';
                if (destinationInput) {
                    destinationInput.value = destination || '';
                    if (destination) {
                        destinationInput.setAttribute('name', 'destination');
                    } else {
                        destinationInput.removeAttribute('name');
                    }
                }
                const locked = mode === 'identify' || mode === 'close';
                if (title) {
                    title.textContent = mode === 'close'
                        ? 'Close Ticket'
                        : (mode === 'identify' ? 'Verifikasi PIC' : 'Pilih PIC Teknisi');
                }
                if (submitBtn) {
                    submitBtn.textContent = mode === 'close' ? 'Verifikasi PIN' : 'Lanjutkan';
                }
                if (locked) {
                    copy.textContent = mode === 'close'
                        ? 'Masukkan PIN PIC ' + technicianName + ' yang mengambil tiket ini untuk Close ticket.'
                        : 'Masukkan PIN PIC ' + technicianName + ' untuk melanjutkan tiket ini.';
                    selectWrap?.classList.add('hidden');
                    lockedWrap?.classList.remove('hidden');
                    if (lockedName) lockedName.textContent = technicianName;
                    if (hiddenId) hiddenId.value = technicianId;
                    if (select) select.removeAttribute('name');
                    hiddenId?.setAttribute('name', 'technician_id');
                } else {
                    copy.textContent = mode === 'preventive'
                        ? 'Pilih PIC lalu masukkan PIN. Nama PIC akan terisi otomatis di form preventive.'
                        : 'Pilih PIC lalu masukkan PIN untuk mengambil tiket.';
                    selectWrap?.classList.remove('hidden');
                    lockedWrap?.classList.add('hidden');
                    if (select) {
                        select.value = '';
                        select.setAttribute('name', 'technician_id');
                    }
                    hiddenId?.removeAttribute('name');
                    if (hiddenId) hiddenId.value = '';
                }
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                ignoreBackdropUntil = Date.now() + 450;
                setTimeout(() => (select && !locked ? select.focus() : pinInput?.focus()), 50);
            }

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-pic-pin-open]');
                if (!trigger) return;
                event.preventDefault();
                openModal({
                    action: trigger.dataset.action,
                    mode: trigger.dataset.mode || 'claim',
                    technicianId: trigger.dataset.technicianId || '',
                    technicianName: trigger.dataset.technicianName || '',
                    destination: trigger.dataset.destination || '',
                });
            });

            document.addEventListener('change', (event) => {
                const trigger = event.target.closest('select[data-pic-pin-close]');
                if (!trigger || trigger.value !== 'close_by_technician') {
                    return;
                }
                openModal({
                    action: trigger.dataset.action,
                    mode: 'close',
                    technicianId: trigger.dataset.technicianId || '',
                    technicianName: trigger.dataset.technicianName || '',
                });
                trigger.value = 'on_progress';
            });

            modal.addEventListener('click', (event) => {
                if (event.target !== modal || Date.now() < ignoreBackdropUntil) {
                    return;
                }
                closeModal();
            });

            modal.querySelectorAll('button[data-pic-pin-cancel]').forEach((button) => {
                button.addEventListener('click', () => closeModal());
            });

            panel?.addEventListener('click', (event) => {
                event.stopPropagation();
            });

            form?.addEventListener('submit', () => {
                if (select?.getAttribute('name') === 'technician_id' && hiddenId) {
                    hiddenId.removeAttribute('name');
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
@endpush
