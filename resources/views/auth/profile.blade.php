@extends('layouts.app')

@section('title', 'Mi Perfil | Roig Arena')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h1>Mi Perfil</h1>

    <div id="perfil-container">
        <p>Cargando perfil...</p>
    </div>

    <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>

@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('token');

    document.addEventListener('DOMContentLoaded', async () => {
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            const response = await fetch('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            const user = data.data || data;

            const container = document.getElementById('perfil-container');
            container.innerHTML = `
                <div>
                    <p><strong>Nombre:</strong> ${user.name || user.nombre || 'N/A'}</p>
                    <p><strong>Email:</strong> ${user.email}</p>
                    <p><strong>Miembro desde:</strong> ${new Date(user.created_at).toLocaleDateString('es-ES')}</p>
                </div>
            `;
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('perfil-container').innerHTML = '<p>Error cargando perfil</p>';
        }
    });
</script>
@endsection
