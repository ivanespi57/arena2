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
        <h2>Ultimas entradas</h2>
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

        try { // reservas activas
            const res  = await fetch('/api/reservas', {
                headers: { 'Authorization': `Bearer ${window.apiToken}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const container = document.getElementById('reservas-container');

            if (data.data && data.data.length) {
                container.innerHTML = data.data.map(r => `
                    <div style="padding:0.75rem 0; border-bottom:1px solid #eee;">
                        <p style="font-weight:600; margin-bottom:0.25rem;">${r.evento?.nombre || r.evento || 'Evento'}</p>
                        <p style="color:#666; font-size:0.9rem; margin-bottom:0.4rem;">
                            ${r.asiento?.nombre || r.asiento || 'Asiento'} &mdash; ${r.precio || ''}
                        </p>
                        <p style="color:#e67e22; font-size:0.85rem; margin-bottom:0.5rem;">
                            Expira en: ${r.tiempo_restante_minutos ?? '?'} min
                        </p>
                        <div style="display:flex; gap:0.5rem;">
                            <button onclick="comprarReserva(${r.id})" class="btn btn-success" style="font-size:0.85rem; padding:0.4rem 0.9rem;">Comprar</button>
                            <button onclick="cancelarReserva(${r.id})" class="btn btn-danger"  style="font-size:0.85rem; padding:0.4rem 0.9rem;">Cancelar</button>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p style="color:#888;">No tienes reservas activas</p>';
            }
        } catch {
            document.getElementById('reservas-container').innerHTML = '<p>Error cargando reservas</p>';
        }

        try { // últimas entradas — la API devuelve estructura plana: { id, evento, fecha, hora, asiento, precio, valida }
            const res  = await fetch('/api/entradas', {
                headers: { 'Authorization': `Bearer ${window.apiToken}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const container = document.getElementById('entradas-container');

            if (data.data && data.data.length) {
                container.innerHTML = data.data.slice(0, 3).map(e => {
                    const hora = e.hora
                        ? (e.hora.length > 5 ? e.hora.substring(11, 16) : e.hora.substring(0, 5))
                        : '';
                    return `
                        <div style="padding:0.75rem 0; border-bottom:1px solid #eee;">
                            <p style="font-weight:600; margin-bottom:0.2rem;">${e.evento || 'Evento'}</p>
                            <p style="color:#666; font-size:0.9rem; margin-bottom:0.2rem;">
                                ${e.fecha || ''}${hora ? ' — ' + hora : ''}
                            </p>
                            <p style="color:#555; font-size:0.9rem; margin-bottom:0.4rem;">
                                ${e.asiento || 'Asiento'} &mdash; <strong style="color:#27ae60;">${e.precio || ''}</strong>
                            </p>
                            <a href="/entradas/${e.id}" class="btn btn-secondary" style="font-size:0.85rem; padding:0.4rem 0.9rem;">Ver entrada</a>
                        </div>
                    `;
                }).join('');
            } else {
                container.innerHTML = '<p style="color:#888;">No tienes entradas todavia</p>';
            }
        } catch {
            document.getElementById('entradas-container').innerHTML = '<p>Error cargando entradas</p>';
        }
    });

    async function cancelarReserva(id) {
        if (!confirm('Cancelar esta reserva?')) return;
        try {
            const res = await fetch(`/api/reservas/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${window.apiToken}`, 'Accept': 'application/json' }
            });
            if (res.ok) location.reload();
            else alert('Error al cancelar la reserva');
        } catch {
            alert('Error de conexion');
        }
    }

    async function comprarReserva(id) {
        if (!confirm('Confirmar la compra de esta reserva?')) return;
        try {
            const res  = await fetch('/api/compras', {
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
                alert('Compra realizada. Ya puedes ver tu entrada.');
                location.reload();
            } else {
                alert(data.error || data.message || 'Error al procesar la compra');
            }
        } catch {
            alert('Error de conexion');
        }
    }
</script>
@endsection
