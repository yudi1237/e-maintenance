<form method="POST" action="{{ route('admin.robots.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:items-end">
    @csrf
    <div>
        <x-input-label for="robot_number" value="Nomor Robot" />
        <x-text-input id="robot_number" name="robot_number" class="block mt-1 w-full" :value="old('robot_number')" placeholder="Contoh: R01" required />
        <x-input-error :messages="$errors->get('robot_number')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="robot_type" value="Type Robot" />
        <x-text-input id="robot_type" name="robot_type" class="block mt-1 w-full" :value="old('robot_type')" placeholder="Contoh: Yushin YC-150" required />
        <x-input-error :messages="$errors->get('robot_type')" class="mt-1" />
    </div>
    <div class="sm:col-span-2 lg:col-span-1 flex items-end">
        <x-primary-button class="w-full justify-center sm:w-auto">Tambah Robot</x-primary-button>
    </div>
</form>

<div class="mt-5 overflow-x-auto">
    <table class="min-w-full text-sm text-slate-700 dark:text-slate-200">
        <thead class="em-table-head">
            <tr>
                <th class="px-4 py-3 text-left">Nomor Robot</th>
                <th class="px-4 py-3 text-left">Type Robot</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
            @forelse ($robots as $robot)
                <tr>
                    <td class="px-4 py-3">
                        <form id="robot-{{ $robot->id }}" method="POST" action="{{ route('admin.robots.update', $robot) }}">
                            @csrf
                            @method('PUT')
                        </form>
                        <x-text-input form="robot-{{ $robot->id }}" name="robot_number" class="!mt-0 w-full min-w-[7rem]" :value="$robot->robot_number" required />
                    </td>
                    <td class="px-4 py-3">
                        <x-text-input form="robot-{{ $robot->id }}" name="robot_type" class="!mt-0 w-full min-w-[10rem]" :value="$robot->robot_type" required />
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <label class="inline-flex items-center gap-2 text-xs">
                            <input form="robot-{{ $robot->id }}" type="hidden" name="is_active" value="0">
                            <input form="robot-{{ $robot->id }}" type="checkbox" name="is_active" value="1" @checked($robot->is_active)>
                            {{ $robot->is_active ? 'Aktif' : 'Nonaktif' }}
                        </label>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button form="robot-{{ $robot->id }}" type="submit" class="text-cyan-600 text-xs font-semibold hover:underline">Simpan</button>
                        <form method="POST" action="{{ route('admin.robots.destroy', $robot) }}" class="inline ms-3" onsubmit="return confirm('Hapus robot ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 text-xs hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada master data robot. Isi nomor dan type robot di form atas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (method_exists($robots, 'hasPages') && $robots->hasPages())
    <div class="p-4">{{ $robots->links() }}</div>
@endif
