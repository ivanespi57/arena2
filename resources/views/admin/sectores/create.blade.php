@extends('layouts.app')

@section('title', 'Crear Sector | Admin')

@section('content')
<div class="card" style="max-width:600px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1>Crear sector</h1>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary" style="font-size:0.9rem;">← Volver</a>
    </div>

    <form id="create-sector-form">
        <div class="form-group">
            <label for="nombre">Nombre del sector *</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Pista, Tribuna Norte..." required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3"
                      placeholder="Descripción opcional del sector..."></textarea>
        </div>

        <div class="form-group">
            <label style="display:flex; align-items:center; gap:0.5rem; font-weight:600; cursor:pointer;">
                <input type="checkbox" id="activo" name="activo" checked
                       style="width:auto; padding:0; border:none; box-shadow:none;">
                Sector activo
            </label>
        </div>

        <div id="form-error"   class="alert"   style="display:none;"></div>
        <div id="form-success" class="success" style="display:none;"></div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Crear sector</button>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('create-sector-form').addEventListener('submit', async (e) => {
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
            const res  = await fetch('/api/admin/sectores', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });
            const json = await res.json();

            if (res.ok) {
                successEl.textContent   = 'Sector creado correctamente.';
                successEl.style.display = 'block';
                form.reset();
                form.activo.checked = true;
            } else {
                const msgs = json.errors
                    ? Object.values(json.errors).flat().join(' | ')
                    : (json.message || 'Error al crear el sector');
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
