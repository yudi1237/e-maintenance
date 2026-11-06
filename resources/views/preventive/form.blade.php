<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900 em-page-hero">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-400 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative w-full">
                @if ($editingReport ?? null)
                    <a href="{{ route('home.submissions.preventive', $editingReport) }}" class="inline-flex items-center gap-1 text-sm text-emerald-200/90 hover:text-white">← Kembali ke detail</a>
                @else
                <a href="{{ $fromSchedule ? route('preventive.menu') : (\App\Support\PreventiveCategory::isUtility($category) ? route('preventive.create', ['category' => $category]) : route('preventive.categories', $machine->machine_number)) }}" class="inline-flex items-center gap-1 text-sm text-emerald-200/90 hover:text-white">
                    ← {{ $fromSchedule ? 'Kembali ke jadwal' : (\App\Support\PreventiveCategory::isUtility($category) ? 'Kembali ke pilih mesin' : 'Kembali ke kategori') }}
                </a>
                @endif
                <p class="mt-4 text-emerald-300/90 text-sm font-medium tracking-wide uppercase">PT Dharma Poliplast</p>
                <h1 class="mt-1 text-xl sm:text-3xl font-bold text-white leading-tight">
                    {{ ($editingReport ?? null) ? 'Edit ' : '' }}{{ \App\Support\PreventiveCategory::formTitle($category) }}
                </h1>
                <p class="mt-2 text-slate-300 text-sm max-w-lg">Mesin {{ $machine->machine_number }} — {{ $machine->view_label }}. Tanggal & PIC terisi otomatis.</p>

                <div class="mt-6 em-chip-row">
                    <div class="em-chip bg-white/10 backdrop-blur border border-white/10 text-white">
                        <span class="text-slate-300">Tanggal:</span>
                        <strong>{{ $currentDateTime }}</strong>
                    </div>
                    <div class="em-chip bg-white/10 backdrop-blur border border-white/10 text-white">
                        <span class="text-slate-300">PIC:</span>
                        <strong>{{ $picName }}</strong>
                    </div>
                    <div class="em-chip bg-white/10 backdrop-blur border border-white/10 text-white">
                        <span class="text-slate-300">Kategori:</span>
                        <strong>{{ $category }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="em-page-body" style="max-width:none;width:100%;">
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

            @php
                $editingReport = $editingReport ?? null;
                $savedItems = $editingReport?->items ?? [];
                $savedReadings = $editingReport?->readings ?? [];
                $savedRemarks = $editingReport?->item_remarks ?? [];
                $savedTeamIds = collect($editingReport?->team_members ?? [])->pluck('id')->all();
                $sparepartChoice = $editingReport ? (filled($editingReport->sparepart_items) ? 'sparepart' : 'none') : null;
                $sparepartItems = $editingReport?->sparepart_items;
            @endphp
            <form method="POST" action="{{ $editingReport ? route('home.submissions.preventive.update', $editingReport) : route('preventive.store') }}" class="space-y-5" enctype="multipart/form-data" data-confirm-preview>
                @csrf
                @if ($editingReport)
                    @method('PUT')
                @endif
                <input type="hidden" name="machine_name_id" value="{{ $machine->id }}">
                <input type="hidden" name="category" value="{{ $category }}">
                @if ($fromSchedule)
                    <input type="hidden" name="from" value="schedule">
                @endif

                <x-preventive.filling-guide />

                @if (\App\Support\PreventiveCategory::isUtility($category))
                    @php
                        $numberLabel = \App\Support\PreventiveCategory::numberLabel($category);
                        $categoryLabel = \App\Support\PreventiveCategory::label($category);
                    @endphp
                    <div class="em-form-section">
                        <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                            <div>
                                <h2 class="text-base font-semibold text-slate-800 dark:text-white">{{ $numberLabel }}</h2>
                                <p class="text-xs text-slate-500">Mesin {{ $machine->machine_number }} — {{ $machine->view_label }} · {{ \App\Support\PreventiveCategory::formTitle($category) }}</p>
                            </div>
                        </div>
                        @if ($category === 'ROBOT')
                            <div>
                                <label for="robot_id" class="em-label-light">{{ $numberLabel }} *</label>
                                <select id="robot_id" name="robot_id" class="em-input-light" required>
                                    <option value="">Pilih nomor {{ $categoryLabel }}</option>
                                    @forelse ($robots as $robot)
                                        <option value="{{ $robot->id }}" @selected((string) old('robot_id', $editingReport?->robot_id) === (string) $robot->id)>{{ $robot->label }}</option>
                                    @empty
                                        <option value="" disabled>Belum ada master data {{ $categoryLabel }}</option>
                                    @endforelse
                                </select>
                                <p class="mt-1 text-xs text-slate-500">Daftar diambil dari menu Master Data {{ $categoryLabel }}.</p>
                            </div>
                        @elseif (\App\Support\EquipmentCategory::usesMasterDropdown($category))
                            <div>
                                <label for="equipment_id" class="em-label-light">{{ $numberLabel }} *</label>
                                <select id="equipment_id" name="equipment_id" class="em-input-light" required>
                                    <option value="">Pilih nomor {{ $categoryLabel }}</option>
                                    @forelse ($equipments as $equipment)
                                        <option value="{{ $equipment->id }}" @selected((string) old('equipment_id', $editingReport?->equipment_id) === (string) $equipment->id)>{{ $equipment->label }}</option>
                                    @empty
                                        <option value="" disabled>Belum ada master data {{ $categoryLabel }}</option>
                                    @endforelse
                                </select>
                                <p class="mt-1 text-xs text-slate-500">Daftar diambil dari menu Master Data {{ $categoryLabel }}.</p>
                            </div>
                        @else
                            <div>
                                <label for="equipment_number" class="em-label-light">{{ $numberLabel }} *</label>
                                <input
                                    id="equipment_number"
                                    type="text"
                                    name="equipment_number"
                                    value="{{ old('equipment_number', $editingReport?->equipment_number) }}"
                                    maxlength="50"
                                    required
                                    class="em-input-light"
                                    placeholder="Contoh: HR-01"
                                >
                            </div>
                        @endif
                    </div>
                @endif

                @include('preventive.partials.team-members')

                <div class="em-form-section">
                    <div class="em-section-header mb-5 pb-4 border-b border-slate-100 dark:border-white/10">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Shift Kerja</h2>
                            <p class="text-xs text-slate-500">Mesin {{ $machine->machine_number }} — {{ $machine->view_label }} · {{ \App\Support\PreventiveCategory::formTitle($category) }}</p>
                        </div>
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

                <div class="space-y-5">
                    @foreach ($checklistGroups as $group)
                        @if (($group['layout'] ?? '') === 'voltage')
                            @include('preventive.partials.voltage-readings', ['group' => $group])
                            @continue
                        @endif
                        @php
                            $groupHasInput = (bool) ($group['has_input'] ?? false);
                            $columnCount = 4 + count($group['answers']) + ($groupHasInput ? 1 : 0);
                        @endphp
                        <div class="em-form-section overflow-hidden !p-0">
                            <div class="px-4 py-3 border-b border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                                <h2 class="text-base font-semibold text-emerald-900 dark:text-emerald-200">{{ $group['label'] }}</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm" style="min-width:40rem;">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-400">
                                            <th class="px-3 py-2 w-12">No</th>
                                            <th class="px-3 py-2">Item Pemeriksaan</th>
                                            <th class="px-3 py-2">Standar</th>
                                            @if ($groupHasInput)
                                                <th class="px-3 py-2 w-36">Isi Manual</th>
                                            @endif
                                            @foreach ($group['answers'] as $valueLabel)
                                                <th class="px-3 py-2 text-center w-28">{{ $valueLabel }}</th>
                                            @endforeach
                                            <th class="px-3 py-2" style="min-width:12rem;">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                        @foreach ($group['items'] as $key => $item)
                                            @php $hasInput = ! empty($item['input']); @endphp
                                            <tr @class(['align-top', 'bg-yellow-100 dark:bg-yellow-500/15' => $hasInput])>
                                                <td class="px-3 py-3 text-slate-500 tabular-nums">{{ $loop->iteration }}</td>
                                                <td class="px-3 py-3 font-medium text-slate-800 dark:text-white">{{ $item['label'] }}</td>
                                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">{{ $item['standard'] !== '' ? $item['standard'] : '—' }}</td>
                                                @if ($groupHasInput)
                                                    <td class="px-3 py-3">
                                                        @if ($hasInput)
                                                            <div class="flex items-center gap-1">
                                                                <input
                                                                    type="text"
                                                                    name="readings[{{ $key }}]"
                                                                    value="{{ old('readings.'.$key, $savedReadings[$key] ?? '') }}"
                                                                    placeholder="{{ $item['input']['placeholder'] }}"
                                                                    maxlength="50"
                                                                    required
                                                                    class="em-input-light py-1.5 text-sm"
                                                                >
                                                                @if (($item['input']['suffix'] ?? '') !== '')
                                                                    <span class="shrink-0 text-xs font-medium text-slate-500">{{ $item['input']['suffix'] }}</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-slate-400">—</span>
                                                        @endif
                                                    </td>
                                                @endif
                                                @foreach ($group['answers'] as $value => $valueLabel)
                                                    <td class="px-3 py-3 text-center">
                                                        <label class="inline-flex cursor-pointer items-center justify-center">
                                                            <span class="sr-only">{{ $valueLabel }} — {{ $item['label'] }}</span>
                                                            <input
                                                                type="radio"
                                                                name="items[{{ $key }}]"
                                                                value="{{ $value }}"
                                                                class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-500 dark:bg-slate-900"
                                                                @checked(old('items.'.$key, $savedItems[$key] ?? null) === $value)
                                                            >
                                                        </label>
                                                    </td>
                                                @endforeach
                                                <td class="px-3 py-3" style="min-width:12rem;">
                                                    <input
                                                        type="text"
                                                        name="remarks[{{ $key }}]"
                                                        value="{{ old('remarks.'.$key, $savedRemarks[$key] ?? '') }}"
                                                        maxlength="500"
                                                        placeholder="Catatan"
                                                        class="em-input-light py-1.5 text-sm"
                                                    >
                                                    @error('remarks.'.$key)
                                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                            </tr>
                                            @error('items.'.$key)
                                                <tr>
                                                    <td colspan="{{ $columnCount }}" class="px-3 pb-3 pt-0">
                                                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                    </td>
                                                </tr>
                                            @enderror
                                            @error('readings.'.$key)
                                                <tr>
                                                    <td colspan="{{ $columnCount }}" class="px-3 pb-3 pt-0">
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

                <div class="em-form-section">
                    <x-preventive.sparepart-usage :choice="$sparepartChoice" :items="$sparepartItems" />
                </div>

                <div class="em-form-section space-y-5">
                    @if ($editingReport && $editingReport->evidence_urls !== [])
                        <div>
                            <p class="em-label-light">Evidence tersimpan</p>
                            <ul class="space-y-1">
                                @foreach ($editingReport->evidence_urls as $url)
                                    <li><a href="{{ $url }}" target="_blank" class="text-sm text-emerald-700 hover:underline dark:text-emerald-300">Lihat foto →</a></li>
                                @endforeach
                            </ul>
                            <p class="mt-1 text-xs text-slate-500">Foto baru ditambahkan ke evidence yang sudah ada.</p>
                        </div>
                    @endif
                    <x-preventive.evidence-upload />
                    <div>
                        <label for="notes" class="em-label-light">Catatan (opsional)</label>
                        <textarea id="notes" name="notes" rows="4" class="em-input-light" placeholder="Temuan, sparepart, atau tindak lanjut...">{{ old('notes', $editingReport?->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-2 sticky bottom-0 z-20 em-form-sticky backdrop-blur py-3 -mx-3 px-3 sm:static sm:z-auto sm:bg-transparent sm:p-0 sm:mx-0 border-t sm:border-0">
                    <button type="submit" class="em-btn-primary sm:min-w-[220px]">
                        {{ $editingReport ? 'Simpan Perubahan' : 'Simpan Preventive' }}
                    </button>
                </div>
            </form>

            <x-submit-preview />

            @if (! $editingReport && $recentReports->isNotEmpty())
                <div class="mt-8 em-form-section">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white mb-4">Preventive {{ $category }} terakhir Anda</h2>
                    <div class="space-y-3">
                        @foreach ($recentReports as $report)
                            <div class="flex flex-col gap-1 rounded-xl border border-slate-200 px-4 py-3 text-sm dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-white">Mesin {{ $report->machine_number }} — {{ $report->machine_name_display }}</p>
                                    <p class="text-xs text-slate-500">{{ $report->work_date->format('d/m/Y') }} · {{ $report->work_shift_label }} · {{ $report->category_label }}</p>
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
