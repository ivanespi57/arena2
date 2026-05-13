@extends('layouts.app')

@section('title', 'Detalle Evento | Roig Arena')

@section('content')
<div class="card">
    <h1 id="evento-nombre">Cargando...</h1>
    <p id="evento-fecha"></p>
    <p id="evento-descripcion"></p>
</div>

<div class="grid-2">
    <div class="card">
        <h2>Sectores</h2>
        <div id="sectores-container">
            <p>Cargando...</p>
        </div>
    </div>

    <div class="card">
        <h2>Asientos disponibles</h2>
        <div id="asientos-container">
            <p>Selecciona un sector primero</p>
        </div>
    </div>
</div>

<div class="card">
    <h2>Mi selección</h2>
    <div id="carrito">
        <p>No hay asientos seleccionados</p>
    </div>
    <button id="btn-reservar" class="btn btn-success mt-2" style="display:none;">Reservar asientos</button>
</div>

@endsection

@section('scripts')
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const eventoId = window.location.pathname.split('/')[2];
    const token = localStorage.getItem('token');
    let selectedAsientos = [];

    document.addEventListener('DOMContentLoaded', async () => {
        await cargarEvento();
        await cargarSectores();
    });

    async function cargarEvento() {
        try {
            const response = await fetch(`/api/eventos/${eventoId}`);
            const data = await response.json();
            const evento = data.data;

            document.getElementById('evento-nombre').textContent = evento.nombre;
            document.getElementById('evento-fecha').textContent = `Fecha: ${new Date(evento.fecha).toLocaleDateString('es-ES')}`;
            document.getElementById('evento-descripcion').textContent = evento.descripcion || '';
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function cargarSectores() {
        try {
            const response = await fetch(`/api/eventos/${eventoId}/sectores`);
            const data = await response.json();

            const container = document.getElementById('sectores-container');
            container.innerHTML = '';

            if (data.data && data.data.length) {
                data.data.forEach(sector => {
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-secondary mt-1';
                    btn.textContent = `${sector.nombre} - $${sector.precio}`;
                    btn.onclick = () => cargarAsientos(sector.id);
                    container.appendChild(btn);
                });
            } else {
                container.innerHTML = '<p>No hay sectores</p>';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function cargarAsientos(sectorId) {
        try {
            const response = await fetch(`/api/eventos/${eventoId}/sectores/${sectorId}/asientos`);
            const data = await response.json();

            const container = document.getElementById('asientos-container');
            container.innerHTML = '';

            if (data.data && data.data.length) {
                data.data.forEach(asiento => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `btn ${asiento.disponible ? 'btn-secondary' : 'btn-danger'} mt-1`;
                    btn.textContent = `Asiento ${asiento.numero}`;
                    btn.disabled = !asiento.disponible;

                    if (asiento.disponible) {
                        btn.onclick = () => seleccionarAsiento(asiento);
                    }

                    container.appendChild(btn);
                });
            } else {
                container.innerHTML = '<p>No hay asientos en este sector</p>';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function seleccionarAsiento(asiento) {
        const index = selectedAsientos.findIndex(a => a.id === asiento.id);
        if (index > -1) {
            selectedAsientos.splice(index, 1);
        } else {
            selectedAsientos.push(asiento);
        }
        actualizarCarrito();
    }

    function actualizarCarrito() {
        const container = document.getElementById('carrito');
        const btnReservar = document.getElementById('btn-reservar');

        if (selectedAsientos.length > 0) {
            container.innerHTML = selectedAsientos.map(a => `
                <div class="mt-2">
                    <strong>Asiento ${a.numero}</strong>
                    <button type="button" onclick="removerAsiento(${a.id})" class="btn btn-danger">Remover</button>
                </div>
            `).join('');
            btnReservar.style.display = 'block';
        } else {
            container.innerHTML = '<p>No hay asientos seleccionados</p>';
            btnReservar.style.display = 'none';
        }
    }

    function removerAsiento(id) {
        selectedAsientos = selectedAsientos.filter(a => a.id !== id);
        actualizarCarrito();
    }

    document.getElementById('btn-reservar').addEventListener('click', async () => {
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            for (const asiento of selectedAsientos) {
                const response = await fetch('/api/reservas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        evento_id: eventoId,
                        asiento_id: asiento.id,
                        estado_asiento_id: asiento.estado_asiento_id
                    })
                });

                if (!response.ok) {
                    alert('Error al reservar asientos');
                    return;
                }
            }

            alert('Reservas realizadas exitosamente');
            window.location.href = '/dashboard';
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la reserva');
        }
    });
</script>
@endsection
