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
        const container = document.getElementById('eventos-container');
        try {
            const res = await fetch('/api/eventos');
            const data = await res.json();

            container.innerHTML = '';

            if (data.data && data.data.length) {
                data.data.forEach(evento => {
                    container.innerHTML += `
                        <div class="event-card">
                            <div class="event-card-image">🎭</div>
                            <div class="event-card-content">
                                <div class="event-card-title">${evento.nombre}</div>
                                <div class="event-card-date">${evento.fecha} ${evento.hora ? '— ' + evento.hora : ''}</div>
                                <p>${evento.descripcion_corta ? evento.descripcion_corta.substring(0, 100) : ''}</p>
                                <a href="/eventos/${evento.id}" class="btn btn-primary mt-2">Ver evento</a>
                            </div>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = '<p>No hay eventos disponibles</p>';
            }
        } catch {
            container.innerHTML = '<p>Error cargando eventos. Inténtalo de nuevo.</p>';
        }
    });
</script>
@endsection
