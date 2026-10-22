@props([
    'recaps',
    'statusLabels' => [],
    'paginated' => false,
    'allowStatusChange' => true,
    'showActions' => false,
])

@php
    $statusLabels = $statusLabels !== [] ? $statusLabels : \App\Support\FindingRecapStatus::labels();
@endphp

<div x-data="findingRecapTable()">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Due Date</th>
                <th class="px-4 py-3">No Mesin</th>
                <th class="px-4 py-3">Nama Mesin</th>
                <th class="px-4 py-3">Nama Equipment</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Catatan</th>
                @if ($showActions)
                    <th class="px-4 py-3 text-right">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
            @forelse ($recaps as $recap)
                <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $recap->found_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">
                        {{ $recap->due_date->format('d/m/Y') }}
                        @if ($recap->is_overdue)
                            <span class="ml-1 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-red-700 dark:bg-red-500/20 dark:text-red-300">Overdue</span>
                        @elseif ($recap->is_reminder)
                            <span class="ml-1 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">Reminder</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap dark:text-slate-100">#{{ $recap->machine_number }}</td>
                    <td class="px-4 py-3 text-slate-700 max-w-[180px] truncate dark:text-slate-200" title="{{ $recap->machine_name_display }}">{{ $recap->machine_name_display }}</td>
                    <td class="px-4 py-3 text-slate-700 max-w-[200px] truncate dark:text-slate-200" title="{{ $recap->equipment_name }}">{{ $recap->equipment_name }}</td>
                    <td class="px-4 py-3">
                        @if ($allowStatusChange)
                            <select
                                class="em-input !mt-0 !min-h-0 !py-1.5 !text-xs min-w-[9.5rem] border font-semibold {{ $recap->status_badge_class }}"
                                style="color: {{ \App\Support\FindingRecapStatus::legendColor($recap->status) }};"
                                data-current-status="{{ $recap->status }}"
                                @change="openStatusChange($event, @js(route('admin.finding-recaps.update-status', $recap)), @js($recap->status_label))"
                            >
                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($recap->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            <span class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold {{ $recap->status_badge_class }}">
                                {{ $recap->status_label }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button
                            type="button"
                            class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium"
                            @click="openNotes(@js($recap->notesForView()), @js('#'.$recap->machine_number.' · '.$recap->equipment_name))"
                        >
                            Lihat catatan
                        </button>
                    </td>
                    @if ($showActions)
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center justify-end gap-3">
                                <a href="{{ route('admin.finding-recaps.edit', $recap) }}" class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.finding-recaps.destroy', $recap) }}" class="inline" onsubmit="return confirm('Hapus log temuan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500 text-xs font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showActions ? 8 : 7 }}" class="px-4 py-12 text-center text-slate-500">Belum ada log temuan.</td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>

    @if ($paginated && method_exists($recaps, 'hasPages') && $recaps->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $recaps->links() }}</div>
    @endif

    <template x-teleport="body">
        <div
            x-show="statusModal"
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto p-4 sm:p-6"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="cancelStatus()"
        >
            <div class="absolute inset-0 z-0 bg-slate-950/60 backdrop-blur-[4px]" @click="cancelStatus()"></div>
            <div class="relative z-10 my-auto w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl sm:p-8 dark:border dark:border-white/10 dark:bg-slate-900">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Catatan perubahan status</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Status akan diubah menjadi <span class="font-semibold text-slate-700 dark:text-slate-200" x-text="pendingStatusLabel"></span>. Isi catatan sebelum menyimpan.
                </p>
                <form method="POST" :action="statusAction" class="mt-5">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" :value="pendingStatus">
                    <label for="finding-status-note" class="em-label">Catatan</label>
                    <textarea
                        id="finding-status-note"
                        name="note"
                        rows="5"
                        x-model="statusNote"
                        class="em-input"
                        placeholder="Tulis catatan untuk status ini..."
                        required
                    ></textarea>
                    <p class="mt-2 hidden text-sm font-medium text-red-500" x-show="statusError" x-cloak>Catatan wajib diisi saat mengubah status.</p>
                    <div class="mt-6 flex gap-3">
                        <button type="button" class="em-btn-secondary w-1/2" @click="cancelStatus()">Batal</button>
                        <button type="submit" class="em-btn-primary w-1/2 justify-center" @click="if (!statusNote.trim()) { $event.preventDefault(); statusError = true; }">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div
            x-show="notesModal"
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto p-4 sm:p-6"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="notesModal = false"
        >
            <div class="absolute inset-0 z-0 bg-slate-950/60 backdrop-blur-[4px]" @click="notesModal = false"></div>
            <div class="relative z-10 my-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl sm:p-8 dark:border dark:border-white/10 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Catatan status</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400" x-text="notesTitle"></p>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-700 dark:hover:text-white" @click="notesModal = false" aria-label="Tutup">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="mt-5 max-h-[60vh] space-y-4 overflow-y-auto pr-1">
                    <template x-for="group in groupedNotes" :key="group.status">
                        <div class="rounded-xl border border-slate-200 p-4 dark:border-white/10">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400" x-text="group.label"></p>
                            <div class="mt-3 space-y-3">
                                <template x-for="(note, index) in group.notes" :key="index">
                                    <div class="rounded-lg bg-slate-50 px-3 py-2.5 dark:bg-white/5">
                                        <p class="whitespace-pre-wrap text-sm text-slate-800 dark:text-slate-100" x-text="note.note"></p>
                                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                            <span x-text="note.author"></span> · <span x-text="note.created_at"></span>
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <p class="text-sm text-slate-500" x-show="groupedNotes.length === 0">Belum ada catatan.</p>
                </div>
            </div>
        </div>
    </template>
</div>

@once
    @push('scripts')
        <script>
            window.findingRecapTable = function () {
                const statusOrder = @json(\App\Support\FindingRecapStatus::values());
                const statusLabels = @json($statusLabels);

                return {
                    statusModal: false,
                    notesModal: false,
                    statusAction: '',
                    pendingStatus: '',
                    pendingStatusLabel: '',
                    statusNote: '',
                    statusError: false,
                    pendingSelect: null,
                    currentStatus: '',
                    notesTitle: '',
                    viewingNotes: [],
                    get groupedNotes() {
                        return statusOrder
                            .map((status) => ({
                                status,
                                label: statusLabels[status] || status,
                                notes: this.viewingNotes.filter((note) => note.status === status),
                            }))
                            .filter((group) => group.notes.length > 0);
                    },
                    openStatusChange(event, action, currentLabel) {
                        const select = event.target;
                        if (select.value === select.dataset.currentStatus) {
                            return;
                        }
                        this.pendingSelect = select;
                        this.currentStatus = select.dataset.currentStatus;
                        this.statusAction = action;
                        this.pendingStatus = select.value;
                        this.pendingStatusLabel = statusLabels[select.value] || select.value;
                        this.statusNote = '';
                        this.statusError = false;
                        this.statusModal = true;
                    },
                    cancelStatus() {
                        if (this.pendingSelect) {
                            this.pendingSelect.value = this.currentStatus;
                        }
                        this.statusModal = false;
                        this.pendingSelect = null;
                    },
                    openNotes(notes, title) {
                        this.viewingNotes = notes || [];
                        this.notesTitle = title || '';
                        this.notesModal = true;
                    },
                };
            };
        </script>
    @endpush
@endonce
