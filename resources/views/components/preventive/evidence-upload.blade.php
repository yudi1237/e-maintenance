@php $maxFiles = \App\Support\PreventiveEvidence::MAX_FILES; @endphp
<div data-evidence-uploader>
    <p class="em-label-light">Upload File Evidence</p>
    <p class="mb-2 text-xs text-slate-500">Foto kondisi mesin. Bisa lebih dari 1 (maks. {{ $maxFiles }} foto, masing-masing 5 MB). Setiap foto baru muncul di samping foto yang sudah dipilih.</p>
    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4" data-evidence-previews>
        <label
            for="preventive-evidences"
            class="flex min-h-28 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-2 py-3 text-center transition hover:border-emerald-300 hover:bg-emerald-50/50 dark:border-slate-600 dark:bg-slate-900/50 dark:hover:border-emerald-400 dark:hover:bg-emerald-500/10"
            data-evidence-add
        >
            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400" data-evidence-add-label>Tambah foto</span>
            <span class="mt-[2px] text-[10px] text-slate-400">JPG, PNG, WEBP</span>
        </label>
        <input
            id="preventive-evidences"
            name="evidences[]"
            type="file"
            accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
            multiple
            class="hidden"
            data-evidence-input
            data-max-files="{{ $maxFiles }}"
        >
    </div>
    <p class="mt-2 hidden text-sm font-medium text-emerald-700 dark:text-emerald-300" data-evidence-summary></p>
    <p class="mt-2 hidden text-sm font-medium text-amber-600 dark:text-amber-300" data-evidence-limit></p>
    <x-input-error :messages="$errors->get('evidences')" class="mt-2" />
    <x-input-error :messages="$errors->get('evidences.*')" class="mt-2" />
</div>

@once
    @push('scripts')
        <script>
            (() => {
                const root = document.querySelector('[data-evidence-uploader]');
                const input = root?.querySelector('[data-evidence-input]');
                const previews = root?.querySelector('[data-evidence-previews]');
                const addTile = root?.querySelector('[data-evidence-add]');
                const addLabel = root?.querySelector('[data-evidence-add-label]');
                const summary = root?.querySelector('[data-evidence-summary]');
                const limitNote = root?.querySelector('[data-evidence-limit]');
                if (!input || !previews || !addTile) {
                    return;
                }

                const maxFiles = Number(input.dataset.maxFiles || 10);
                const collected = [];

                function sameFile(a, b) {
                    return a.name === b.name && a.size === b.size && a.lastModified === b.lastModified;
                }

                function syncInput() {
                    const transfer = new DataTransfer();
                    collected.forEach((file) => transfer.items.add(file));
                    input.files = transfer.files;
                }

                function render() {
                    previews.querySelectorAll('[data-evidence-tile]').forEach((el) => el.remove());
                    collected.forEach((file, index) => {
                        const wrap = document.createElement('div');
                        wrap.setAttribute('data-evidence-tile', '');
                        wrap.className = 'relative overflow-hidden rounded-2xl border border-emerald-200 bg-white dark:border-emerald-500/30 dark:bg-slate-900';

                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.className = 'h-28 w-full object-cover';
                            img.alt = file.name;
                            img.src = URL.createObjectURL(file);
                            wrap.appendChild(img);
                        }

                        const badge = document.createElement('span');
                        badge.className = 'absolute left-1.5 top-1.5 inline-flex items-center gap-1 rounded-full bg-emerald-600 px-1.5 py-0.5 text-[10px] font-semibold text-white shadow';
                        badge.textContent = 'Berhasil';
                        wrap.appendChild(badge);

                        const remove = document.createElement('button');
                        remove.type = 'button';
                        remove.className = 'absolute right-1.5 top-1.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-900/70 text-white text-sm leading-none';
                        remove.setAttribute('aria-label', 'Hapus foto');
                        remove.textContent = '×';
                        remove.addEventListener('click', () => {
                            collected.splice(index, 1);
                            syncInput();
                            render();
                        });
                        wrap.appendChild(remove);

                        const name = document.createElement('p');
                        name.className = 'truncate px-2 py-1 text-[11px] text-slate-500';
                        name.textContent = file.name;
                        wrap.appendChild(name);

                        previews.insertBefore(wrap, addTile);
                    });

                    if (addLabel) {
                        addLabel.textContent = collected.length === 0 ? 'Tambah foto' : 'Tambah lagi';
                    }
                    addTile.classList.toggle('hidden', collected.length >= maxFiles);

                    if (summary) {
                        if (collected.length === 0) {
                            summary.classList.add('hidden');
                            summary.textContent = '';
                        } else {
                            summary.textContent = collected.length + ' foto siap diunggah';
                            summary.classList.remove('hidden');
                        }
                    }

                    if (limitNote) {
                        if (collected.length >= maxFiles) {
                            limitNote.textContent = 'Batas ' + maxFiles + ' foto sudah terpenuhi.';
                            limitNote.classList.remove('hidden');
                        } else {
                            limitNote.classList.add('hidden');
                        }
                    }
                }

                input.addEventListener('change', () => {
                    const incoming = Array.from(input.files || []);
                    let skipped = 0;
                    incoming.forEach((file) => {
                        if (collected.some((existing) => sameFile(existing, file))) {
                            return;
                        }
                        if (collected.length >= maxFiles) {
                            skipped += 1;
                            return;
                        }
                        collected.push(file);
                    });
                    syncInput();
                    render();
                    if (limitNote && skipped > 0) {
                        limitNote.textContent = 'Maksimal ' + maxFiles + ' foto. Foto tambahan tidak ditambahkan.';
                        limitNote.classList.remove('hidden');
                    }
                });
            })();
        </script>
    @endpush
@endonce
