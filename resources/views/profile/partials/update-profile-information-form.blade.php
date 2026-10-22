<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">
            Perbarui nama dan NPK akun Anda.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="npk" value="NPK" />
            <x-text-input id="npk" name="npk" type="text" class="mt-1 block w-full" :value="old('npk', $user->npk)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('npk')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-slate-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
