<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\PreventSensitivePageCache;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $default = $request->user()->canAccessAdmin()
            ? route('admin.dashboard')
            : route('home');

        return redirect()->intended($default);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $recaller = Auth::guard('web')->getRecallerName();

        Auth::guard('web')->logout();

        if ($user !== null) {
            User::query()->whereKey($user->id)->update(['remember_token' => null]);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = redirect()
            ->route('login')
            ->with('status', 'Anda telah keluar. Silakan login kembali untuk melanjutkan.');

        PreventSensitivePageCache::applyNoCacheHeaders($response);

        if ($recaller !== null) {
            return $response->withCookie(Cookie::forget($recaller));
        }

        return $response;
    }
}
