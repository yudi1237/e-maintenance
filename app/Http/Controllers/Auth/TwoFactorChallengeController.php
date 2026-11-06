<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\GoogleTotp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function setup(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);
        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('mfa.challenge');
        }

        $secret = $request->session()->get('login.mfa_setup_secret');
        if (! is_string($secret) || $secret === '') {
            $secret = GoogleTotp::generateSecret();
            $request->session()->put('login.mfa_setup_secret', $secret);
        }

        $otpauth = GoogleTotp::otpauthUrl($secret, $user->npk);

        return view('auth.mfa-setup', [
            'secret' => $secret,
            'qr' => GoogleTotp::qrDataUri($otpauth),
            'account' => $user->npk,
        ]);
    }

    public function confirmSetup(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);
        if ($user === null) {
            return redirect()->route('login');
        }

        $secret = $request->session()->get('login.mfa_setup_secret');
        if (! is_string($secret) || $secret === '') {
            return redirect()->route('mfa.setup');
        }

        $this->ensureNotRateLimited($request, $user);

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        if (! GoogleTotp::verify($secret, $validated['code'])) {
            RateLimiter::hit($this->throttleKey($request, $user));

            throw ValidationException::withMessages([
                'code' => 'Kode Google Authenticator tidak valid. Coba lagi.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request, $user));

        $recoveryCodes = GoogleTotp::generateRecoveryCodes();
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => GoogleTotp::hashRecoveryCodes($recoveryCodes),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('login.mfa_setup_secret');
        $request->session()->put('login.mfa_recovery_codes', $recoveryCodes);

        $this->completeLogin($request, $user);

        return redirect()->route('mfa.recovery');
    }

    public function challenge(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);
        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('mfa.setup');
        }

        return view('auth.mfa-challenge');
    }

    public function confirmChallenge(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);
        if ($user === null) {
            return redirect()->route('login');
        }

        $this->ensureNotRateLimited($request, $user);

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = $validated['code'];
        $ok = GoogleTotp::verify((string) $user->two_factor_secret, $code);

        if (! $ok) {
            $remaining = GoogleTotp::consumeRecoveryCode(
                $user->two_factor_recovery_codes ?? [],
                $code,
            );
            if ($remaining !== null) {
                $user->forceFill(['two_factor_recovery_codes' => $remaining])->save();
                $ok = true;
            }
        }

        if (! $ok) {
            RateLimiter::hit($this->throttleKey($request, $user));

            throw ValidationException::withMessages([
                'code' => 'Kode autentikator atau kode pemulihan tidak valid.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request, $user));
        $this->completeLogin($request, $user);

        return $this->redirectAfterLogin($request, $user);
    }

    public function recovery(Request $request): View|RedirectResponse
    {
        $codes = $request->session()->get('login.mfa_recovery_codes');
        if (! is_array($codes) || $codes === []) {
            return $this->redirectAfterLogin($request, $request->user());
        }

        return view('auth.mfa-recovery', ['codes' => $codes]);
    }

    public function acknowledgeRecovery(Request $request): RedirectResponse
    {
        $request->session()->forget('login.mfa_recovery_codes');

        return $this->redirectAfterLogin($request, $request->user());
    }

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('login.pending_user_id');
        if (! is_numeric($id)) {
            return null;
        }

        return User::query()->find((int) $id);
    }

    private function completeLogin(Request $request, User $user): void
    {
        $remember = (bool) $request->session()->pull('login.remember', false);
        $request->session()->forget(['login.pending_user_id', 'login.mfa_setup_secret']);

        Auth::login($user, $remember);
        $request->session()->regenerate();
    }

    private function redirectAfterLogin(Request $request, ?User $user): RedirectResponse
    {
        $user ??= $request->user();
        $default = $user ? route($user->homeRouteName()) : route('home');

        $intended = $request->session()->pull('url.intended');
        if (! is_string($intended) || $intended === '' || str_contains($intended, '/login')) {
            return redirect()->to($default);
        }

        return redirect()->to($intended);
    }

    private function ensureNotRateLimited(Request $request, User $user): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request, $user), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request, $user));

        throw ValidationException::withMessages([
            'code' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function throttleKey(Request $request, User $user): string
    {
        return 'mfa|'.$user->id.'|'.$request->ip();
    }
}
