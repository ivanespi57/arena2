@extends('layouts.app')

@section('title', 'Crear Evento | Roig Arena')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h1>Crear nuevo evento</h1>

    <form id="evento-form">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha</label>
            <input type="datetime-local" id="fecha" name="fecha" required>
        </div>

        <button type="submit" class="btn btn-primary">Crear evento</button>
        <a href="{{ route('eventos.index') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">Volver</a>
    </form>
</div>

@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('token');

    document.getElementById('evento-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!token) {
            window.location.href = '/login';
            return;
        }

        const formData = new FormData(e.target);
        const data = {
            nombre: formData.get('nombre'),
            descripcion: formData.get('descripcion'),
            fecha: formData.get('fecha')
        };

        try {
            const response = await fetch('/api/admin/eventos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Evento creado exitosamente');
                window.location.href = '/eventos';
            } else {
                const result = await response.json();
                alert(result.message || 'Error al crear evento');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al crear evento');
        }
    });
</script>
@endsection
