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
    <h2>Proximos eventos</h2>
    <div id="eventos-container" class="grid mt-3">
        <p>Cargando eventos...</p>
    </div>
</section>

@endsection

@section('scripts')
<script>
    function formatFecha(val) {
        if (!val) return '';
        const d = val.substring(0, 10);
        const [y, m, day] = d.split('-');
        return `${day}/${m}/${y}`;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch('/api/eventos');
            const eventos  = await response.json();
            const container = document.getElementById('eventos-container');
            container.innerHTML = '';

            if (eventos.data && eventos.data.length) {
                eventos.data.forEach(evento => {
                    const imagen = evento.poster_url
                        ? `<img src="${evento.poster_url}" alt="${evento.nombre}"
                                style="width:100%; height:100%; object-fit:cover;"
                                onerror="this.parentElement.innerHTML=''">` : '';
                    container.innerHTML += `
                        <div class="event-card">
                            <div class="event-card-image">${imagen}</div>
                            <div class="event-card-content">
                                <div class="event-card-title">${evento.nombre}</div>
                                <div class="event-card-date">${formatFecha(evento.fecha)}</div>
                                <a href="/eventos/${evento.id}" class="btn btn-primary mt-2">Ver detalles</a>
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
