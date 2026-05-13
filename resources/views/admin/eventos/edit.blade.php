@extends('layouts.app')

@section('title', 'Editar Evento | Admin')

@section('content')
<div class="card" style="max-width:700px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1>Editar evento</h1>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary" style="font-size:0.9rem;">← Volver</a>
    </div>

    <form id="edit-form">
        <div class="form-group">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" value="{{ $evento['nombre'] }}" required>
        </div>

        <div class="form-group">
            <label for="descripcion_corta">Descripción corta * <small style="font-weight:400;">(máx. 255 caracteres)</small></label>
            <input type="text" id="descripcion_corta" name="descripcion_corta"
                   value="{{ $evento['descripcion_corta'] }}" maxlength="255" required>
        </div>

        <div class="form-group">
            <label for="descripcion_larga">Descripción larga *</label>
            <textarea id="descripcion_larga" name="descripcion_larga" rows="5" required>{{ $evento['descripcion_larga'] }}</textarea>
        </div>

        <div class="form-group">
            <label for="poster_url">URL del póster</label>
            <input type="url" id="poster_url" name="poster_url"
                   value="{{ $evento['poster_url'] ?? '' }}" placeholder="https://...">
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="fecha">Fecha *</label>
                <input type="date" id="fecha" name="fecha" value="{{ substr($evento['fecha'] ?? '', 0, 10) }}" required>
            </div>
            <div class="form-group">
                <label for="hora">Hora *</label>
                <input type="time" id="hora" name="hora" value="{{ $evento['hora'] ?? '' }}" required>
            </div>
        </div>

        <div id="form-error" class="alert" style="display:none;"></div>
        <div id="form-success" class="success" style="display:none;"></div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('eventos.show', $evento['id']) }}" class="btn btn-secondary">Ver evento</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const eventoId = {{ $evento['id'] }};

    document.getElementById('edit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const errorEl   = document.getElementById('form-error');
        const successEl = document.getElementById('form-success');
        errorEl.style.display   = 'none';
        successEl.style.display = 'none';

        const form = e.target;
        const data = {
            nombre:            form.nombre.value,
            descripcion_corta: form.descripcion_corta.value,
            descripcion_larga: form.descripcion_larga.value,
            poster_url:        form.poster_url.value || null,
            fecha:             form.fecha.value,
            hora:              form.hora.value,
        };

        try {
            const res  = await fetch(`/api/admin/eventos/${eventoId}`, {
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
                successEl.textContent  = '✅ Evento actualizado correctamente.';
                successEl.style.display = 'block';
            } else {
                const msgs = json.errors
                    ? Object.values(json.errors).flat().join(' | ')
                    : (json.message || 'Error al actualizar el evento');
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
