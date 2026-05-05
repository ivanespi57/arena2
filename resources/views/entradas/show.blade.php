@extends('layouts.app')

@section('title', 'Detalle de Entrada')

@section('content')
<div class="mb-8">
    <a href="{{ route('entradas.index') }}" class="text-red-600 hover:text-red-700 mb-4 inline-block">← Volver a mis entradas</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Información principal -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-8">
                <h1 class="text-4xl font-bold">{{ $entrada->evento->nombre }}</h1>
                <p class="text-red-100 mt-2">Entrada #{{ $entrada->id }}</p>
            </div>

            <!-- Contenido -->
            <div class="p-8 space-y-8">
                <!-- Evento -->
                <div class="border-b pb-6">
                    <h2 class="text-2xl font-bold mb-4">Evento</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">📅 Fecha</p>
                            <p class="font-semibold">{{ $entrada->evento->fecha->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">⏰ Hora</p>
                            <p class="font-semibold">{{ $entrada->evento->hora }}</p>
                        </div>
                    </div>
                </div>

                <!-- Asiento -->
                <div class="border-b pb-6">
                    <h2 class="text-2xl font-bold mb-4">Tu asiento</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">🎪 Sector</p>
                            <p class="font-semibold">{{ $entrada->asiento->sector->nombre }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">📍 Ubicación</p>
                            <p class="font-semibold">{{ $entrada->asiento->nombreCompleto() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Precio -->
                <div class="border-b pb-6">
                    <h2 class="text-2xl font-bold mb-4">Información de pago</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Precio pagado:</span>
                            <span class="text-3xl font-bold text-red-600">{{ $entrada->precioFormateado() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="border-b pb-6">
                    <h2 class="text-2xl font-bold mb-4">Estado</h2>
                    @if ($entrada->esValida())
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-green-800 font-semibold">✓ Entrada válida</p>
                            <p class="text-green-700 text-sm">Esta entrada es válida para acceder al evento.</p>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-red-800 font-semibold">✗ Entrada expirada</p>
                            <p class="text-red-700 text-sm">El evento ya ha finalizado.</p>
                        </div>
                    @endif
                </div>

                <!-- Código QR -->
                <div>
                    <h2 class="text-2xl font-bold mb-4">Código de acceso</h2>
                    <p class="text-gray-600 text-sm mb-3">Presenta este código al acceder al evento:</p>
                    <div class="bg-gray-900 p-6 rounded-lg">
                        <p class="font-mono text-white text-sm break-all">{{ $entrada->codigo_qr }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
            <h3 class="text-xl font-bold mb-6">Resumen</h3>

            <div class="space-y-4">
                <div>
                    <p class="text-gray-600 text-sm">Evento</p>
                    <p class="font-semibold">{{ $entrada->evento->nombre }}</p>
                </div>

                <div>
                    <p class="text-gray-600 text-sm">Asiento</p>
                    <p class="font-semibold">{{ $entrada->asiento->nombreCompleto() }}</p>
                </div>

                <div>
                    <p class="text-gray-600 text-sm">Sector</p>
                    <p class="font-semibold">{{ $entrada->asiento->sector->nombre }}</p>
                </div>

                <div class="border-t pt-4">
                    <p class="text-gray-600 text-sm">Fecha evento</p>
                    <p class="font-semibold">{{ $entrada->evento->fecha->format('d/m/Y H:i') }}</p>
                </div>

                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-gray-600 text-sm">Total pagado</p>
                    <p class="text-2xl font-bold text-red-600">{{ $entrada->precioFormateado() }}</p>
                </div>
            </div>

            <a href="{{ route('home') }}" class="w-full mt-6 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors font-semibold text-center block">
                Ver más eventos
            </a>
        </div>
    </div>
</div>
@endsection
