<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthWebController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Registrar nuevo usuario
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nombre'   => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        auth()->login($user);

        // Generar token Sanctum para uso en AJAX
        $token = $user->createToken('auth-token')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->route('home')->with('success', '¡Bienvenido ' . $user->nombre . '!');
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Iniciar sesión
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        auth()->login($user);

        // Eliminar tokens anteriores
        $user->tokens()->delete();

        // Generar token Sanctum para uso en AJAX
        $token = $user->createToken('auth-token')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->route('home')->with('success', '¡Sesión iniciada correctamente!');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente');
    }
}
