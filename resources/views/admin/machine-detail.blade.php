<x-app-layout>
    <div class="em-shell py-8">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">← Kembali ke Dashboard</a>

        <x-alert />

        <div class="mt-4 mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Problem — Mesin {{ $machineNumber }}</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                    {{ $filteredReportCount }} laporan ditampilkan dari {{ $reportCount }} laporan tercatat
                </p>
            </div>
            @if (auth()->user()->seesFullDashboard())
            <div class="em-glass flex items-center gap-4 rounded-2xl p-3 sm:min-w-[280px]">
                <img src="{{ route('admin.machines.qr', $machineNumber) }}" alt="QR Mesin {{ $machineNumber }}" class="h-28 w-28 rounded-xl bg-white p-1 shadow-sm">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">QR Code Mesin {{ $machineNumber }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Scan tanpa login</p>
                    <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500 break-all">{{ \App\Support\MachineQrCode::url($machineNumber) }}</p>
                    <a href="{{ route('admin.machines.qr.download', $machineNumber) }}" class="em-qr-btn mt-3">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download QR
                    </a>
                </div>
            </div>
            @endif
        </div>

        @if (auth()->user()->isProduksiInjection())
            <form method="POST" action="{{ route('admin.machines.mold', $machineNumber) }}" class="em-glass mb-6 rounded-2xl p-6">
                @csrf
                <label for="mold_id" class="em-label">Mold terpasang</label>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pilih mold yang sedang dipasang di Mesin {{ $machineNumber }}. Nama mold akan tampil di kartu mesin pada dashboard.</p>
                <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <select id="mold_id" name="mold_id" class="em-input sm:max-w-xl" onchange="this.form.submit()">
                        <option value="">Belum ada mold</option>
                        @foreach ($molds as $mold)
                            <option value="{{ $mold->id }}" @selected((int) $machine?->mold_id === (int) $mold->id)>{{ $mold->optionLabel() }}</option>
                        @endforeach
                        @if ($machine?->mold && ! $molds->contains('id', $machine->mold_id))
                            <option value="{{ $machine->mold_id }}" selected>{{ $machine->mold->optionLabel() }}</option>
                        @endif
                    </select>
                </div>
                <x-input-error :messages="$errors->get('mold_id')" class="mt-2" />
            </form>
        @elseif ($machine?->mold)
            <div class="em-glass mb-6 rounded-2xl px-6 py-4 text-sm text-slate-700 dark:text-slate-200">
                Mold terpasang: <span class="font-semibold">{{ $machine->mold->optionLabel() }}</span>
            </div>
        @endif

        @include('partials.machine-specs', ['machine' => $machine, 'machineNumber' => $machineNumber])

        <form method="GET" action="{{ route('admin.machines.show', $machineNumber) }}" class="em-glass mb-6 rounded-2xl p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="rounded-xl bg-cyan-500/20 p-2.5">
                    <svg class="h-5 w-5 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Filter Riwayat Problem</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tampilkan riwayat problem Mesin {{ $machineNumber }} berdasarkan periode tanggal</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_1fr_1fr_2fr] md:items-end">
                <div>
                    <label for="start_date" class="em-label">Periode Mulai</label>
                    <input id="start_date" name="start_date" type="date" class="em-input" value="{{ $startDate }}">
                </div>
                <div>
                    <label for="end_date" class="em-label">Periode Akhir</label>
                    <input id="end_date" name="end_date" type="date" class="em-input" value="{{ $endDate }}">
                </div>
                <a href="{{ route('admin.machines.show', $machineNumber) }}" class="em-btn-secondary w-full">Reset</a>
                <button type="submit" class="em-btn-primary w-full justify-center">Filter Riwayat</button>
            </div>
            @error('end_date')
                <p class="mt-3 text-sm text-red-300">{{ $message }}</p>
            @enderror
        </form>

        <div class="em-glass rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Masalah</th>
                            <th class="px-4 py-3">Analisa Penyebab Kerusakan</th>
                            <th class="px-4 py-3">Kategori Penyebab Kerusakan</th>
                            <th class="px-4 py-3">Tindakan Perbaikan</th>
                            <th class="px-4 py-3">Penggunaan Sparepart</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($operatorTickets as $ticket)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $ticket->stop_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[220px] whitespace-pre-wrap break-words dark:text-slate-300">{{ $ticket->problem }}</td>
                                <td class="px-4 py-3 text-slate-400">—</td>
                                <td class="px-4 py-3 text-slate-400">—</td>
                                <td class="px-4 py-3 text-slate-400">—</td>
                                <td class="px-4 py-3 text-slate-400">—</td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">{{ $ticket->pic_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold {{ $ticket->status_badge_class }}">{{ $ticket->status_label }}</span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.operator-reports.show', $ticket) }}" class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium">Detail →</a>
                                </td>
                            </tr>
                        @endforeach
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $report->report_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[220px] whitespace-pre-wrap break-words dark:text-slate-300">{{ $report->masalah ?: ($report->operatorReport?->problem ?: '—') }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[220px] whitespace-pre-wrap break-words dark:text-slate-300">{{ $report->analisa_penyebab_kerusakan ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[180px] whitespace-pre-wrap break-words dark:text-slate-300">{{ $report->damage_cause_category_label }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[220px] whitespace-pre-wrap break-words dark:text-slate-300">{{ $report->tindakan_perbaikan ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-[180px] whitespace-pre-wrap break-words dark:text-slate-300">{{ $report->penggunaan_sparepart ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">{{ $report->pic_name }}</td>
                                <td class="px-4 py-3">
                                    <x-admin.report-status-form :report="$report" :status-labels="$statusLabels" compact />
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium">Detail →</a>
                                        @if (auth()->user()->isAdmin())
                                            <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" class="inline" onsubmit="return confirm('Hapus riwayat problem ini beserta tiket OP-nya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-500 text-xs font-medium">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            @if ($operatorTickets->isEmpty())
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-slate-500">Tidak ada laporan untuk periode yang dipilih.</td>
                                </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($reports->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $reports->links() }}</div>
            @endif
        </div>

        <div class="mt-8 em-glass rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-white/10">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Preventive Mesin {{ $machineNumber }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ $filteredPreventiveCount }} hasil ditampilkan dari {{ $preventiveCount }} preventive tercatat untuk mesin ini
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Shift</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Hasil</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Approval</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($preventiveReports as $report)
                            <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $report->work_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $report->category_label }}</span>
                                </td>
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
                                        <a href="{{ route('admin.preventive.export', $report) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium">Download PDF</a>
                                        <a href="{{ route('admin.preventive.show', $report) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs font-medium">Detail →</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">Belum ada hasil preventive untuk mesin ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($preventiveReports->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $preventiveReports->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
