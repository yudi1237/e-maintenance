<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-slate-400">
        Lupa password? Masukkan NPK Anda. Jika NPK terdaftar, kami akan mengirim tautan reset password.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="npk" value="NPK" />
            <x-text-input id="npk" class="block mt-1 w-full" type="text" name="npk" :value="old('npk')" required autofocus />
            <x-input-error :messages="$errors->get('npk')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Kirim Tautan Reset
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
