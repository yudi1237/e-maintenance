<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Master Kategori Kerusakan</h2>
    </x-slot>

    <div class="py-8">
        <div class="em-shell space-y-6">
            <x-alert-light />

            <div class="em-card p-6">
                <form method="POST" action="{{ route('admin.damage-categories.store') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="name" value="Nama Kategori" />
                        <x-text-input id="name" name="name" class="block mt-1 w-full" placeholder="Contoh: Mesin Injection, Mold, Utility..." required />
                    </div>
                    <div class="flex items-center gap-4 pb-0.5">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap dark:text-slate-300">
                            <input type="checkbox" name="allows_other" value="1" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-900">
                            Other (input manual)
                        </label>
                        <x-primary-button>Tambah</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="em-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-700 dark:text-slate-200">
                        <thead class="em-table-head">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama Kategori</th>
                                <th class="px-4 py-3 text-left">Other Manual</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @foreach ($categories as $category)
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $category->name }}</td>
                                    <td class="px-4 py-3">
                                        @if ($category->allows_other)
                                            <span class="px-2 py-1 rounded text-xs bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">Ya</span>
                                        @else
                                            <span class="text-gray-400 dark:text-slate-500">Tidak</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <form method="POST" action="{{ route('admin.damage-categories.update', $category) }}" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="name" value="{{ $category->name }}">
                                            <input type="hidden" name="allows_other" value="{{ $category->allows_other ? 1 : 0 }}">
                                            <input type="hidden" name="sort_order" value="{{ $category->sort_order }}">
                                            <label class="text-xs cursor-pointer">
                                                <input type="checkbox" name="is_active" value="1" @checked($category->is_active) onchange="this.form.submit()">
                                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </label>
                                        </form>
                                        <form method="POST" action="{{ route('admin.damage-categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 text-xs hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($categories->hasPages())
                    <div class="p-4">{{ $categories->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
