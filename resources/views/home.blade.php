@extends('layouts.app')

@section('title', 'Inicio | Roig Arena')

@section('content')
<section class="text-center mb-4">
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">Bienvenido a Roig Arena</h1>
    <p style="font-size: 1.2rem; color: #666;">Descubre los mejores eventos y compra tus entradas</p>
    @guest
        <a href="{{ route('register') }}" class="btn btn-primary mt-2" style="font-size: 1.1rem;">Comenzar</a>
    @endguest
</section>

<section class="card">
    <h2>Próximos eventos</h2>
    <div id="eventos-container" class="grid mt-3">
        <p>Cargando eventos...</p>
    </div>
</section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch('/api/eventos');
            const eventos = await response.json();

            const container = document.getElementById('eventos-container');
            container.innerHTML = '';

            if (eventos.data && eventos.data.length) {
                eventos.data.forEach(evento => {
                    container.innerHTML += `
                        <div class="event-card">
                            <div class="event-card-image">📅</div>
                            <div class="event-card-content">
                                <div class="event-card-title">${evento.nombre}</div>
                                <div class="event-card-date">${new Date(evento.fecha).toLocaleDateString('es-ES')}</div>
                                <a href="/eventos/${evento.id}" class="btn btn-primary">Ver detalles</a>
                            </div>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = '<p>No hay eventos disponibles</p>';
            }
        } catch (error) {
            console.error('Error cargando eventos:', error);
        }
    });
</script>
@endsection
