@extends('layouts.app')

@section('title', 'Panel Admin | Roig Arena')

@section('styles')
<style>
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
    }

    .admin-table th {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #e0e0e0;
        color: #555;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .admin-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tr:hover td { background: #fafafa; }

    .badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .badge-active   { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }

    .actions { display: flex; gap: 0.4rem; flex-wrap: wrap; }

    .btn-sm {
        padding: 0.3rem 0.75rem;
        font-size: 0.82rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-family: inherit;
        font-weight: 600;
        transition: opacity 0.15s;
    }

    .btn-sm:hover { opacity: 0.85; }
    .btn-sm-edit    { background: #667eea; color: white; }
    .btn-sm-delete  { background: #e74c3c; color: white; }
    .btn-sm-view    { background: #6c757d; color: white; }
</style>
@endsection

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h1 style="margin-bottom:0.25rem;">Panel de Administración</h1>
        <p style="color:#666; font-size:0.9rem;">Gestión de eventos y sectores</p>
    </div>
</div>

{{-- ── Eventos ── --}}
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h2>Eventos</h2>
        <a href="{{ route('eventos.create') }}" class="btn btn-primary" style="font-size:0.9rem; padding:0.5rem 1.1rem;">
            + Crear evento
        </a>
    </div>

    @if(empty($eventos))
        <p style="color:#888;">No hay eventos registrados.</p>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventos as $evento)
                    <tr>
                        <td>
                            <strong>{{ $evento['nombre'] }}</strong>
                            @if(!empty($evento['descripcion_corta']))
                                <br><small style="color:#888;">{{ Str::limit($evento['descripcion_corta'], 60) }}</small>
                            @endif
                        </td>
                        <td>{{ $evento['fecha'] }}</td>
                        <td>{{ $evento['hora'] ?? '—' }}</td>
                        <td class="actions">
                            <a href="{{ route('eventos.show', $evento['id']) }}" class="btn-sm btn-sm-view">Ver</a>
                            <a href="{{ route('admin.eventos.edit', $evento['id']) }}" class="btn-sm btn-sm-edit">Editar</a>
                            <button
                                type="button"
                                class="btn-sm btn-sm-delete"
                                onclick="eliminarEvento({{ $evento['id'] }}, '{{ addslashes($evento['nombre']) }}')">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ── Sectores ── --}}
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h2>Sectores</h2>
        <a href="{{ route('admin.sectores.create') }}" class="btn btn-primary" style="font-size:0.9rem; padding:0.5rem 1.1rem;">
            + Crear sector
        </a>
    </div>

    @if(empty($sectores))
        <p style="color:#888;">No hay sectores registrados.</p>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sectores as $sector)
                    <tr>
                        <td style="color:#888; font-size:0.85rem;">{{ $sector['id'] }}</td>
                        <td><strong>{{ $sector['nombre'] }}</strong></td>
                        <td style="color:#666; font-size:0.875rem;">
                            {{ Str::limit($sector['descripcion'] ?? '—', 60) }}
                        </td>
                        <td>
                            <span class="badge {{ ($sector['activo'] ?? true) ? 'badge-active' : 'badge-inactive' }}">
                                {{ ($sector['activo'] ?? true) ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="actions">
                            <a href="{{ route('admin.sectores.edit', $sector['id']) }}" class="btn-sm btn-sm-edit">Editar</a>
                            <button
                                type="button"
                                class="btn-sm btn-sm-delete"
                                onclick="eliminarSector({{ $sector['id'] }}, '{{ addslashes($sector['nombre']) }}')">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div id="msg-global" style="display:none; position:fixed; bottom:1.5rem; right:1.5rem; padding:1rem 1.5rem; border-radius:8px; font-weight:600; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:999;"></div>

@endsection

@section('scripts')
<script>
    function mostrarMsg(texto, ok = true) {
        const el = document.getElementById('msg-global');
        el.textContent  = texto;
        el.style.background = ok ? '#d4edda' : '#f8d7da';
        el.style.color      = ok ? '#155724' : '#721c24';
        el.style.display    = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 3500);
    }

    async function eliminarEvento(id, nombre) {
        if (!confirm(`¿Eliminar el evento "${nombre}"? Esta acción no se puede deshacer.`)) return;

        const res = await fetch(`/api/admin/eventos/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${window.apiToken}`,
                'Accept': 'application/json',
            },
        });

        if (res.ok || res.status === 204) {
            mostrarMsg(`Evento "${nombre}" eliminado.`);
            setTimeout(() => location.reload(), 1200);
        } else {
            const json = await res.json().catch(() => ({}));
            mostrarMsg(json.message || 'Error al eliminar el evento.', false);
        }
    }

    async function eliminarSector(id, nombre) {
        if (!confirm(`¿Eliminar el sector "${nombre}"? Esta acción no se puede deshacer.`)) return;

        const res = await fetch(`/api/admin/sectores/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${window.apiToken}`,
                'Accept': 'application/json',
            },
        });

        if (res.ok || res.status === 204) {
            mostrarMsg(`Sector "${nombre}" eliminado.`);
            setTimeout(() => location.reload(), 1200);
        } else {
            const json = await res.json().catch(() => ({}));
            mostrarMsg(json.message || 'Error al eliminar el sector.', false);
        }
    }
</script>
@endsection
