<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AuthWebController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Mostrar dashboard del usuario
     */
    public function dashboard()
    {
        return view('auth.dashboard');
    }

    /**
     * Mostrar perfil del usuario
     */
    public function profile()
    {
        return view('auth.profile');
    }

    /**
     * Logout
     */
    public function logout()
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente');
    }
}
