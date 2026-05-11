<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-token" content="{{ session('api_token', '') }}">
    <title>@yield('title') - Roig Arena</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navegación -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center h-16 gap-12">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600">Roig Arena</a>
                </div>

                <!-- Menú central -->
                <div class="flex gap-32">
                    <a href="{{ route('home') }}" class="text-gray-700 font-medium hover:text-red-600 hover:border-b-2 hover:border-red-600 transition-all pb-1">Eventos</a>

                        <a href="{{ route('entradas.index') }}" class="text-gray-700 font-medium hover:text-red-600 hover:border-b-2 hover:border-red-600 transition-all pb-1">Mis Entradas</a>

                </div>

                <!-- Botones derecha (flex-1 para empujar a la derecha) -->
                <div class="flex-1 flex items-center justify-end space-x-4">
                    @auth
                        <span class="text-gray-700 text-sm font-semibold">{{ auth()->user()->nombre }}</span>
                        @if(auth()->user()->is_admin)
                            <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-semibold">Admin</span>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 font-semibold transition-colors">Cerrar sesión</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-red-600 transition-colors">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 font-semibold transition-colors">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="text-red-800 font-semibold">Errores:</h3>
                <ul class="text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; 2026 Roig Arena. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
