<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MoldXlsxExport;
use App\Http\Controllers\Controller;
use App\Models\Mold;
use App\Rules\SafePlainText;
use App\Support\TablePagination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MoldController extends Controller
{
    public function index(): View
    {
        return view('admin.molds.index', [
            'molds' => Mold::query()->orderBy('name')->orderBy('id')->paginate(TablePagination::perPage()),
        ]);
    }

    public function export(): StreamedResponse|RedirectResponse
    {
        $molds = Mold::query()->orderBy('name')->orderBy('id')->get();

        if ($molds->isEmpty()) {
            return redirect()
                ->route('admin.molds.index')
                ->withErrors(['export' => 'Belum ada master data mold untuk diunduh.']);
        }

        $export = new MoldXlsxExport;
        $filename = $export->filename();

        return response()->streamDownload(function () use ($export, $molds) {
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
                $export->buildExporter($molds)->writeToFile($path);
                readfile($path);
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Mold::query()->create($this->validated($request) + ['is_active' => true]);

        return redirect()
            ->route('admin.molds.index')
            ->with('success', 'Master data mold berhasil ditambahkan.');
    }

    public function update(Request $request, Mold $mold): RedirectResponse
    {
        $mold->update($this->validated($request, $mold));

        return redirect()
            ->route('admin.molds.index')
            ->with('success', 'Master data mold berhasil diperbarui.');
    }

    public function destroy(Mold $mold): RedirectResponse
    {
        $mold->delete();

        return redirect()
            ->route('admin.molds.index')
            ->with('success', 'Master data mold berhasil dihapus.');
    }

    /**
     * @return array{name: string, customer: string, dimension: string, is_active?: bool}
     */
    private function validated(Request $request, ?Mold $mold = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150', new SafePlainText],
            'customer' => ['required', 'string', 'max:150', new SafePlainText],
            'dimension' => ['required', 'string', 'max:80', new SafePlainText],
        ];

        if ($mold !== null) {
            $rules['is_active'] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules);
        $validated['name'] = trim($validated['name']);
        $validated['customer'] = trim($validated['customer']);
        $validated['dimension'] = trim($validated['dimension']);

        if ($mold !== null) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        return $validated;
    }
}
