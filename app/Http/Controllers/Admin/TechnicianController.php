<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Support\TablePagination;
use App\Support\TechnicianDuty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function index(): View
    {
        return view('admin.technicians.index', [
            'technicians' => Technician::query()->orderBy('name')->paginate(TablePagination::perPage()),
            'technicianStatuses' => TechnicianDuty::snapshot(),
        ]);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'technicians' => TechnicianDuty::snapshot(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npk' => ['required', 'string', 'max:50', 'unique:technicians,npk'],
            'pin' => ['required', 'digits_between:4,6', 'confirmed'],
        ], [
            'pin.digits_between' => 'PIN harus 4 sampai 6 digit angka.',
            'pin.confirmed' => 'Konfirmasi PIN tidak sama.',
        ]);

        Technician::create([
            'name' => $validated['name'],
            'npk' => $validated['npk'],
            'pin' => $validated['pin'],
            'is_active' => true,
        ]);

        return back()->with('success', 'PIC teknisi berhasil ditambahkan.');
    }

    public function update(Request $request, Technician $technician): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npk' => ['required', 'string', 'max:50', Rule::unique('technicians', 'npk')->ignore($technician->id)],
            'pin' => ['nullable', 'digits_between:4,6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'pin.digits_between' => 'PIN harus 4 sampai 6 digit angka.',
            'pin.confirmed' => 'Konfirmasi PIN tidak sama.',
        ]);

        $technician->update([
            'name' => $validated['name'],
            'npk' => $validated['npk'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($validated['pin'])) {
            $technician->update(['pin' => $validated['pin']]);
        }

        return back()->with('success', 'PIC teknisi berhasil diperbarui.');
    }

    public function destroy(Technician $technician): RedirectResponse
    {
        $technician->delete();

        return back()->with('success', 'PIC teknisi berhasil dihapus.');
    }
}
