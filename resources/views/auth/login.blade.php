@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Iniciar sesión</h1>

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 @error('email') border-red-500 @enderror"
                    required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-gray-700 font-semibold mb-2">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 @error('password') border-red-500 @enderror"
                    required>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botón -->
            <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold">
                Iniciar sesión
            </button>
        </form>

        <!-- Link a registro -->
        <p class="text-center text-gray-600 mt-6">
            ¿No tienes cuenta? <a href="{{ route('register') }}" class="text-red-600 hover:text-red-700 font-semibold">Regístrate aquí</a>
        </p>
    </div>
</div>
@endsection
