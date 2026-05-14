@extends('layouts.app')

@section('title', 'Evento | Roig Arena')

@section('styles')
<style>
    .poster-thumb {
        width: 140px;
        height: 100px;
        object-fit: cover;
        object-position: center;
        border-radius: 8px;
        flex-shrink: 0;
        display: block;
    }

    .poster-thumb-placeholder {
        width: 190px;
        height: 150px;
        border-radius: 8px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    @media (max-width: 600px) {
        .poster-thumb, .poster-thumb-placeholder {
            width: 100%;
            height: 160px;
        }
    }

    .sector-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        font-family: inherit;
        background: white;
        cursor: pointer;
        appearance: auto;
        transition: border-color 0.2s;
    }

    .sector-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
    }

    .asiento-btn {
        width: 2.8rem;
        height: 2.8rem;
        border: none;
        border-radius: 5px;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.1s, opacity 0.15s;
    }

    .asiento-btn:hover:not(:disabled) {
        transform: scale(1.15);
    }

    .asiento-btn:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .leyenda {
        display: flex;
        gap: 1.25rem;
        font-size: 0.85rem;
        color: #555;
        flex-wrap: wrap;
    }

    .leyenda-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .leyenda-dot {
        width: 14px;
        height: 14px;
        border-radius: 3px;
        display: inline-block;
    }

    .carrito-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
        font-size: 0.95rem;
    }

    .carrito-row:last-of-type { border-bottom: none; }

    .badge-disponibles {
        display: inline-block;
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')

<div class="card" id="evento-header">
    <div style="display:flex; gap:1.5rem; align-items:flex-start; flex-wrap:wrap;">
        <div style="flex:1; min-width:0;">
            <h1 id="evento-nombre" style="font-size:1.8rem; margin-bottom:0.25rem;">Cargando...</h1>
            <p id="evento-fecha" style="color:#666; font-size:1rem; margin-bottom:0.75rem;"></p>
            <p id="evento-descripcion" style="color:#444; line-height:1.7;"></p>
            <div id="evento-disponibles"></div>
        </div>
        <div id="poster-wrapper"></div>
    </div>
</div>

<div class="grid-2">
    {{-- Columna izquierda: selector de sector --}}
    <div class="card">
        <h2 style="margin-bottom:1rem;">Selecciona un sector</h2>
        <div id="sectores-container">
            <p style="color:#666;">Cargando sectores...</p>
        </div>
        <div id="sector-info" style="margin-top:0.75rem; display:none;">
            <p id="sector-precio-texto" style="font-size:1rem; color:#27ae60; font-weight:600;"></p>
        </div>
    </div>

    {{-- Columna derecha: mapa de asientos --}}
    <div class="card">
        <h2 style="margin-bottom:0.25rem;">
            Asientos
            <span id="sector-nombre-titulo" style="font-weight:400; font-size:0.95rem; color:#666;"></span>
        </h2>
        <div id="asientos-container" style="min-height:80px;">
            <p style="color:#888; margin-top:0.5rem;">Elige un sector para ver los asientos.</p>
        </div>
        <div class="leyenda mt-2">
            <span class="leyenda-item">
                <span class="leyenda-dot" style="background:#27ae60;"></span> Disponible
            </span>
            <span class="leyenda-item">
                <span class="leyenda-dot" style="background:#e74c3c;"></span> Ocupado
            </span>
            <span class="leyenda-item">
                <span class="leyenda-dot" style="background:#3498db;"></span> Seleccionado
            </span>
        </div>
    </div>
</div>

{{-- Carrito --}}
<div class="card">
    <h2>Mi selección</h2>
    <div id="carrito-items" style="margin-top:0.75rem;">
        <p style="color:#888;">No hay asientos seleccionados.</p>
    </div>
    <div id="carrito-total" class="mt-2" style="font-size:1.2rem; font-weight:700; color:#333;"></div>

    <div id="acciones" class="mt-3" style="display:none;">
        @auth
            <button id="btn-reservar" class="btn btn-success" onclick="reservarAsientos()">
                Reservar (15 min)
            </button>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Inicia sesión para reservar</a>
        @endauth
    </div>

    <div id="seccion-compra" class="mt-3" style="display:none;">
        <p style="color:#27ae60; font-weight:600; margin-bottom:0.75rem;">
            Asientos reservados — tienes 15 minutos para confirmar
        </p>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <button id="btn-comprar" class="btn btn-primary" onclick="confirmarCompra()">
                Confirmar compra
            </button>
            <button class="btn btn-secondary" onclick="cancelarReservas()">
                Cancelar
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const eventoId = {{ $eventoId }};
    let selectedAsientos = [];
    let reservasIds     = [];
    let preciosPorSector = {};
    let currentSectorId = null;
    let currentSectorNombre = null;

    document.addEventListener('DOMContentLoaded', cargarEvento);

    // ── Cargar evento ─────────────────────────────────────────────────
    async function cargarEvento() {
        try {
            const res  = await fetch(`/api/eventos/${eventoId}`);
            const json = await res.json();
            const { evento, sectores_disponibles, asientos_disponibles } = json.data;

            // Poster
            if (evento.poster_url) {
                document.getElementById('poster-wrapper').innerHTML =
                    `<img src="${evento.poster_url}" alt="${evento.nombre}" class="poster-thumb"
                          style="width:150px;height:110px;object-fit:cover;object-position:center;border-radius:8px;flex-shrink:0;display:block;"
                          onerror="this.outerHTML='<div class=poster-thumb-placeholder></div>'">`;
            } else {
                document.getElementById('poster-wrapper').innerHTML =
                    `<div class="poster-thumb-placeholder"></div>`;
            }

            const fecha = evento.fecha ? evento.fecha.substring(0, 10).split('-').reverse().join('/') : '';
            const hora  = evento.hora  ? evento.hora.substring(0, 5) : '';

            document.getElementById('evento-nombre').textContent  = evento.nombre;
            document.getElementById('evento-fecha').textContent   =
                `${fecha}${hora ? ' — ' + hora : ''}`;
            document.getElementById('evento-descripcion').textContent =
                evento.descripcion_larga || evento.descripcion_corta || '';
            document.getElementById('evento-disponibles').innerHTML =
                `<span class="badge-disponibles">${asientos_disponibles} asientos disponibles</span>`;

            // Guardar precios por sectorId
            sectores_disponibles.forEach(s => {
                preciosPorSector[s.id] = s.pivot ? parseFloat(s.pivot.precio) : 0;
            });

            renderSectores(sectores_disponibles);
        } catch (e) {
            document.getElementById('evento-nombre').textContent = 'Error cargando el evento';
        }
    }

    // ── Renderizar selector de sector ────────────────────────────────
    function renderSectores(sectores) {
        const container = document.getElementById('sectores-container');
        if (!sectores.length) {
            container.innerHTML = '<p style="color:#666;">No hay sectores disponibles.</p>';
            return;
        }

        const options = sectores.map(s => {
            const precio = s.pivot
                ? parseFloat(s.pivot.precio).toFixed(2).replace('.', ',') + ' €'
                : '';
            return `<option value="${s.id}" data-nombre="${s.nombre}" data-precio="${s.pivot?.precio ?? 0}">
                ${s.nombre}${precio ? ' — ' + precio : ''}
            </option>`;
        }).join('');

        container.innerHTML = `
            <select id="sector-select" class="sector-select" onchange="onSectorChange(this)">
                <option value="">— Elige un sector —</option>
                ${options}
            </select>
        `;
    }

    function onSectorChange(select) {
        const sectorId = parseInt(select.value);
        if (!sectorId) return;

        const opt    = select.options[select.selectedIndex];
        const nombre = opt.getAttribute('data-nombre');
        const precio = parseFloat(opt.getAttribute('data-precio'));

        currentSectorId     = sectorId;
        currentSectorNombre = nombre;

        const infoEl = document.getElementById('sector-info');
        document.getElementById('sector-precio-texto').textContent =
            `Precio: ${precio.toFixed(2).replace('.', ',')} €/asiento`;
        infoEl.style.display = 'block';

        cargarAsientos(sectorId, nombre);
    }

    // ── Cargar asientos del sector ────────────────────────────────────
    async function cargarAsientos(sectorId, sectorNombre) {
        document.getElementById('sector-nombre-titulo').textContent = `— ${sectorNombre}`;
        const container = document.getElementById('asientos-container');
        container.innerHTML = '<p style="color:#888;">Cargando asientos...</p>';

        try {
            const res  = await fetch(`/api/eventos/${eventoId}/sectores/${sectorId}/asientos`);
            const json = await res.json();
            const asientos = json.data.asientos; // {sector, precio, asientos:[{id,fila,numero,disponible}]}

            if (!asientos || !asientos.length) {
                container.innerHTML = '<p style="color:#888;">No hay asientos en este sector.</p>';
                return;
            }

            // Enriquecer con info del sector para el carrito
            const enriched = asientos.map(a => ({
                ...a,
                sector_id:     sectorId,
                sector_nombre: sectorNombre,
            }));

            // Agrupar por fila
            const filas = {};
            enriched.forEach(a => {
                if (!filas[a.fila]) filas[a.fila] = [];
                filas[a.fila].push(a);
            });

            container.innerHTML = Object.entries(filas)
                .sort(([fa], [fb]) => fa.localeCompare(fb, undefined, { numeric: true }))
                .map(([fila, asientosF]) => {
                    const btns = asientosF
                        .sort((a, b) => a.numero - b.numero)
                        .map(a => {
                            const selected  = selectedAsientos.some(s => s.id === a.id);
                            const color     = selected ? '#3498db' : (a.disponible ? '#27ae60' : '#e74c3c');
                            const disabled  = (!a.disponible && !selected) ? 'disabled' : '';
                            const dataAttr  = encodeURIComponent(JSON.stringify(a));
                            return `<button
                                type="button"
                                class="asiento-btn"
                                ${disabled}
                                onclick="toggleAsiento(decodeAndParse(this))"
                                data-asiento="${dataAttr}"
                                style="background:${color};"
                                title="Fila ${a.fila} - Nº ${a.numero}"
                            >${a.numero}</button>`;
                        }).join('');

                    return `<div style="margin-bottom:0.75rem;">
                        <small style="color:#888; font-weight:600;">Fila ${fila}</small>
                        <div style="display:flex; flex-wrap:wrap; gap:0.4rem; margin-top:0.3rem;">${btns}</div>
                    </div>`;
                }).join('');

        } catch {
            container.innerHTML = '<p style="color:#e74c3c;">Error cargando asientos.</p>';
        }
    }

    function decodeAndParse(btn) {
        return JSON.parse(decodeURIComponent(btn.getAttribute('data-asiento')));
    }

    // ── Toggle asiento ────────────────────────────────────────────────
    function toggleAsiento(asiento) {
        const idx = selectedAsientos.findIndex(a => a.id === asiento.id);
        if (idx > -1) {
            selectedAsientos.splice(idx, 1);
        } else {
            if (!asiento.disponible) return;
            selectedAsientos.push(asiento);
        }
        actualizarCarrito();
        // Re-render asiento buttons color in place
        document.querySelectorAll('.asiento-btn').forEach(btn => {
            const a = JSON.parse(decodeURIComponent(btn.getAttribute('data-asiento')));
            const selected = selectedAsientos.some(s => s.id === a.id);
            btn.style.background = selected ? '#3498db' : (a.disponible ? '#27ae60' : '#e74c3c');
        });
    }

    // ── Actualizar carrito ────────────────────────────────────────────
    function actualizarCarrito() {
        const container = document.getElementById('carrito-items');
        const totalEl   = document.getElementById('carrito-total');
        const acciones  = document.getElementById('acciones');

        if (!selectedAsientos.length) {
            container.innerHTML = '<p style="color:#888;">No hay asientos seleccionados.</p>';
            totalEl.textContent = '';
            acciones.style.display = 'none';
            return;
        }

        let total = 0;
        container.innerHTML = selectedAsientos.map(a => {
            const precio = preciosPorSector[a.sector_id] ?? 0;
            total += precio;
            const precioStr = precio.toFixed(2).replace('.', ',');
            const dataAttr  = encodeURIComponent(JSON.stringify(a));
            return `<div class="carrito-row">
                <span>Fila <strong>${a.fila}</strong> — Nº <strong>${a.numero}</strong>
                    <span style="color:#888; font-size:0.85rem;">(${a.sector_nombre})</span>
                </span>
                <span style="display:flex; align-items:center; gap:0.75rem;">
                    <strong>${precioStr} €</strong>
                    <button type="button"
                        onclick="toggleAsiento(JSON.parse(decodeURIComponent('${dataAttr}')))"
                        style="background:none; border:none; color:#e74c3c; cursor:pointer; font-size:1.1rem; line-height:1;">&times;</button>
                </span>
            </div>`;
        }).join('');

        totalEl.innerHTML = `Total: <span style="color:#667eea;">${total.toFixed(2).replace('.', ',')} €</span>`;
        acciones.style.display = 'block';
    }

    // ── Reservar asientos ─────────────────────────────────────────────
    async function reservarAsientos() {
        const btn = document.getElementById('btn-reservar');
        btn.disabled    = true;
        btn.textContent = 'Reservando...';
        reservasIds = [];

        try {
            for (const asiento of selectedAsientos) {
                const res  = await fetch('/api/reservas', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${window.apiToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ evento_id: eventoId, asiento_id: asiento.id }),
                });
                const json = await res.json();
                if (!res.ok) {
                    alert(json.error || json.message || `Error reservando fila ${asiento.fila} nº ${asiento.numero}`);
                    btn.disabled    = false;
                    btn.textContent = 'Reservar (15 min)';
                    return;
                }
                reservasIds.push(json.data.id);
            }
            document.getElementById('acciones').style.display     = 'none';
            document.getElementById('seccion-compra').style.display = 'block';
        } catch {
            alert('Error de conexión al reservar');
            btn.disabled    = false;
            btn.textContent = 'Reservar (15 min)';
        }
    }

    // ── Confirmar compra ──────────────────────────────────────────────
    async function confirmarCompra() {
        const btn = document.getElementById('btn-comprar');
        btn.disabled    = true;
        btn.textContent = 'Procesando...';

        try {
            const res  = await fetch('/api/compras', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ reservas: reservasIds }),
            });
            const json = await res.json();
            if (res.ok) {
                window.location.href = '/mis-entradas';
            } else {
                alert(json.error || 'Error al procesar la compra');
                btn.disabled    = false;
                btn.textContent = 'Confirmar compra';
            }
        } catch {
            alert('Error de conexión al comprar');
            btn.disabled    = false;
            btn.textContent = 'Confirmar compra';
        }
    }

    // ── Cancelar reservas ─────────────────────────────────────────────
    async function cancelarReservas() {
        if (!confirm('¿Cancelar todas las reservas?')) return;
        for (const id of reservasIds) {
            await fetch(`/api/reservas/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Accept': 'application/json',
                },
            });
        }
        selectedAsientos = [];
        reservasIds      = [];
        document.getElementById('seccion-compra').style.display = 'none';
        actualizarCarrito();
        cargarEvento();
    }
</script>
@endsection
