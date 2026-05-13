@extends('layouts.app')

@section('title', 'Iniciar sesión | Roig Arena')

@section('content')
<div style="max-width: 500px; margin: 0 auto;">
    <div class="card">
        <h1>Iniciar sesión</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>

        <p class="mt-3" style="text-align: center;">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </p>
    </div>
</div>
@endsection
