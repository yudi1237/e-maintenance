@props(['choice' => null, 'items' => null])
@php
    $choiceValue = old('penggunaan_sparepart_choice', $choice);
    $oldItems = old('sparepart_items', $items);
    if (! is_array($oldItems) || count($oldItems) === 0) {
        $oldItems = [['name' => '', 'qty' => '']];
    }
@endphp
<div>
    <label for="penggunaan_sparepart_choice" class="em-label-light">Pemakaian Sparepart *</label>
    <select id="penggunaan_sparepart_choice" name="penggunaan_sparepart_choice" class="em-input-light" required>
        <option value="">— Pilih —</option>
        <option value="sparepart" @selected($choiceValue === 'sparepart')>Sparepart</option>
        <option value="none" @selected($choiceValue === 'none')>Tidak ada</option>
    </select>
    <x-input-error :messages="$errors->get('penggunaan_sparepart_choice')" class="mt-2" />
    <x-input-error :messages="$errors->get('sparepart_items')" class="mt-2" />

    <div id="sparepart-items-wrap" class="mt-4 space-y-3 {{ $choiceValue === 'sparepart' ? '' : 'hidden' }}">
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

@push('scripts')
    <script>
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
