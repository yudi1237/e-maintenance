<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamageCategory;
use App\Support\TablePagination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DamageCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.damage-categories.index', [
            'categories' => DamageCategory::orderBy('sort_order')->orderBy('name')->paginate(TablePagination::perPage()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:damage_categories,name'],
            'allows_other' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $maxOrder = DamageCategory::max('sort_order') ?? 0;

        DamageCategory::create([
            'name' => $validated['name'],
            'allows_other' => $request->boolean('allows_other'),
            'sort_order' => $validated['sort_order'] ?? ($maxOrder + 1),
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori kerusakan berhasil ditambahkan.');
    }

    public function update(Request $request, DamageCategory $damageCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:damage_categories,name,'.$damageCategory->id],
            'allows_other' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $damageCategory->update([
            'name' => $validated['name'],
            'allows_other' => $request->boolean('allows_other'),
            'sort_order' => $validated['sort_order'] ?? $damageCategory->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Kategori kerusakan berhasil diperbarui.');
    }

    public function destroy(DamageCategory $damageCategory): RedirectResponse
    {
        $damageCategory->delete();

        return back()->with('success', 'Kategori kerusakan berhasil dihapus.');
    }
}
