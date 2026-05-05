@extends('layouts.app')

@section('title', 'Eventos')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900">Próximos Eventos</h1>
    <p class="text-gray-600 mt-2">Selecciona un evento para comprar entradas</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($eventos as $evento)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <!-- Imagen del evento -->
            @if ($evento->poster_url)
                <img src="{{ $evento->poster_url }}" alt="{{ $evento->nombre }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                    <span class="text-white text-4xl">🎭</span>
                </div>
            @endif

            <!-- Contenido -->
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $evento->nombre }}</h2>

                <p class="text-gray-600 text-sm mb-4">{{ $evento->descripcion_corta }}</p>

                <!-- Fecha y hora -->
                <div class="flex items-center text-gray-700 mb-3">
                    <span class="text-lg mr-2">📅</span>
                    <span>{{ $evento->fecha->format('d/m/Y') }} a las {{ $evento->hora }}</span>
                </div>

                <!-- Disponibilidad -->
                @php
                    $asientosDisp = $evento->totalAsientosDisponibles();
                    $entradasVend = $evento->totalEntradasVendidas();
                @endphp
                <div class="text-sm text-gray-600 mb-4">
                    <p>{{ $asientosDisp }} asientos disponibles</p>
                    <p>{{ $entradasVend }} entradas vendidas</p>
                </div>

                <!-- Precio mínimo -->
                @php
                    $precioMin = $evento->precios->min('precio');
                @endphp
                <div class="flex items-center justify-between mb-4">
                    <span class="text-red-600 font-bold">Desde {{ number_format($precioMin, 2) }}€</span>
                </div>

                <!-- Botón -->
                <a href="{{ route('eventos.show', $evento->id) }}" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors text-center font-semibold">
                    Ver más y comprar
                </a>
            </div>
        </div>
    @endforeach
</div>

@if ($eventos->isEmpty())
    <div class="text-center py-12">
        <p class="text-gray-600 text-lg">No hay eventos disponibles en este momento.</p>
    </div>
@endif
@endsection
