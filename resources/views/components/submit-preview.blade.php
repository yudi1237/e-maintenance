<div
    id="submit-preview-modal"
    class="hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="submit-preview-title"
    style="position:fixed;inset:0;z-index:2147483646;background:rgba(15,23,42,.6);"
>
    <div style="box-sizing:border-box;height:100%;display:flex;align-items:center;justify-content:center;padding:16px;">
        <div id="submit-preview-panel" style="box-sizing:border-box;display:flex;flex-direction:column;width:min(42rem,100%);height:min(90vh,100%);border-radius:16px;overflow:hidden;box-shadow:0 25px 50px rgba(15,23,42,.25);">
            <div style="flex:0 0 auto;padding:16px 20px;border-bottom:1px solid #e2e8f0;">
                <h2 id="submit-preview-title" style="margin:0;font-size:1.125rem;font-weight:700;">Preview Pengisian</h2>
                <p style="margin:4px 0 0;font-size:.875rem;color:#64748b;">Periksa data di bawah, lalu tekan Submit.</p>
            </div>
            <div id="submit-preview-body" style="flex:1 1 auto;min-height:0;overflow-y:auto;padding:8px 20px 16px;"></div>
            <div style="flex:0 0 auto;display:flex;gap:8px;justify-content:flex-end;padding:12px 20px;border-top:1px solid #e2e8f0;">
                <button type="button" id="submit-preview-back" class="em-btn-secondary" style="justify-content:center;">Kembali</button>
                <button type="button" id="submit-preview-ok" class="em-btn-primary" style="justify-content:center;min-width:140px;">Submit</button>
            </div>
        </div>
    </div>
</div>
<style>
    #submit-preview-panel,
    #submit-preview-panel > div:first-child,
    #submit-preview-panel > div:last-child {
        background: #fff;
        color: #0f172a;
    }
    html.dark #submit-preview-panel,
    html.dark #submit-preview-panel > div:first-child,
    html.dark #submit-preview-panel > div:last-child {
        background: #0f172a;
        color: #f8fafc;
    }
</style>

@push('scripts')
    <script>
        (function () {
            if (window.__submitPreviewBound) return;
            window.__submitPreviewBound = true;

            const modal = document.getElementById('submit-preview-modal');
            const body = document.getElementById('submit-preview-body');
            const okButton = document.getElementById('submit-preview-ok');
            const backButton = document.getElementById('submit-preview-back');
            let activeForm = null;

            function clean(text) {
                return (text || '').replace(/\s+/g, ' ').replace(/\*/g, '').trim();
            }

            function isHidden(el) {
                return Boolean(el.closest('.hidden'));
            }

            function labelFor(el) {
                if (el.id) {
                    const byFor = el.form.querySelector('label[for="' + CSS.escape(el.id) + '"]');
                    if (byFor) return clean(byFor.textContent);
                }
                const wrapping = el.closest('label');
                if (wrapping) {
                    const copy = wrapping.cloneNode(true);
                    copy.querySelectorAll('input, select, textarea').forEach((node) => node.remove());
                    const text = clean(copy.textContent);
                    if (text) return text;
                }
                const block = el.closest('div');
                const label = block ? block.querySelector('label') : null;
                if (label && !label.classList.contains('sr-only')) return clean(label.textContent);
                return clean(el.getAttribute('placeholder') || el.name || 'Isian');
            }

            function rowName(el) {
                const cells = el.closest('tr') ? el.closest('tr').querySelectorAll('td') : [];
                return cells.length > 1 ? clean(cells[1].textContent) : '';
            }

            function radioValue(checked) {
                if (!checked) return 'Belum dipilih';
                const sr = checked.parentElement ? checked.parentElement.querySelector('.sr-only') : null;
                if (sr) {
                    const part = clean(sr.textContent.split('—')[0]);
                    if (part) return part;
                }
                return checked.value || 'Dipilih';
            }

            function addRow(list, label, value) {
                const row = document.createElement('div');
                row.className = 'grid gap-1 border-b border-slate-100 py-2 last:border-0 sm:grid-cols-[minmax(9rem,14rem)_1fr] dark:border-white/5';
                const dt = document.createElement('p');
                dt.className = 'text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400';
                dt.textContent = label;
                const dd = document.createElement('p');
                dd.className = 'whitespace-pre-wrap text-sm text-slate-800 dark:text-slate-100';
                dd.textContent = value;
                row.appendChild(dt);
                row.appendChild(dd);
                list.appendChild(row);
            }

            function sectionRows(section) {
                const rows = [];
                const seenRadio = new Set();

                section.querySelectorAll('input[type="radio"]').forEach((radio) => {
                    if (isHidden(radio) || seenRadio.has(radio.name)) return;
                    seenRadio.add(radio.name);
                    const group = Array.from(section.querySelectorAll('input[type="radio"]')).filter((item) => item.name === radio.name);
                    const checked = group.find((item) => item.checked) || null;
                    rows.push([rowName(radio) || labelFor(radio), radioValue(checked)]);
                });

                const checks = Array.from(section.querySelectorAll('input[type="checkbox"]')).filter((el) => !isHidden(el));
                if (checks.length) {
                    const picked = checks
                        .filter((el) => el.checked)
                        .map((el) => rowName(el) || labelFor(el) || el.value)
                        .filter(Boolean);
                    rows.push(['Yang dipilih', picked.length ? picked.join(', ') : 'Tidak ada']);
                }

                section.querySelectorAll('input, select, textarea').forEach((el) => {
                    if (isHidden(el) || el.disabled) return;
                    if (el.type === 'hidden' || el.type === 'radio' || el.type === 'checkbox' || el.type === 'submit' || el.type === 'button') return;
                    if (el.name === '_token') return;

                    if (el.type === 'password') {
                        rows.push([labelFor(el), el.value ? 'Sudah diisi' : 'Belum diisi']);
                        return;
                    }

                    if (el.type === 'file') {
                        const names = Array.from(el.files || []).map((file) => file.name).join(', ');
                        rows.push([labelFor(el) || 'File', names || 'Tidak ada file']);
                        return;
                    }

                    if (el.name && el.name.indexOf('readings[') === 0) {
                        const span = el.closest('label') ? el.closest('label').querySelector('span') : null;
                        const label = clean(span ? span.textContent : '') || rowName(el) || 'Pembacaan';
                        if (el.value.trim()) rows.push([label, el.value.trim()]);
                        return;
                    }

                    if (el.name && el.name.indexOf('sparepart_items[') === 0) {
                        return;
                    }

                    const value = el.tagName === 'SELECT'
                        ? (el.value ? clean(el.options[el.selectedIndex]?.textContent || '') : '')
                        : el.value.trim();
                    if (!value) return;
                    rows.push([labelFor(el), value]);
                });

                const sparepartRows = Array.from(section.querySelectorAll('.sparepart-row')).filter((row) => !isHidden(row));
                sparepartRows.forEach((row, index) => {
                    const name = row.querySelector('.sparepart-name')?.value.trim() || '';
                    const qty = row.querySelector('.sparepart-qty')?.value.trim() || '';
                    if (!name && !qty) return;
                    rows.push(['Sparepart ' + (index + 1), [name || '—', qty ? 'qty ' + qty : ''].filter(Boolean).join(' · ')]);
                });

                return rows;
            }

            function render(form) {
                body.replaceChildren();
                const sections = form.querySelectorAll('.em-form-section');
                let shown = 0;
                sections.forEach((section) => {
                    const rows = sectionRows(section);
                    if (!rows.length) return;
                    shown += 1;
                    const block = document.createElement('section');
                    const title = document.createElement('h3');
                    title.className = 'mb-1 text-sm font-semibold text-cyan-700 dark:text-cyan-300';
                    title.textContent = clean(section.querySelector('h2, h3, th')?.textContent || 'Pengisian');
                    const list = document.createElement('div');
                    rows.forEach((row) => addRow(list, row[0], row[1]));
                    block.appendChild(title);
                    block.appendChild(list);
                    body.appendChild(block);
                });
                if (!shown) {
                    const empty = document.createElement('p');
                    empty.className = 'text-sm text-slate-500';
                    empty.textContent = 'Belum ada isian yang bisa ditampilkan.';
                    body.appendChild(empty);
                }
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                activeForm = null;
                okButton.disabled = false;
                okButton.textContent = 'Submit';
            }

            function openModal(form) {
                activeForm = form;
                render(form);
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                okButton.focus();
            }

            document.querySelectorAll('form[data-confirm-preview]').forEach((form) => {
                form.addEventListener('submit', function (event) {
                    if (form.dataset.previewConfirmed === '1') return;
                    event.preventDefault();
                    if (typeof form.reportValidity === 'function' && !form.reportValidity()) return;
                    openModal(form);
                });
            });

            okButton.addEventListener('click', function () {
                if (!activeForm) return;
                const form = activeForm;
                form.dataset.previewConfirmed = '1';
                okButton.disabled = true;
                okButton.textContent = 'Mengirim...';
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            });

            backButton.addEventListener('click', closeModal);
            modal.addEventListener('click', function (event) {
                if (event.target === modal || event.target === modal.firstElementChild) closeModal();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
        })();
    </script>
@endpush
