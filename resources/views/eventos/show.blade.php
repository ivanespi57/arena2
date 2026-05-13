@extends('layouts.app')

@section('title', 'Evento | Roig Arena')

@section('content')

<div class="card" id="evento-header">
    <h1 id="evento-nombre">Cargando...</h1>
    <p id="evento-fecha" style="color:#666;"></p>
    <p id="evento-descripcion" class="mt-2"></p>
    <p id="evento-disponibles" class="mt-1" style="color:#27ae60; font-weight:600;"></p>
</div>

<div class="grid-2">
    <div class="card">
        <h2>Sectores disponibles</h2>
        <div id="sectores-container"><p>Cargando...</p></div>
    </div>

    <div class="card">
        <h2>Asientos <span id="sector-nombre-titulo" style="font-weight:400; font-size:1rem;"></span></h2>
        <div id="asientos-container" style="display:flex; flex-wrap:wrap; gap:0.5rem;">
            <p>Selecciona un sector primero</p>
        </div>
        <div class="mt-2" style="display:flex; gap:1rem; font-size:0.85rem;">
            <span>🟢 Disponible</span>
            <span>🔴 Ocupado</span>
            <span>🔵 Seleccionado</span>
        </div>
    </div>
</div>

<div class="card">
    <h2>Mi selección</h2>
    <div id="carrito-items"><p>No hay asientos seleccionados</p></div>
    <div id="carrito-total" class="mt-2" style="font-size:1.1rem; font-weight:600;"></div>

    <div id="acciones" class="mt-3" style="display:none;">
        @auth
            <button id="btn-reservar" class="btn btn-success" onclick="reservarAsientos()">
                Reservar asientos (15 min)
            </button>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Inicia sesión para reservar</a>
        @endauth
    </div>

    <div id="seccion-compra" class="mt-3" style="display:none;">
        <p style="color:#27ae60; font-weight:600;">✅ Asientos reservados durante 15 minutos</p>
        <button id="btn-comprar" class="btn btn-primary mt-2" onclick="confirmarCompra()">
            Confirmar compra
        </button>
        <button class="btn btn-secondary mt-2" style="margin-left:0.5rem;" onclick="cancelarReservas()">
            Cancelar reservas
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const eventoId = {{ $eventoId }};
    let selectedAsientos = [];
    let reservasIds = [];
    let preciosPorSector = {};

    document.addEventListener('DOMContentLoaded', cargarEvento);

    async function cargarEvento() {
        try {
            const res = await fetch(`/api/eventos/${eventoId}`);
            const json = await res.json();
            const { evento, sectores_disponibles, asientos_disponibles } = json.data;

            document.getElementById('evento-nombre').textContent = evento.nombre;
            document.getElementById('evento-fecha').textContent =
                `${evento.fecha}${evento.hora ? ' — ' + evento.hora : ''}`;
            document.getElementById('evento-descripcion').textContent =
                evento.descripcion_larga || evento.descripcion_corta || '';
            document.getElementById('evento-disponibles').textContent =
                `${asientos_disponibles} asientos disponibles`;

            // Guardar precios por sector para mostrarlos en el carrito
            sectores_disponibles.forEach(s => {
                preciosPorSector[s.id] = s.pivot ? parseFloat(s.pivot.precio) : 0;
            });

            renderSectores(sectores_disponibles);
        } catch {
            document.getElementById('evento-nombre').textContent = 'Error cargando evento';
        }
    }

    function renderSectores(sectores) {
        const container = document.getElementById('sectores-container');
        if (!sectores.length) {
            container.innerHTML = '<p>No hay sectores disponibles</p>';
            return;
        }
        container.innerHTML = sectores.map(s => `
            <button
                class="btn btn-secondary mt-1"
                style="width:100%; text-align:left;"
                onclick="cargarAsientos(${s.id}, '${s.nombre}')"
            >
                <strong>${s.nombre}</strong>
                ${s.pivot ? ` — ${parseFloat(s.pivot.precio).toFixed(2).replace('.', ',')} €` : ''}
            </button>
        `).join('');
    }

    async function cargarAsientos(sectorId, sectorNombre) {
        document.getElementById('sector-nombre-titulo').textContent = `— ${sectorNombre}`;
        const container = document.getElementById('asientos-container');
        container.innerHTML = '<p>Cargando asientos...</p>';

        try {
            const res = await fetch(`/api/eventos/${eventoId}/sectores/${sectorId}/asientos`);
            const json = await res.json();
            const asientos = json.data;

            if (!asientos.length) {
                container.innerHTML = '<p>No hay asientos en este sector</p>';
                return;
            }

            // Agrupar por fila
            const filas = {};
            asientos.forEach(a => {
                if (!filas[a.fila]) filas[a.fila] = [];
                filas[a.fila].push(a);
            });

            container.innerHTML = Object.entries(filas).map(([fila, asientosF]) => `
                <div style="width:100%; margin-bottom:0.75rem;">
                    <small style="color:#666;">Fila ${fila}</small><br>
                    <div style="display:flex; flex-wrap:wrap; gap:0.4rem; margin-top:0.25rem;">
                        ${asientosF.map(a => {
                            const seleccionado = selectedAsientos.find(s => s.id === a.id);
                            const color = seleccionado ? '#3498db' : (a.disponible ? '#27ae60' : '#e74c3c');
                            const disabled = !a.disponible && !seleccionado ? 'disabled' : '';
                            return `<button
                                type="button"
                                ${disabled}
                                onclick="toggleAsiento(${JSON.stringify(a).replace(/"/g, '&quot;')})"
                                style="width:2.5rem; height:2.5rem; background:${color}; color:white; border:none; border-radius:4px; cursor:${disabled ? 'not-allowed' : 'pointer'}; font-size:0.75rem;"
                                title="Asiento ${a.numero}"
                            >${a.numero}</button>`;
                        }).join('')}
                    </div>
                </div>
            `).join('');
        } catch {
            container.innerHTML = '<p>Error cargando asientos</p>';
        }
    }

    function toggleAsiento(asiento) {
        const idx = selectedAsientos.findIndex(a => a.id === asiento.id);
        if (idx > -1) {
            selectedAsientos.splice(idx, 1);
        } else {
            selectedAsientos.push(asiento);
        }
        actualizarCarrito();
    }

    function actualizarCarrito() {
        const container = document.getElementById('carrito-items');
        const totalEl   = document.getElementById('carrito-total');
        const acciones  = document.getElementById('acciones');

        if (!selectedAsientos.length) {
            container.innerHTML = '<p>No hay asientos seleccionados</p>';
            totalEl.textContent = '';
            acciones.style.display = 'none';
            return;
        }

        let total = 0;
        container.innerHTML = selectedAsientos.map(a => {
            const precio = preciosPorSector[a.sector_id] || 0;
            total += precio;
            return `
                <div style="display:flex; justify-content:space-between; align-items:center; padding:0.5rem 0; border-bottom:1px solid #eee;">
                    <span>${a.nombre_completo} (${a.sector})</span>
                    <span>${precio.toFixed(2).replace('.', ',')} €
                        <button type="button" onclick="toggleAsiento(${JSON.stringify(a).replace(/"/g, '&quot;')})"
                            style="background:none; border:none; color:#e74c3c; cursor:pointer; font-size:1.1rem; margin-left:0.5rem;">✕</button>
                    </span>
                </div>
            `;
        }).join('');

        totalEl.textContent = `Total: ${total.toFixed(2).replace('.', ',')} €`;
        acciones.style.display = 'block';
    }

    async function reservarAsientos() {
        const btn = document.getElementById('btn-reservar');
        btn.disabled = true;
        btn.textContent = 'Reservando...';
        reservasIds = [];

        try {
            for (const asiento of selectedAsientos) {
                const res = await fetch('/api/reservas', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${window.apiToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ evento_id: eventoId, asiento_id: asiento.id })
                });
                const json = await res.json();
                if (!res.ok) {
                    alert(json.error || `Error reservando asiento ${asiento.numero}`);
                    btn.disabled = false;
                    btn.textContent = 'Reservar asientos (15 min)';
                    return;
                }
                reservasIds.push(json.data.id);
            }

            document.getElementById('acciones').style.display = 'none';
            document.getElementById('seccion-compra').style.display = 'block';
        } catch {
            alert('Error de conexión al reservar');
            btn.disabled = false;
            btn.textContent = 'Reservar asientos (15 min)';
        }
    }

    async function confirmarCompra() {
        const btn = document.getElementById('btn-comprar');
        btn.disabled = true;
        btn.textContent = 'Procesando...';

        try {
            const res = await fetch('/api/compras', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reservas: reservasIds })
            });
            const json = await res.json();
            if (res.ok) {
                window.location.href = '/mis-entradas';
            } else {
                alert(json.error || 'Error al procesar la compra');
                btn.disabled = false;
                btn.textContent = 'Confirmar compra';
            }
        } catch {
            alert('Error de conexión al comprar');
            btn.disabled = false;
            btn.textContent = 'Confirmar compra';
        }
    }

    async function cancelarReservas() {
        if (!confirm('¿Cancelar todas las reservas?')) return;
        for (const id of reservasIds) {
            await fetch(`/api/reservas/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${window.apiToken}`, 'Accept': 'application/json' }
            });
        }
        selectedAsientos = [];
        reservasIds = [];
        document.getElementById('seccion-compra').style.display = 'none';
        actualizarCarrito();
        cargarEvento();
    }
</script>
@endsection
