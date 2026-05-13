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
                    const imagen = evento.poster_url
                        ? `<img src="${evento.poster_url}" alt="${evento.nombre}"
                                style="width:100%; height:100%; object-fit:cover;"
                                onerror="this.parentElement.innerHTML='🎭'">`
                        : '🎭';
                    container.innerHTML += `
                        <div class="event-card">
                            <div class="event-card-image">${imagen}</div>
                            <div class="event-card-content">
                                <div class="event-card-title">${evento.nombre}</div>
                                <div class="event-card-date">📅 ${evento.fecha}${evento.hora ? ' — 🕐 ' + evento.hora : ''}</div>
                                <p style="color:#555; font-size:0.9rem; margin-bottom:0.75rem;">${evento.descripcion_corta ? evento.descripcion_corta.substring(0, 100) : ''}</p>
                                <a href="/eventos/${evento.id}" class="btn btn-primary" style="width:100%; text-align:center;">Ver evento →</a>
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
