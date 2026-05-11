@extends('layouts.app')

@section('title', $evento->nombre)

@section('content')
<div x-data="selectorAsientos()" class="mb-8">
    <!-- Encabezado -->
    <div class="mb-8">
        <a href="{{ route('home') }}" class="text-red-600 hover:text-red-700 mb-4 inline-block">← Volver a eventos</a>
        <h1 class="text-4xl font-bold text-gray-900">{{ $evento->nombre }}</h1>
        <p class="text-gray-600 mt-2">{{ $evento->descripcion_corta }}</p>
    </div>

    <!-- Información del evento -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-gray-600 text-sm">📅 Fecha</p>
                <p class="font-semibold">{{ $evento->fecha->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">⏰ Hora</p>
                <p class="font-semibold">{{ \Carbon\Carbon::parse($evento->hora)->format('H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">🎟 Asientos disponibles</p>
                <p class="font-semibold">{{ $evento->totalAsientosDisponibles() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">🎪 Entradas vendidas</p>
                <p class="font-semibold">{{ $evento->totalEntradasVendidas() }}</p>
            </div>
        </div>
    </div>

    <!-- Descripción completa -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">Descripción</h2>
        <p class="text-gray-700 leading-relaxed">{{ $evento->descripcion_larga }}</p>
    </div>

    <!-- Selector de sectores y asientos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sectores disponibles -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold mb-6">Selecciona tu sector</h2>
            <div x-data="{ sectorAbierto: false }" class="mb-6">
                <button
                    @click="sectorAbierto = !sectorAbierto"
                    class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-3 px-6 rounded-lg border border-gray-300 transition-all flex items-center justify-between">
                    <span x-text="sectorSeleccionado ? 'Sector seleccionado' : 'Seleccionar sector'"></span>
                    <span class="text-lg" x-text="sectorAbierto ? '▼' : '▶'"></span>
                </button>

                <div x-show="sectorAbierto" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3" style="display: none;">
                    @php
                        $sectores = $evento->sectoresDisponibles();
                    @endphp
                    @foreach ($sectores as $sector)
                        @php
                            $precio = $sector->precios()
                                ->where('evento_id', $evento->id)
                                ->first();
                        @endphp
                        <button
                            @click="seleccionarSector({{ $sector->id }}, '{{ $sector->nombre }}', {{ $precio?->precio ?? 0 }}); sectorAbierto = false"
                            :class="sectorSeleccionado === {{ $sector->id }} ? 'ring-2 ring-red-600 bg-red-50 border-red-600' : 'hover:shadow-lg border-gray-300'"
                            class="p-4 border rounded-lg transition-all cursor-pointer text-left">
                            <p class="font-semibold text-gray-900">{{ $sector->nombre }}</p>
                            <p class="text-red-600 font-bold text-lg">{{ number_format($precio?->precio ?? 0, 2) }}€</p>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Asientos del sector seleccionado -->
            <div x-show="sectorSeleccionado !== null" class="mt-8" style="display: none;">
                <h2 class="text-2xl font-bold mb-6">Asientos disponibles</h2>
                <div id="seatMap" class="bg-white rounded-lg shadow p-8">
                    <p class="text-gray-500 text-center" x-show="cargandoAsientos">Cargando asientos...</p>
                    <div id="asientosContainer" class="grid gap-4"></div>
                </div>
            </div>
        </div>

        <!-- Carrito de compra -->
        <div class="bg-white rounded-lg shadow p-6 h-fit sticky top-20">
            <h3 class="text-2xl font-bold mb-6">Resumen</h3>

            <!-- Asientos seleccionados -->
            <div class="mb-6">
                <p class="font-semibold mb-3">Asientos seleccionados:</p>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <template x-for="asiento in asientosSeleccionados" :key="asiento.id">
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded">
                            <span x-text="asiento.nombre"></span>
                            <button
                                @click="desseleccionarAsiento(asiento.id)"
                                class="text-red-600 hover:text-red-700 font-bold">
                                ✕
                            </button>
                        </div>
                    </template>
                </div>
                <p x-show="asientosSeleccionados.length === 0" class="text-gray-500 text-sm">No hay asientos seleccionados</p>
            </div>

            <!-- Precios -->
            <div class="border-t pt-4 space-y-2 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-700">Cantidad de asientos:</span>
                    <span class="font-semibold" x-text="asientosSeleccionados.length"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Subtotal:</span>
                    <span class="font-semibold" x-text="'€' + calcularSubtotal().toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>Total:</span>
                    <span class="text-red-600" x-text="'€' + calcularSubtotal().toFixed(2)"></span>
                </div>
            </div>

            <!-- Botón comprar -->
            @auth
                <button
                    @click="procesarCompra()"
                    :disabled="asientosSeleccionados.length === 0 || cargandoCompra"
                    class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span x-show="!cargandoCompra">Comprar entradas</span>
                    <span x-show="cargandoCompra">Procesando...</span>
                </button>
            @else
                <a href="{{ route('login') }}" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold text-center block">
                    Inicia sesión para comprar
                </a>
            @endauth
        </div>
    </div>
</div>

<script>
function selectorAsientos() {
    return {
        sectorSeleccionado: null,
        asientosSeleccionados: [],
        cargandoAsientos: false,
        cargandoCompra: false,
        eventoId: {{ $evento->id }},
        precioSector: 0,

        seleccionarSector(sectorId, sectorNombre, precio) {
            this.sectorSeleccionado = sectorId;
            this.precioSector = precio;
            this.asientosSeleccionados = [];
            this.cargarAsientos(sectorId);
        },

        cargarAsientos(sectorId) {
            this.cargandoAsientos = true;
            window.axios.get(`/api/eventos/${this.eventoId}/sectores/${sectorId}/asientos`)
                .then(response => {
                    const data = response.data.data;
                    this.renderizarAsientos(data.asientos, sectorId);
                })
                .catch(error => {
                    alert('Error al cargar asientos: ' + error.response.data.error);
                })
                .finally(() => {
                    this.cargandoAsientos = false;
                });
        },

        renderizarAsientos(asientos, sectorId) {
            const container = document.getElementById('asientosContainer');
            container.innerHTML = '';

            // Agrupar por fila
            const filas = {};
            asientos.forEach(asiento => {
                if (!filas[asiento.fila]) {
                    filas[asiento.fila] = [];
                }
                filas[asiento.fila].push(asiento);
            });

            // Renderizar filas
            Object.keys(filas).sort().forEach(fila => {
                const filaDom = document.createElement('div');
                filaDom.className = 'flex items-center gap-4';

                // Label de fila
                const label = document.createElement('span');
                label.className = 'w-8 font-semibold text-gray-600';
                label.textContent = 'Fila ' + fila;
                filaDom.appendChild(label);

                // Asientos
                const contenedorAsientos = document.createElement('div');
                contenedorAsientos.className = 'flex gap-2 flex-wrap';

                filas[fila].forEach(asiento => {
                    const boton = document.createElement('button');
                    boton.className = 'w-10 h-10 rounded text-sm font-semibold transition-colors';
                    boton.textContent = asiento.numero;

                    if (asiento.disponible) {
                        const estaSel = this.asientosSeleccionados.some(a => a.id === asiento.id);
                        boton.className += estaSel
                            ? ' bg-red-600 text-white border-2 border-red-800'
                            : ' bg-gray-200 text-gray-900 hover:bg-gray-300 border-2 border-gray-300';
                        boton.onclick = () => this.toggleAsiento(asiento, `Fila ${fila}, Asiento ${asiento.numero}`);
                    } else {
                        boton.className += ' bg-gray-400 text-gray-600 cursor-not-allowed';
                        boton.disabled = true;
                    }

                    contenedorAsientos.appendChild(boton);
                });

                filaDom.appendChild(contenedorAsientos);
                container.appendChild(filaDom);
            });
        },

        toggleAsiento(asiento, nombre) {
            const existe = this.asientosSeleccionados.findIndex(a => a.id === asiento.id);
            if (existe !== -1) {
                this.asientosSeleccionados.splice(existe, 1);
            } else {
                this.asientosSeleccionados.push({
                    id: asiento.id,
                    nombre: nombre,
                    precio: this.precioSector
                });
            }
            // Re-renderizar para actualizar colores
            const container = document.getElementById('asientosContainer');
            const filas = {};

            // Agrupar asientos del DOM
            const botones = container.querySelectorAll('button');
            botones.forEach(boton => {
                const className = boton.className;
                if (className.includes('bg-gray-200') || className.includes('bg-red-600')) {
                    const esRojo = className.includes('bg-red-600');
                    const numero = boton.textContent;
                    const fila = boton.closest('.flex.items-center.gap-4')?.querySelector('span')?.textContent;

                    if (fila) {
                        // Encontrar el asiento en asientosSeleccionados
                        const estaSel = this.asientosSeleccionados.some(a => a.nombre === `${fila}, Asiento ${numero}`);

                        if (estaSel && !esRojo) {
                            boton.className = 'w-10 h-10 rounded text-sm font-semibold transition-colors bg-red-600 text-white border-2 border-red-800';
                        } else if (!estaSel && esRojo) {
                            boton.className = 'w-10 h-10 rounded text-sm font-semibold transition-colors bg-gray-200 text-gray-900 hover:bg-gray-300 border-2 border-gray-300';
                        }
                    }
                }
            });
        },

        desseleccionarAsiento(asientoId) {
            this.asientosSeleccionados = this.asientosSeleccionados.filter(a => a.id !== asientoId);
        },

        calcularSubtotal() {
            return this.asientosSeleccionados.reduce((total, asiento) => total + asiento.precio, 0);
        },

        async procesarCompra() {
            if (this.asientosSeleccionados.length === 0) {
                alert('Debes seleccionar al menos un asiento');
                return;
            }

            this.cargandoCompra = true;

            try {
                // Primero, reservar los asientos
                const reservasIds = [];
                for (let asiento of this.asientosSeleccionados) {
                    const resReserva = await window.axios.post('/api/reservas', {
                        evento_id: this.eventoId,
                        asiento_id: asiento.id
                    });
                    reservasIds.push(resReserva.data.data.id);
                }

                // Luego, procesar la compra
                const resCompra = await window.axios.post('/api/compras', {
                    reservas: reservasIds
                });

                alert('¡Compra realizada exitosamente! Revisa tus entradas en "Mis Entradas".');
                window.location.href = '{{ route('entradas.index') }}';
            } catch (error) {
                const mensaje = error.response?.data?.error || 'Error al procesar la compra';
                alert('Error: ' + mensaje);
            } finally {
                this.cargandoCompra = false;
            }
        },

        obtenerToken() {
            // Axios ya está configurado con el token automáticamente
            return '';
        }
    };
}
</script>
@endsection
