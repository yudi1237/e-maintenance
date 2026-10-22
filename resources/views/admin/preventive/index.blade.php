<x-app-layout>
    @php
        $editingScheduleId = (int) old('editing_id');
        $scheduleStoreUrl = route('admin.preventive-schedules.store');
        $scheduleFormAction = $editingScheduleId
            ? route('admin.preventive-schedules.update', $editingScheduleId)
            : $scheduleStoreUrl;
        $emptyScheduleRow = [
            'machineNameId' => '',
            'machineName' => '',
            'machineType' => '',
            'planDate' => '',
            'preventiveType' => '',
            'notes' => '',
        ];
        $initialScheduleRows = [];
        if ($editingScheduleId) {
            $initialScheduleRows[] = [
                'machineNameId' => (string) old('machine_name_id', ''),
                'machineName' => $oldMachineName,
                'machineType' => $oldMachineType,
                'planDate' => old('plan_date', ''),
                'preventiveType' => old('preventive_type', ''),
                'notes' => old('notes', ''),
            ];
        } elseif (is_array(old('rows'))) {
            $machinesById = $machines->keyBy('id');
            foreach (old('rows') as $row) {
                $machine = $machinesById->get((int) ($row['machine_name_id'] ?? 0));
                $initialScheduleRows[] = [
                    'machineNameId' => (string) ($row['machine_name_id'] ?? ''),
                    'machineName' => $machine?->view_label ?? '',
                    'machineType' => $machine?->machine_type ?? '',
                    'planDate' => $row['plan_date'] ?? '',
                    'preventiveType' => $row['preventive_type'] ?? '',
                    'notes' => $row['notes'] ?? '',
                ];
            }
        }
        if ($initialScheduleRows === []) {
            $initialScheduleRows = [$emptyScheduleRow];
        }
        $scheduleMachines = $machines->map(fn ($machine) => [
            'id' => (string) $machine->id,
            'name' => $machine->view_label,
            'type' => $machine->machine_type ?? '',
        ])->values();
        $cellInput = 'em-input !mt-0 min-h-[40px] rounded-lg px-2 py-1.5 text-sm';
        $cellReadonly = $cellInput.' bg-slate-50 dark:bg-slate-950/40';
    @endphp
    <div
        class="em-shell py-8 space-y-8"
        x-data="{
            showPicker: false,
            showForm: {{ $errors->any() ? 'true' : 'false' }},
            isEditing: {{ $editingScheduleId ? 'true' : 'false' }},
            storeUrl: @js($scheduleStoreUrl),
            formAction: @js($scheduleFormAction),
            editingId: {{ $editingScheduleId ?: 'null' }},
            rowSeq: {{ count($initialScheduleRows) }},
            machines: @js($scheduleMachines),
            groups: @js($scheduleGroups),
            typesByGroup: @js($scheduleTypesByGroup),
            scheduleGroup: @js($selectedScheduleGroup ?: ''),
            rows: @js(collect($initialScheduleRows)->values()->map(fn ($row, $index) => $row + ['_id' => $index + 1])->all()),
            currentTypeList() {
                const map = this.typesByGroup[this.scheduleGroup] || {};
                return Object.entries(map).map(([value, label]) => ({ value, label }));
            },
            defaultType() {
                const types = this.currentTypeList();
                return types.length === 1 ? types[0].value : '';
            },
            emptyRow() {
                this.rowSeq += 1;
                return {
                    _id: this.rowSeq,
                    machineNameId: '',
                    machineName: '',
                    machineType: '',
                    planDate: '',
                    preventiveType: this.defaultType(),
                    notes: '',
                };
            },
            fieldName(field, index) {
                return this.isEditing ? field : `rows[${index}][${field}]`;
            },
            rowNumber(index) {
                return index + 1;
            },
            syncMachine(index) {
                const id = String(this.rows[index]?.machineNameId || '');
                const machine = this.machines.find((item) => String(item.id) === id);
                this.rows[index].machineName = machine?.name || '';
                this.rows[index].machineType = machine?.type || '';
            },
            addRow() {
                if (this.isEditing) {
                    return;
                }
                this.rows.push(this.emptyRow());
            },
            removeRow(index) {
                if (this.isEditing || this.rows.length <= 1) {
                    return;
                }
                this.rows.splice(index, 1);
            },
            resetToCreate() {
                this.isEditing = false;
                this.editingId = null;
                this.formAction = this.storeUrl;
                this.rowSeq = 0;
                this.rows = [this.emptyRow()];
            },
            selectGroup(group) {
                this.scheduleGroup = group;
                this.resetToCreate();
                this.showPicker = false;
                this.showForm = true;
            },
            changeGroup() {
                this.showForm = false;
                this.resetToCreate();
                this.scheduleGroup = '';
                this.showPicker = true;
            },
            toggleForm() {
                if (this.showForm || this.showPicker) {
                    this.resetToCreate();
                    this.scheduleGroup = '';
                    this.showForm = false;
                    this.showPicker = false;
                    return;
                }
                this.resetToCreate();
                this.showPicker = true;
            },
            cancelForm() {
                this.resetToCreate();
                this.scheduleGroup = '';
                this.showForm = false;
                this.showPicker = false;
            },
            fillEdit(schedule) {
                this.isEditing = true;
                this.editingId = schedule.id;
                this.formAction = schedule.updateUrl;
                this.scheduleGroup = schedule.scheduleGroup || '';
                this.rows = [{
                    _id: schedule.id,
                    machineNameId: String(schedule.machineNameId),
                    machineName: schedule.machineName,
                    machineType: schedule.machineType,
                    planDate: schedule.planDate,
                    preventiveType: schedule.preventiveType,
                    notes: schedule.notes || '',
                }];
                this.showPicker = false;
                this.showForm = true;
            }
        }"
    >
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Preventive Mesin</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $filteredReportCount }} hasil ditampilkan dari {{ $reportCount }} preventive tercatat
            </p>
            </div>
            <button type="button" class="em-btn-primary w-fit" @click="toggleForm()">Schedule Preventive</button>
        </div>

        <x-alert />

        <div class="em-glass rounded-2xl p-4 sm:p-6" x-show="showPicker" x-cloak>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Pilih Kategori Preventive</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Pilih kategori dulu. Injection berisi JSW EL, P1E, PM1, PM2, PM3, dan PM4. Kategori lain berdiri sendiri.</p>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($scheduleGroups as $group => $label)
                    <button
                        type="button"
                        class="rounded-2xl border border-slate-200 bg-white px-4 py-4 text-left shadow-sm transition hover:border-cyan-400 dark:border-white/10 dark:bg-slate-900/40"
                        @click="selectGroup(@js($group))"
                    >
                        <span class="block text-sm font-semibold text-slate-900 dark:text-white">
                            @if ($group === 'injection')
                                Kategori Injection
                            @else
                                {{ $label }}
                            @endif
                        </span>
                        <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">
                            @if ($group === 'injection')
                                JSW EL, P1E, PM1, PM2, PM3, PM4
                            @else
                                Berdiri sendiri
                            @endif
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="em-glass rounded-2xl p-4 sm:p-6" x-show="showForm" x-cloak>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="isEditing ? 'Edit Jadwal Preventive' : 'Form Schedule Preventive'"></h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        <span x-show="scheduleGroup === 'injection'">Kategori Injection. </span>
                        <span x-show="scheduleGroup && scheduleGroup !== 'injection'">Kategori <span x-text="groups[scheduleGroup]"></span>. </span>
                        <span x-text="isEditing ? 'Nama dan tipe mesin mengikuti nomer mesin yang dipilih.' : 'Setiap baris satu jadwal. Nama dan tipe mesin terisi otomatis dari nomer mesin.'"></span>
                    </p>
                </div>
                <button
                    type="button"
                    class="em-btn-secondary w-full justify-center sm:w-auto"
                    x-show="!isEditing"
                    @click="changeGroup()"
                >Ganti kategori</button>
            </div>

            <form method="POST" action="{{ $scheduleFormAction }}" :action="formAction" class="space-y-4">
                @csrf
                <template x-if="isEditing">
                    <div>
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="editing_id" :value="editingId">
                    </div>
                </template>
                <input type="hidden" name="create_schedule" value="1" x-bind:disabled="isEditing">
                <input type="hidden" name="schedule_group" :value="scheduleGroup">

                <div class="md:overflow-x-auto md:rounded-xl md:border md:border-slate-200 dark:md:border-white/10">
                    <table class="w-full min-w-0 border-separate border-spacing-0 text-sm md:min-w-[960px]">
                        <thead class="hidden md:table-header-group">
                            <tr class="bg-sky-200 text-center text-xs font-semibold text-slate-800 dark:bg-sky-900/70 dark:text-sky-100">
                                <th class="border-b border-sky-300 px-2 py-2.5 dark:border-sky-800">No</th>
                                <th class="border-b border-sky-300 px-2 py-2.5 dark:border-sky-800">Nomer Mesin</th>
                                <th class="border-b border-sky-300 px-2 py-2.5 dark:border-sky-800">Nama Mesin</th>
                                <th class="border-b border-sky-300 px-2 py-2.5 dark:border-sky-800">Tipe Mesin</th>
                                <th class="border-b border-sky-300 px-2 py-2.5 dark:border-sky-800">Plan Tanggal PM</th>
                                <th class="border-b border-sky-300 px-2 py-2.5 dark:border-sky-800">Tipe Preventive</th>
                                <th class="border-b border-sky-300 px-2 py-2.5 dark:border-sky-800">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="block space-y-4 md:table-row-group md:space-y-0">
                            <template x-for="(row, index) in rows" :key="row._id">
                                <tr class="block rounded-xl border border-slate-200 bg-white p-3 dark:border-white/10 dark:bg-slate-900/40 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0">
                                    <td class="block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10">
                                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">No</span>
                                        <div class="flex items-center justify-between gap-2 md:justify-center">
                                            <span class="inline-flex min-h-[40px] min-w-[2.5rem] items-center justify-center font-semibold text-slate-800 dark:text-slate-100" x-text="rowNumber(index)"></span>
                                            <button
                                                type="button"
                                                class="text-xs font-medium text-rose-600 md:hidden"
                                                x-show="!isEditing && rows.length > 1"
                                                @click="removeRow(index)"
                                            >Hapus baris</button>
                                        </div>
                                    </td>
                                    <td class="block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10">
                                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">Nomer Mesin</span>
                                        <select
                                            class="{{ $cellInput }}"
                                            :name="fieldName('machine_name_id', index)"
                                            x-model="row.machineNameId"
                                            @change="syncMachine(index)"
                                        >
                                            <option value="">Pilih nomer mesin</option>
                                            @foreach ($machines as $machine)
                                                <option value="{{ $machine->id }}">{{ $machine->machine_number }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10">
                                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">Nama Mesin</span>
                                        <input type="text" class="{{ $cellReadonly }}" x-model="row.machineName" placeholder="Otomatis" readonly>
                                    </td>
                                    <td class="block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10">
                                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">Tipe Mesin</span>
                                        <input type="text" class="{{ $cellReadonly }}" x-model="row.machineType" placeholder="Otomatis" readonly>
                                    </td>
                                    <td class="block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10">
                                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">Plan Tanggal PM</span>
                                        <input type="date" class="{{ $cellInput }}" :name="fieldName('plan_date', index)" x-model="row.planDate">
                                    </td>
                                    <td class="block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10">
                                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">Tipe Preventive</span>
                                        <select class="{{ $cellInput }}" :name="fieldName('preventive_type', index)" x-model="row.preventiveType">
                                            <option value="">Pilih</option>
                                            <template x-for="type in currentTypeList()" :key="scheduleGroup + '-' + type.value">
                                                <option :value="type.value" x-text="type.label"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="block px-0 py-2 md:table-cell md:border-b md:border-slate-200 md:px-2 md:py-2 dark:md:border-white/10">
                                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-500 md:hidden">Catatan</span>
                                        <div class="flex items-start gap-2">
                                            <input
                                                type="text"
                                                class="{{ $cellInput }}"
                                                :name="fieldName('notes', index)"
                                                x-model="row.notes"
                                                placeholder="Opsional"
                                                maxlength="2000"
                                            >
                                            <button
                                                type="button"
                                                class="hidden min-h-[40px] shrink-0 px-2 text-xs font-medium text-rose-600 md:inline-flex md:items-center"
                                                x-show="!isEditing && rows.length > 1"
                                                @click="removeRow(index)"
                                            >Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <button
                        type="button"
                        class="em-btn-secondary w-full justify-center sm:w-auto"
                        x-show="!isEditing"
                        @click="addRow()"
                    >+ Tambah Baris</button>
                    <span class="hidden sm:block" x-show="isEditing"></span>
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="button" class="em-btn-secondary w-full justify-center sm:w-auto" @click="cancelForm()">Batal</button>
                        <button type="submit" class="em-btn-primary w-full justify-center sm:w-auto" x-text="isEditing ? 'Perbarui Jadwal' : 'SIMPAN JADWAL'"></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="flex flex-col gap-4 px-6 py-5 border-b border-slate-200 dark:border-white/10 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Jadwal Preventive</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Rencana PM per mesin. Hasil check sheet tercatat di tabel bawah.</p>
                </div>
                <form method="GET" action="{{ route('admin.preventive.index') }}" class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-end">
                    @if ($startDate)
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                    @endif
                    @if ($endDate)
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                    @endif
                    @if ($machineNumber)
                        <input type="hidden" name="machine_number" value="{{ $machineNumber }}">
                    @endif
                    <div class="sm:min-w-[13rem]">
                        <label for="schedule_category" class="em-label">Filter kategori</label>
                        <select id="schedule_category" name="schedule_category" class="em-input">
                            <option value="">Semua kategori</option>
                            @foreach ($scheduleGroups as $group => $label)
                                <option value="{{ $group }}" @selected($scheduleCategory === $group)>
                                    {{ $group === 'injection' ? 'Injection' : $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:min-w-[11rem]">
                        <label for="schedule_month" class="em-label">Filter bulan</label>
                        <input id="schedule_month" name="schedule_month" type="month" class="em-input" value="{{ $scheduleMonth }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="em-btn-primary w-full justify-center sm:w-auto">Filter</button>
                        @if ($scheduleMonth || $scheduleCategory)
                            <a href="{{ route('admin.preventive.index', array_filter(['start_date' => $startDate, 'end_date' => $endDate, 'machine_number' => $machineNumber])) }}" class="em-btn-secondary w-full justify-center sm:w-auto">Semua</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nomer Mesin</th>
                            <th class="px-4 py-3">Nama Mesin</th>
                            <th class="px-4 py-3">Tipe Mesin</th>
                            <th class="px-4 py-3">Plan Tanggal PM</th>
                            <th class="px-4 py-3">Kategori Preventive</th>
                            <th class="px-4 py-3">Tipe Preventive</th>
                            <th class="px-4 py-3">Catatan</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($schedules as $schedule)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="em-schedule-no px-4 py-3 text-slate-700 dark:text-slate-200">{{ $schedules->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap dark:text-slate-100">{{ $schedule->machine_number }}</td>
                                <td class="px-4 py-3 text-slate-700 max-w-[180px] truncate dark:text-slate-200" title="{{ $schedule->machine_name_display }}">{{ $schedule->machine_name_display }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $schedule->machine_type ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">
                                    {{ $schedule->plan_date->format('d/m/Y') }}
                                    @if ($schedule->technicianStatus() === 'overdue')
                                        <span class="em-schedule-overdue em-status-badge em-status-badge-overdue ml-1 !px-2 !py-0.5 !text-[10px] uppercase tracking-wide">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $schedule->schedule_group_label }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $schedule->preventive_type_label }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[220px] truncate dark:text-slate-300" title="{{ $schedule->notes }}">{{ $schedule->notes ?: '—' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-3">
                                        <button
                                            type="button"
                                            class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium"
                                            @click="fillEdit(@js([
                                                'id' => $schedule->id,
                                                'updateUrl' => route('admin.preventive-schedules.update', $schedule),
                                                'machineNameId' => $schedule->machine_name_id,
                                                'machineName' => $schedule->machineName?->view_label ?? $schedule->machine_name,
                                                'machineType' => $schedule->machineName?->machine_type ?? $schedule->machine_type,
                                                'planDate' => $schedule->plan_date->toDateString(),
                                                'preventiveType' => $schedule->preventive_type,
                                                'scheduleGroup' => \App\Support\PreventiveCategory::scheduleGroupKey($schedule->preventive_type),
                                                'notes' => $schedule->notes ?? '',
                                            ]))"
                                        >
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.preventive-schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Hapus jadwal preventive ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-500 text-xs font-medium">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-slate-500">Tidak ada jadwal preventive yang belum dikerjakan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($schedules->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $schedules->links() }}</div>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.preventive.index') }}" class="em-glass rounded-2xl p-6">
            @if ($scheduleMonth)
                <input type="hidden" name="schedule_month" value="{{ $scheduleMonth }}">
            @endif
            @if ($scheduleCategory)
                <input type="hidden" name="schedule_category" value="{{ $scheduleCategory }}">
            @endif
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Filter Hasil Preventive</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tampilkan hasil berdasarkan mesin dan periode tanggal</p>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_1fr_1fr_auto_auto] md:items-end">
                <div>
                    <label for="machine_number" class="em-label">No Mesin</label>
                    <input id="machine_number" name="machine_number" type="number" min="1" class="em-input" value="{{ $machineNumber }}" placeholder="Semua">
                </div>
                <div>
                    <label for="start_date" class="em-label">Periode Mulai</label>
                    <input id="start_date" name="start_date" type="date" class="em-input" value="{{ $startDate }}">
                </div>
                <div>
                    <label for="end_date" class="em-label">Periode Akhir</label>
                    <input id="end_date" name="end_date" type="date" class="em-input" value="{{ $endDate }}">
                </div>
                <a href="{{ route('admin.preventive.index', array_filter(['schedule_month' => $scheduleMonth, 'schedule_category' => $scheduleCategory])) }}" class="em-btn-secondary w-full">Reset</a>
                <button type="submit" class="em-btn-primary w-full justify-center">Filter Hasil</button>
            </div>
            @error('end_date')
                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </form>

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Mesin</th>
                            <th class="px-4 py-3">Nama Mesin</th>
                            <th class="px-4 py-3">Kategori Preventive</th>
                            <th class="px-4 py-3">Tipe Preventive</th>
                            <th class="px-4 py-3">Shift</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Hasil</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Approval</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->work_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->machine_number }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->machine_name_display }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->schedule_group_label }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->category_label }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $report->work_shift_label }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $report->pic_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $report->result_badge_class }}">
                                        {{ $report->result_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <x-admin.preventive-status-form :report="$report" compact />
                                </td>
                                <td class="px-4 py-3">
                                    <x-admin.approval-progress :status="$report->status" variant="preventive" compact />
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.preventive.export', $report) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium">Download</a>
                                        <a href="{{ route('admin.preventive.show', $report) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium">Lihat →</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center text-slate-500">Belum ada hasil preventive.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($reports->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $reports->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
