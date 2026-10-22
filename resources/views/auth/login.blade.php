<x-guest-layout>
    <div class="em-login-head">
        <p class="em-login-kicker">
            <span class="em-login-kicker-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4.1 4.1 0 0 0-5.8 5.8l-5.2 5.2a2 2 0 1 0 2.8 2.8l5.2-5.2a4.1 4.1 0 0 0 5.8-5.8l-2.1 2.1-1.8-.4-.4-1.8 2.1-2.1z" />
                </svg>
            </span>
            Portal Maintenance
        </p>
        <h1 class="em-login-title">e-Maintenance</h1>
        <p class="em-login-subtitle">Tiket OP, check sheet preventive, dan data mesin PT Dharma Poliplast.</p>
        <div class="em-login-chips" aria-hidden="true">
            <span>Mesin</span>
            <span>Preventive</span>
            <span>Tiket OP</span>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-amber-200/80 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="em-login-form">
        @csrf

        <div>
            <label for="npk" class="em-login-label">NPK akun</label>
            <input
                id="npk"
                class="em-login-input"
                type="text"
                name="npk"
                value="{{ old('npk') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan NPK"
            >
            <x-input-error :messages="$errors->get('npk')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="em-login-label">Password akun</label>
            <input
                id="password"
                class="em-login-input"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="em-login-remember">
            <input id="remember_me" type="checkbox" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>

        <button type="submit" class="em-login-submit">
            Masuk ke sistem
        </button>
    </form>
</x-guest-layout>
