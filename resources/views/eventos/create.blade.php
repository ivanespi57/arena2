@extends('layouts.app')

@section('title', 'Crear Evento | Roig Arena')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <h1>Crear nuevo evento</h1>

    <form id="evento-form">
        <div class="form-group">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="descripcion_corta">Descripción corta * <small style="font-weight:400;">(máx. 255 caracteres)</small></label>
            <input type="text" id="descripcion_corta" name="descripcion_corta" maxlength="255" required>
        </div>

        <div class="form-group">
            <label for="descripcion_larga">Descripción larga *</label>
            <textarea id="descripcion_larga" name="descripcion_larga" rows="5" required></textarea>
        </div>

        <div class="form-group">
            <label for="poster_url">URL del póster</label>
            <input type="url" id="poster_url" name="poster_url" placeholder="https://...">
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="fecha">Fecha *</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="hora">Hora * <small style="font-weight:400;">(HH:MM)</small></label>
                <input type="time" id="hora" name="hora" required>
            </div>
        </div>

        <div id="form-error" class="alert" style="display:none;"></div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Crear evento</button>
            <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('evento-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const errorEl = document.getElementById('form-error');
        errorEl.style.display = 'none';

        const form = e.target;
        const data = {
            nombre:           form.nombre.value,
            descripcion_corta: form.descripcion_corta.value,
            descripcion_larga: form.descripcion_larga.value,
            poster_url:        form.poster_url.value || null,
            fecha:             form.fecha.value,
            hora:              form.hora.value,
        };

        try {
            const res = await fetch('/api/admin/eventos', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const json = await res.json();

            if (res.ok) {
                window.location.href = `/eventos/${json.data.id}`;
            } else {
                const msgs = json.errors
                    ? Object.values(json.errors).flat().join(' | ')
                    : (json.message || 'Error al crear el evento');
                errorEl.textContent = msgs;
                errorEl.style.display = 'block';
            }
        } catch {
            errorEl.textContent = 'Error de conexión';
            errorEl.style.display = 'block';
        }
    });
</script>
@endsection
