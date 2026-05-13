@extends('layouts.app')

@section('title', 'Iniciar sesión | Roig Arena')

@section('content')
<div class="grid-2" style="max-width: 500px; margin: 0 auto;">
    <div class="card">
        <h1>Iniciar sesión</h1>

        <form id="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>

        <p class="mt-3" style="text-align: center;">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </p>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = {
            email: formData.get('email'),
            password: formData.get('password')
        };

        try {
            const response = await fetch('/api/login', {
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
                alert(result.message || 'Error en el login');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error en el login');
        }
    });
</script>
@endsection
