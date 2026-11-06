<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SparepartHistoryXlsxExport;
use App\Http\Controllers\Controller;
use App\Models\MachineName;
use App\Support\SparepartHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SparepartHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->validateFilters($request);

        return view('admin.sparepart-history.index', [
            'rows' => SparepartHistory::paginate($request),
            'filters' => $this->filters($request),
            'machineNumbers' => MachineName::query()
                ->operational()
                ->active()
                ->whereNotNull('machine_number')
                ->orderBy('machine_number')
                ->pluck('machine_number')
                ->unique()
                ->values(),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $this->validateFilters($request);
        $rows = SparepartHistory::rows($request);

        if ($rows->isEmpty()) {
            return redirect()
                ->route('admin.sparepart-history.index', $this->filters($request))
                ->withErrors(['export' => 'Tidak ada history spare part sesuai filter.']);
        }

        $export = new SparepartHistoryXlsxExport;
        $filename = $export->filename();

        return response()->streamDownload(function () use ($export, $rows) {
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
                $export->buildExporter($rows)->writeToFile($path);
                readfile($path);
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** @return array<string, string> */
    private function filters(Request $request): array
    {
        return array_filter([
            'start_date' => (string) $request->input('start_date', ''),
            'end_date' => (string) $request->input('end_date', ''),
            'machine_number' => (string) $request->input('machine_number', ''),
            'sparepart' => (string) $request->input('sparepart', ''),
            'pic' => (string) $request->input('pic', ''),
        ], fn ($value) => $value !== '');
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'machine_number' => ['nullable', 'integer', 'min:1'],
            'sparepart' => ['nullable', 'string', 'max:100'],
            'pic' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
