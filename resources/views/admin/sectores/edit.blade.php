@extends('layouts.app')

@section('title', 'Editar Sector | Admin')

@section('content')
<div class="card" style="max-width:600px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1>Editar sector</h1>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary" style="font-size:0.9rem;">← Volver</a>
    </div>

    <div id="loading" style="color:#666; padding:1rem 0;">Cargando datos del sector...</div>

    <form id="edit-sector-form" style="display:none;">
        <div class="form-group">
            <label for="nombre">Nombre del sector *</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label style="display:flex; align-items:center; gap:0.5rem; font-weight:600; cursor:pointer;">
                <input type="checkbox" id="activo" name="activo"
                       style="width:auto; padding:0; border:none; box-shadow:none;">
                Sector activo
            </label>
        </div>

        <div id="form-error"   class="alert"   style="display:none;"></div>
        <div id="form-success" class="success" style="display:none;"></div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const sectorId = {{ $sectorId }};

    async function cargarSector() {
        try {
            const res  = await fetch('/api/sectores', {
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();
            const sectores = json.data ?? json;
            const sector   = sectores.find(s => s.id === sectorId);

            if (!sector) {
                document.getElementById('loading').textContent = 'Sector no encontrado.';
                return;
            }

            document.getElementById('nombre').value      = sector.nombre ?? '';
            document.getElementById('descripcion').value = sector.descripcion ?? '';
            document.getElementById('activo').checked    = sector.activo ?? true;

            document.getElementById('loading').style.display         = 'none';
            document.getElementById('edit-sector-form').style.display = 'flex';
        } catch {
            document.getElementById('loading').textContent = 'Error cargando el sector.';
        }
    }

    cargarSector();

    document.getElementById('edit-sector-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const errorEl   = document.getElementById('form-error');
        const successEl = document.getElementById('form-success');
        errorEl.style.display   = 'none';
        successEl.style.display = 'none';

        const form = e.target;
        const data = {
            nombre:      form.nombre.value,
            descripcion: form.descripcion.value || null,
            activo:      form.activo.checked,
        };

        try {
            const res  = await fetch(`/api/admin/sectores/${sectorId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });
            const json = await res.json();

            if (res.ok) {
                successEl.textContent   = '✅ Sector actualizado correctamente.';
                successEl.style.display = 'block';
            } else {
                const msgs = json.errors
                    ? Object.values(json.errors).flat().join(' | ')
                    : (json.message || 'Error al actualizar el sector');
                errorEl.textContent  = msgs;
                errorEl.style.display = 'block';
            }
        } catch {
            errorEl.textContent  = 'Error de conexión';
            errorEl.style.display = 'block';
        }
    });
</script>
@endsection
