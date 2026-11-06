<x-app-layout>
    <div class="em-shell py-8">
            <div class="em-glass rounded-2xl overflow-hidden">
                <div class="px-4 py-4 sm:px-6 border-b border-slate-200 dark:border-white/10">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Laporan OP Mesin</h2>
                            @if (auth()->user()->seesDashboardOverview())
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    {{ auth()->user()->seesAdminDashboard() ? 'Filter daftar tiket di bawah, atau Export Laporan Excel sesuai periode dan mesin.' : 'Filter daftar tiket untuk memantau status mesin dan laporan.' }}
                                </p>
                            @endif
                        </div>
                        <form method="GET" action="{{ route('admin.report.op-mesin') }}" class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-5 xl:w-auto xl:max-w-4xl">
                                <div>
                                    <label for="export_start_date" class="em-label">Periode Mulai</label>
                                    <input id="export_start_date" name="start_date" type="date" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ old('start_date', $exportStartDate) }}">
                                </div>
                                <div>
                                    <label for="export_end_date" class="em-label">Periode Akhir</label>
                                    <input id="export_end_date" name="end_date" type="date" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm" value="{{ old('end_date', $exportEndDate) }}">
                                </div>
                                <div>
                                    <label for="export_machine_number" class="em-label">Filter Mesin</label>
                                    <select id="export_machine_number" name="machine_number" class="em-input !mt-1 min-h-[40px] px-3 py-2 text-sm">
                                        <option value="">Semua Mesin</option>
                                        @foreach ($machineNumbers as $num)
                                            <option value="{{ $num }}" @selected((string) old('machine_number', $ticketMachineNumber) === (string) $num)>Mesin {{ $num }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="em-btn-secondary w-full min-h-[40px] justify-center px-3">Filter</button>
                                </div>
                                @if (auth()->user()->seesAdminDashboard())
                                <div class="flex items-end">
                                    <button type="submit" formaction="{{ route('admin.reports.export') }}" class="em-qr-btn w-full min-h-[40px] justify-center px-3">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Download Excel
                                    </button>
                                </div>
                                @endif
                            </form>
                    </div>
                    @if (auth()->user()->seesAdminDashboard() && $errors->has('export'))
                        <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200">
                            {{ $errors->first('export') }}
                        </div>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                                <th class="px-4 py-3">Stop</th>
                                <th class="px-4 py-3">Tiket OP</th>
                                <th class="px-4 py-3">Mesin</th>
                                <th class="px-4 py-3">Nama Part</th>
                                <th class="px-4 py-3">Masalah</th>
                                <th class="px-4 py-3">Operator</th>
                                <th class="px-4 py-3">Teknisi</th>
                                <th class="px-4 py-3">Form 2</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Approval</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse ($tickets as $ticket)
                                @php
                                    $report = $ticket->maintenanceReport;
                                @endphp
                                <tr class="hover:bg-slate-50 transition dark:hover:bg-white/5">
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">{{ $ticket->stop_date_time_label }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap dark:text-slate-100">#{{ $ticket->id }}</td>
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap dark:text-slate-200">
                                        #{{ $ticket->machine_number }}
                                        <span class="block text-xs text-slate-500 max-w-[160px] truncate">{{ $ticket->machine_name_display }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 max-w-[180px] truncate dark:text-slate-300" title="{{ $ticket->item_name }}">{{ $ticket->item_name }}</td>
                                    <td class="px-4 py-3 text-slate-600 max-w-[220px] truncate dark:text-slate-300" title="{{ $ticket->problem }}">{{ $ticket->problem }}</td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap dark:text-slate-300">
                                        {{ $ticket->operator_name }}
                                        @if (filled($ticket->npk))
                                            <span class="block text-xs text-slate-500">NPK {{ $ticket->npk }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 max-w-[220px] dark:text-slate-300">
                                        @php
                                            $technicianLabel = $report?->isStep2Completed()
                                                ? $report->dashboard_technician_label
                                                : '';
                                        @endphp
                                        @if ($technicianLabel !== '')
                                            {{ $technicianLabel }}
                                        @elseif ($ticket->pic_name !== 'Belum diambil')
                                            {{ $ticket->pic_name }}
                                        @else
                                            <span class="text-slate-400">Belum diambil</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($report?->isStep2Completed())
                                            <span class="text-emerald-400 text-xs">✓ Lengkap</span>
                                        @else
                                            <span class="text-amber-400 text-xs">Belum</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($report)
                                            <x-admin.report-status-form :report="$report" :status-labels="$statusLabels" compact />
                                        @else
                                            <span class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold {{ $ticket->status_badge_class }}" style="color: {{ \App\Support\RepairStatus::legendColor($ticket->status ?? \App\Support\RepairStatus::OPEN) }};">
                                                {{ $ticket->status_label }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.approval-progress :status="$report->status_perbaikan ?? $ticket->status" :report="$report" compact />
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.operator-reports.show', $ticket) }}" class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 text-xs font-medium">Detail →</a>
                                            @if (auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('admin.operator-reports.destroy', $ticket) }}" class="inline" onsubmit="return confirm('Hapus tiket OP #{{ $ticket->id }} beserta laporan perbaikannya?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-500 text-xs font-medium">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-12 text-center text-slate-500">Tidak ada tiket OP.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($tickets->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-white/10">{{ $tickets->links() }}</div>
                @endif
            </div>

    </div>
</x-app-layout>
