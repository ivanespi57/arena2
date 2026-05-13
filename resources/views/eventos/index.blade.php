@extends('layouts.app')

@section('title', 'Eventos | Roig Arena')

@section('content')
<h1>Eventos disponibles</h1>

<div id="eventos-container" class="grid mt-3">
    <p>Cargando eventos...</p>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch('/api/eventos');
            const data = await response.json();

            const container = document.getElementById('eventos-container');
            container.innerHTML = '';

            if (data.data && data.data.length) {
                data.data.forEach(evento => {
                    const fecha = new Date(evento.fecha).toLocaleDateString('es-ES');
                    container.innerHTML += `
                        <div class="event-card">
                            <div class="event-card-image">🎭</div>
                            <div class="event-card-content">
                                <div class="event-card-title">${evento.nombre}</div>
                                <div class="event-card-date">${fecha}</div>
                                <p>${evento.descripcion?.substring(0, 100) || ''}</p>
                                <a href="/eventos/${evento.id}" class="btn btn-primary mt-2">Ver evento</a>
                            </div>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = '<p>No hay eventos disponibles</p>';
            }
        } catch (error) {
            console.error('Error cargando eventos:', error);
            document.getElementById('eventos-container').innerHTML = '<p>Error cargando eventos</p>';
        }
    });
</script>
@endsection
