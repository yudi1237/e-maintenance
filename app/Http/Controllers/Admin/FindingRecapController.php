<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FindingRecapXlsxExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFindingRecapRequest;
use App\Http\Requests\UpdateFindingRecapRequest;
use App\Http\Requests\UpdateFindingRecapStatusRequest;
use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Support\FindingRecapStatus;
use App\Support\TablePagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FindingRecapController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $this->statusFilter($request);

        return view('admin.finding-recaps.index', $this->formData() + [
            'statusFilter' => $statusFilter,
            'recaps' => $this->filteredQuery($statusFilter)
                ->with(['machineName', 'user', 'statusNotes.user'])
                ->priorityByDueDate()
                ->paginate(TablePagination::perPage())
                ->withQueryString(),
        ]);
    }

    public function store(StoreFindingRecapRequest $request): RedirectResponse
    {
        $machine = MachineName::query()->findOrFail($request->integer('machine_name_id'));
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $status = $request->string('status')->toString();

        $recap = FindingRecap::query()->create([
            'user_id' => $request->user()->id,
            'machine_name_id' => $machine->id,
            'machine_number' => (int) $machine->machine_number,
            'machine_name' => $machine->viewLabel(),
            'equipment_name' => $request->string('equipment_name')->toString(),
            'found_date' => $request->input('found_date') ?: $today,
            'due_date' => $request->string('due_date')->toString(),
            'status' => $status,
        ]);

        $recap->addStatusNote($status, $request->string('note')->toString(), $request->user()->id);

        return redirect()
            ->route('admin.finding-recaps.index')
            ->with('success', 'Log temuan berhasil disimpan.');
    }

    public function edit(FindingRecap $findingRecap): View
    {
        return view('admin.finding-recaps.edit', $this->formData($findingRecap) + [
            'recap' => $findingRecap,
        ]);
    }

    public function update(UpdateFindingRecapRequest $request, FindingRecap $findingRecap): RedirectResponse
    {
        $machine = MachineName::query()->findOrFail($request->integer('machine_name_id'));

        $findingRecap->update([
            'machine_name_id' => $machine->id,
            'machine_number' => (int) $machine->machine_number,
            'machine_name' => $machine->viewLabel(),
            'equipment_name' => $request->string('equipment_name')->toString(),
            'due_date' => $request->string('due_date')->toString(),
        ]);

        return redirect()
            ->route('admin.finding-recaps.index')
            ->with('success', 'Log temuan berhasil diperbarui.');
    }

    public function destroy(FindingRecap $findingRecap): RedirectResponse
    {
        $findingRecap->delete();

        return redirect()
            ->route('admin.finding-recaps.index')
            ->with('success', 'Log temuan berhasil dihapus.');
    }

    public function updateStatus(UpdateFindingRecapStatusRequest $request, FindingRecap $findingRecap): RedirectResponse
    {
        $status = $request->string('status')->toString();

        if ($findingRecap->status === $status) {
            return back()->withErrors([
                'status' => 'Pilih status yang berbeda, lalu isi catatan perubahan.',
            ]);
        }

        $findingRecap->update(['status' => $status]);
        $findingRecap->addStatusNote($status, $request->string('note')->toString(), $request->user()->id);

        return back()->with('success', 'Status log temuan diperbarui menjadi '.$findingRecap->fresh()->status_label.'.');
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $statusFilter = $this->statusFilter($request);
        $recaps = $this->filteredQuery($statusFilter)
            ->with(['machineName', 'statusNotes.user'])
            ->priorityByDueDate()
            ->get();

        if ($recaps->isEmpty()) {
            return redirect()
                ->route('admin.finding-recaps.index', array_filter(['status' => $statusFilter]))
                ->withErrors(['export' => 'Belum ada log temuan untuk diunduh.']);
        }

        $export = new FindingRecapXlsxExport;
        $filename = $export->filename();

        return response()->streamDownload(function () use ($export, $recaps) {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
            if ($tmp === false) {
                throw new \RuntimeException('Tidak dapat membuat file sementara untuk export Excel.');
            }

            $path = $tmp.'.xlsx';
            if (! rename($tmp, $path)) {
                @unlink($tmp);
                throw new \RuntimeException('Tidak dapat menyiapkan file export Excel.');
            }

            try {
                $export->buildExporter($recaps)->writeToFile($path);
                readfile($path);
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** @return array<string, mixed> */
    private function formData(?FindingRecap $recap = null): array
    {
        $machines = MachineName::query()
            ->operational()
            ->whereNotNull('machine_number')
            ->where(function ($query) use ($recap) {
                $query->where('is_active', true);
                if ($recap?->machine_name_id) {
                    $query->orWhere('id', $recap->machine_name_id);
                }
            })
            ->orderBy('machine_number')
            ->get(['id', 'machine_number', 'name', 'brand', 'tonnage']);

        return [
            'today' => now()->timezone('Asia/Jakarta')->toDateString(),
            'todayLabel' => now()->timezone('Asia/Jakarta')->format('d/m/Y'),
            'machines' => $machines,
            'statusLabels' => FindingRecapStatus::labels(),
        ];
    }

    private function statusFilter(Request $request): string
    {
        $status = $request->string('status')->toString();

        return in_array($status, FindingRecapStatus::values(), true) ? $status : '';
    }

    private function filteredQuery(string $statusFilter): Builder
    {
        $query = FindingRecap::query();

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        return $query;
    }
}
