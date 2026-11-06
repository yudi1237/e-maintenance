<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">User Login</h2>
    </x-slot>

    <div
        class="py-8"
        x-data="{
            show: false,
            action: '',
            name: '',
            npk: '',
            role: 'technician',
            open(user, url) {
                this.action = url;
                this.name = user.name;
                this.npk = user.npk;
                this.role = user.role;
                this.show = true;
                this.$nextTick(() => {
                    this.$refs.password.value = '';
                    this.$refs.passwordConfirmation.value = '';
                });
            },
            close() {
                this.show = false;
            }
        }"
    >
        <div class="em-shell space-y-6">
            <x-alert />

            <div class="em-card p-6">
                <h3 class="font-medium text-gray-800 dark:text-white mb-4">Tambah Pengguna Baru</h3>
                <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="npk" value="NPK" />
                        <x-text-input id="npk" name="npk" type="text" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="role" value="Peran" />
                        <select id="role" name="role" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', 'technician') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <x-primary-button>Simpan Pengguna</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="em-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-700 dark:text-slate-200">
                        <thead class="em-table-head">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">NPK</th>
                                <th class="px-4 py-3 text-left">Peran</th>
                                <th class="px-4 py-3 text-left">MFA</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-4 py-3">{{ $user->name }}</td>
                                    <td class="px-4 py-3">{{ $user->npk }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs {{ $user->roleBadgeClass() }}">
                                            {{ $user->isTechnician() ? 'Teknisi Maintenance' : $user->roleLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if (method_exists($user, 'hasTwoFactorEnabled') && $user->hasTwoFactorEnabled())
                                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Aktif</span>
                                        @else
                                            <span class="text-xs text-slate-500">Belum</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <button
                                            type="button"
                                            class="text-cyan-600 text-xs font-semibold hover:underline"
                                            @click="open({{ \Illuminate\Support\Js::from(['name' => $user->name, 'npk' => $user->npk, 'role' => $user->role]) }}, {{ \Illuminate\Support\Js::from(route('admin.users.update', $user)) }})"
                                        >
                                            Edit
                                        </button>
                                        @if (method_exists($user, 'hasTwoFactorEnabled') && $user->hasTwoFactorEnabled() && \Illuminate\Support\Facades\Route::has('admin.users.reset-mfa'))
                                            <form method="POST" action="{{ route('admin.users.reset-mfa', $user) }}" class="inline ms-3" onsubmit="return confirm('Reset Google Authenticator untuk pengguna ini?')">
                                                @csrf
                                                <button type="submit" class="text-amber-600 text-xs font-semibold hover:underline">Reset MFA</button>
                                            </form>
                                        @endif
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline ms-3" onsubmit="return confirm('Hapus pengguna?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 text-xs hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($users->hasPages())
                    <div class="p-4">{{ $users->links() }}</div>
                @endif
            </div>
        </div>

        <div
            x-show="show"
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-user-title"
        >
            <div class="absolute inset-0 z-0 bg-slate-950/60 backdrop-blur-[4px]" @click="close()"></div>
            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:border dark:border-white/10 dark:bg-slate-900">
                <h3 id="edit-user-title" class="text-lg font-semibold text-slate-900 dark:text-white">Edit Pengguna</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ubah data akun atau isi password baru untuk reset password.</p>

                <form method="POST" :action="action" class="mt-5 grid grid-cols-1 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="edit_name" value="Nama" />
                        <x-text-input id="edit_name" name="name" class="block mt-1 w-full" x-model="name" required />
                    </div>
                    <div>
                        <x-input-label for="edit_npk" value="NPK" />
                        <x-text-input id="edit_npk" name="npk" type="text" class="block mt-1 w-full" x-model="npk" required />
                    </div>
                    <div>
                        <x-input-label for="edit_role" value="Peran" />
                        <select id="edit_role" name="role" x-model="role" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="edit_password" value="Password baru (opsional)" />
                        <x-text-input id="edit_password" x-ref="password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                    </div>
                    <div>
                        <x-input-label for="edit_password_confirmation" value="Konfirmasi password baru" />
                        <x-text-input id="edit_password_confirmation" x-ref="passwordConfirmation" name="password_confirmation" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                    </div>
                    <div class="mt-2 flex gap-3">
                        <button type="button" class="w-1/2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300" @click="close()">Batal</button>
                        <x-primary-button class="w-1/2 justify-center">Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
