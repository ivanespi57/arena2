@extends('layouts.app')

@section('title', 'Mis Entradas')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900">Mis Entradas</h1>
    <p class="text-gray-600 mt-2">Historial de todas tus entradas compradas</p>
</div>

@if ($entradas->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-600 text-lg">Aún no tienes entradas compradas.</p>
        <a href="{{ route('home') }}" class="text-red-600 hover:text-red-700 font-semibold mt-4 inline-block">
            Ver eventos disponibles
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($entradas as $entrada)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Encabezado -->
                <div class="bg-red-600 text-white p-4">
                    <h2 class="text-xl font-bold">{{ $entrada->evento->nombre }}</h2>
                </div>

                <!-- Contenido -->
                <div class="p-6 space-y-4">
                    <!-- Evento y fecha -->
                    <div>
                        <p class="text-gray-600 text-sm">📅 Fecha y hora</p>
                        <p class="font-semibold">
                            {{ $entrada->evento->fecha->format('d/m/Y') }} a las {{ $entrada->evento->hora }}
                        </p>
                    </div>

                    <!-- Asiento -->
                    <div>
                        <p class="text-gray-600 text-sm">🎪 Asiento</p>
                        <p class="font-semibold">
                            {{ $entrada->asiento->sector->nombre }} - {{ $entrada->asiento->nombreCompleto() }}
                        </p>
                    </div>

                    <!-- Precio -->
                    <div>
                        <p class="text-gray-600 text-sm">💰 Precio pagado</p>
                        <p class="font-semibold text-red-600">{{ $entrada->precioFormateado() }}</p>
                    </div>

                    <!-- Código QR -->
                    <div>
                        <p class="text-gray-600 text-sm">🔐 Código QR</p>
                        <p class="font-mono text-xs break-all bg-gray-100 p-2 rounded">{{ $entrada->codigo_qr }}</p>
                    </div>

                    <!-- Estado -->
                    <div>
                        <p class="text-gray-600 text-sm">✓ Estado</p>
                        @if ($entrada->esValida())
                            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-semibold">
                                Válida
                            </span>
                        @else
                            <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded text-sm font-semibold">
                                Expirada
                            </span>
                        @endif
                    </div>

                    <!-- Botón detalle -->
                    <a href="{{ route('entradas.show', $entrada->id) }}" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors text-center font-semibold block">
                        Ver detalle
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
