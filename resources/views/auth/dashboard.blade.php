@extends('layouts.app')

@section('title', 'Dashboard | Roig Arena')

@section('content')
<h1>Bienvenido, {{ auth()->user()->nombre }}</h1>

<div class="grid-2 mt-3">
    <div class="card">
        <h2>Mis Reservas activas</h2>
        <div id="reservas-container">
            <p>Cargando...</p>
        </div>
    </div>

    <div class="card">
        <h2>Últimas entradas</h2>
        <div id="entradas-container">
            <p>Cargando...</p>
        </div>
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary mt-2">Ver todas mis entradas</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {

        // Cargar reservas activas
        try {
            const res = await fetch('/api/reservas', {
                headers: { 'Authorization': `Bearer ${window.apiToken}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const container = document.getElementById('reservas-container');

            if (data.data && data.data.length) {
                container.innerHTML = data.data.map(r => `
                    <div class="card mt-2">
                        <p><strong>${r.evento?.nombre || 'N/A'}</strong></p>
                        <p>Asiento: ${r.asiento?.nombre || 'N/A'} &mdash; ${r.precio}</p>
                        <p>Expira en: ${r.tiempo_restante_minutos} min</p>
                        <div style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                            <button onclick="comprarReserva(${r.id})" class="btn btn-success">Comprar</button>
                            <button onclick="cancelarReserva(${r.id})" class="btn btn-danger">Cancelar</button>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p>No tienes reservas activas</p>';
            }
        } catch {
            document.getElementById('reservas-container').innerHTML = '<p>Error cargando reservas</p>';
        }

        // Cargar últimas entradas
        try {
            const res = await fetch('/api/entradas', {
                headers: { 'Authorization': `Bearer ${window.apiToken}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const container = document.getElementById('entradas-container');

            if (data.data && data.data.length) {
                container.innerHTML = data.data.slice(0, 3).map(e => `
                    <div class="card mt-2">
                        <p><strong>${e.evento?.nombre || 'N/A'}</strong></p>
                        <p>${e.asiento?.nombre || 'N/A'} &mdash; ${e.precio_pagado}</p>
                        <a href="/entradas/${e.id}" class="btn btn-secondary mt-1">Ver entrada</a>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p>No tienes entradas todavía</p>';
            }
        } catch {
            document.getElementById('entradas-container').innerHTML = '<p>Error cargando entradas</p>';
        }
    });

    async function cancelarReserva(id) {
        if (!confirm('¿Cancelar esta reserva?')) return;
        try {
            const res = await fetch(`/api/reservas/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${window.apiToken}`, 'Accept': 'application/json' }
            });
            if (res.ok) location.reload();
            else alert('Error al cancelar la reserva');
        } catch {
            alert('Error de conexión');
        }
    }

    async function comprarReserva(id) {
        if (!confirm('¿Confirmar la compra de esta reserva?')) return;
        try {
            const res = await fetch('/api/compras', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reservas: [id] })
            });
            const data = await res.json();
            if (res.ok) {
                alert('¡Compra realizada! Ya puedes ver tu entrada.');
                location.reload();
            } else {
                alert(data.error || 'Error al procesar la compra');
            }
        } catch {
            alert('Error de conexión');
        }
    }
</script>
@endsection
