@extends('layouts.app')

@section('title', 'Registrarse | Roig Arena')

@section('content')
<div class="grid-2" style="max-width: 500px; margin: 0 auto;">
    <div class="card">
        <h1>Crear cuenta</h1>

        <form id="register-form">
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary">Registrarse</button>
        </form>

        <p class="mt-3" style="text-align: center;">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </p>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('register-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = {
            name: formData.get('name'),
            email: formData.get('email'),
            password: formData.get('password'),
            password_confirmation: formData.get('password_confirmation')
        };

        try {
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                localStorage.setItem('token', result.token);
                window.location.href = '/dashboard';
            } else {
                alert(result.message || 'Error en el registro');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error en el registro');
        }
    });
</script>
@endsection
