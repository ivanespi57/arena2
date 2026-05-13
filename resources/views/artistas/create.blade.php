@extends('layouts.app')

@section('title', 'Crear Artista | Roig Arena')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h1>Crear artista</h1>

    <form id="artista-form">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Crear artista</button>
        <a href="{{ route('eventos.index') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">Volver</a>
    </form>
</div>

@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('token');

    document.getElementById('artista-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!token) {
            window.location.href = '/login';
            return;
        }

        const formData = new FormData(e.target);
        const data = {
            nombre: formData.get('nombre'),
            descripcion: formData.get('descripcion')
        };

        try {
            const response = await fetch('/api/admin/artistas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Artista creado exitosamente');
                window.location.href = '/eventos';
            } else {
                const result = await response.json();
                alert(result.message || 'Error al crear artista');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al crear artista');
        }
    });
</script>
@endsection
