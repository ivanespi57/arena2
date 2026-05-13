<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->withInput();
        }

        $request->session()->regenerate();

        $token = auth()->user()->createToken('web-session')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre'                => 'required|string|max:255',
            'apellido'              => 'required|string|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $token = $user->createToken('web-session')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        return view('auth.dashboard');
    }

    public function profile()
    {
        return view('auth.profile');
    }

    public function logout(Request $request)
    {
        auth()->user()?->tokens()->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente');
    }
}
