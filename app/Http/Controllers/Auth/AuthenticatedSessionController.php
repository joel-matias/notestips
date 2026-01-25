<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function store(Request $request)
    {

        $credentials = $request->validate(
            [
                'username' => ['required', 'string', 'max:32'],
                'password' => ['required'],
            ],
            [
                'username.required' => 'El nombre de usuario es obligatorio.',
                'username.max' => 'El nombre de usuario no puede tener más de 32 caracteres.',
                'password.required' => 'La contraseña es obligatoria.',
            ]
        );

        $key = Str::lower($request->input('username')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->with('lock_seconds', $seconds)
                ->withErrors(['username' => 'LOCKED'])
                ->onlyInput('username');
        }

        if (! Auth::attempt($credentials)) {// , $request->boolean('remember'))) { //remember aun no implementado
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'username' => 'Usuario o contraseña incorrectos.',
            ])->onlyInput('username');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended('/notes');
    }

    public function destroy(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
