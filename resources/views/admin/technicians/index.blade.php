<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Teknisi</h2>
    </x-slot>

    <div class="py-8">
        <div class="em-shell space-y-6">
            <x-alert />

            @php
                $showTechnicianForm = $errors->any() || filled(old('name')) || filled(old('npk'));
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-slate-500 dark:text-slate-400">PIC teknisi dipakai saat mengambil tiket OP di halaman Teknisi Maintenance. Setiap PIC punya PIN sendiri.</p>
                <button type="button" id="show-technician-form" class="em-btn-primary {{ $showTechnicianForm ? 'hidden' : '' }}" style="background:#0891b2;color:#fff;">
                    Tambah Teknisi
                </button>
            </div>
            <div id="technician-create-form" class="em-card p-6 {{ $showTechnicianForm ? '' : 'hidden' }}">
                <h3 class="font-medium text-gray-800 dark:text-white mb-4">Tambah PIC Teknisi</h3>
                <form method="POST" action="{{ route('admin.technicians.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nama PIC" />
                        <x-text-input id="name" name="name" class="block mt-1 w-full" value="{{ old('name') }}" required />
                    </div>
                    <div>
                        <x-input-label for="npk" value="NPK" />
                        <x-text-input id="npk" name="npk" type="text" class="block mt-1 w-full" value="{{ old('npk') }}" required />
                    </div>
                    <div>
                        <x-input-label for="pin" value="PIN (4–6 digit)" />
                        <x-text-input id="pin" name="pin" type="password" inputmode="numeric" maxlength="6" class="block mt-1 w-full" required autocomplete="new-password" />
                    </div>
                    <div>
                        <x-input-label for="pin_confirmation" value="Konfirmasi PIN" />
                        <x-text-input id="pin_confirmation" name="pin_confirmation" type="password" inputmode="numeric" maxlength="6" class="block mt-1 w-full" required autocomplete="new-password" />
                    </div>
                    <div class="flex items-end">
                        <x-primary-button>Simpan PIC Teknisi</x-primary-button>
                    </div>
                </form>
            </div>
            <script>
                document.getElementById('show-technician-form')?.addEventListener('click', () => {
                    document.getElementById('technician-create-form')?.classList.remove('hidden');
                    document.getElementById('show-technician-form')?.classList.add('hidden');
                    document.getElementById('name')?.focus();
                });
            </script>

            @include('admin.partials.technician-status')

            <div class="em-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-700 dark:text-slate-200">
                        <thead class="em-table-head">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama PIC</th>
                                <th class="px-4 py-3 text-left">NPK</th>
                                <th class="px-4 py-3 text-left">PIN baru</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($technicians as $technician)
                                <tr>
                                    <td class="px-4 py-3">
                                        <form id="tech-{{ $technician->id }}" method="POST" action="{{ route('admin.technicians.update', $technician) }}">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <x-text-input form="tech-{{ $technician->id }}" name="name" class="!mt-0 w-full min-w-[10rem]" value="{{ $technician->name }}" required />
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-text-input form="tech-{{ $technician->id }}" name="npk" class="!mt-0 w-full min-w-[7rem]" value="{{ $technician->npk }}" required />
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col gap-2 min-w-[8rem]">
                                            <x-text-input form="tech-{{ $technician->id }}" name="pin" type="password" inputmode="numeric" maxlength="6" class="!mt-0 w-full" placeholder="PIN baru" autocomplete="new-password" />
                                            <x-text-input form="tech-{{ $technician->id }}" name="pin_confirmation" type="password" inputmode="numeric" maxlength="6" class="!mt-0 w-full" placeholder="Ulangi PIN" autocomplete="new-password" />
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <label class="inline-flex items-center gap-2 text-xs">
                                            <input form="tech-{{ $technician->id }}" type="checkbox" name="is_active" value="1" @checked($technician->is_active)>
                                            {{ $technician->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </label>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <button form="tech-{{ $technician->id }}" type="submit" class="text-cyan-600 text-xs font-semibold hover:underline">Simpan</button>
                                        <form method="POST" action="{{ route('admin.technicians.destroy', $technician) }}" class="inline ms-3" onsubmit="return confirm('Hapus PIC teknisi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 text-xs hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada PIC teknisi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($technicians->hasPages())
                    <div class="p-4">{{ $technicians->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
