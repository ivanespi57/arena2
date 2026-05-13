@extends('layouts.app')

@section('title', 'Dashboard | Roig Arena')

@section('content')
<h1>Mi Dashboard</h1>

<div class="grid-2 mt-3">
    <div class="card">
        <h2>Mis Reservas</h2>
        <div id="reservas-container">
            <p>Cargando...</p>
        </div>
    </div>

    <div class="card">
        <h2>Mis Entradas</h2>
        <div id="entradas-container">
            <p>Cargando...</p>
        </div>
    </div>
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

        // Cargar reservas
        try {
            const response = await fetch('/api/reservas', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            const container = document.getElementById('reservas-container');

            if (data.data && data.data.length) {
                container.innerHTML = data.data.map(res => `
                    <div class="card mt-2">
                        <p><strong>Evento:</strong> ${res.evento?.nombre || 'N/A'}</p>
                        <p><strong>Asiento:</strong> ${res.asiento?.numero || 'N/A'}</p>
                        <p><strong>Estado:</strong> ${res.estado_asiento?.estado || 'N/A'}</p>
                        <button onclick="cancelarReserva(${res.id})" class="btn btn-danger mt-1">Cancelar</button>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p>No tienes reservas</p>';
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('reservas-container').innerHTML = '<p>Error cargando reservas</p>';
        }

        // Cargar entradas
        try {
            const response = await fetch('/api/entradas', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            const container = document.getElementById('entradas-container');

            if (data.data && data.data.length) {
                container.innerHTML = data.data.map(entrada => `
                    <div class="card mt-2">
                        <p><strong>Evento:</strong> ${entrada.evento?.nombre || 'N/A'}</p>
                        <p><strong>Asiento:</strong> ${entrada.asiento?.numero || 'N/A'}</p>
                        <p><strong>QR:</strong> <a href="/entradas/${entrada.id}" target="_blank">Ver QR</a></p>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p>No tienes entradas</p>';
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('entradas-container').innerHTML = '<p>Error cargando entradas</p>';
        }
    });

    async function cancelarReserva(id) {
        if (confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
            try {
                const response = await fetch(`/api/reservas/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    alert('Reserva cancelada');
                    location.reload();
                } else {
                    alert('Error al cancelar la reserva');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
</script>
@endsection
