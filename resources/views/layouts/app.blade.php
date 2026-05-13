<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Roig Arena')</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
                sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header & Navigation */
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a, nav button {
            color: white;
            text-decoration: none;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.2s;
        }

        nav a:hover, nav button:hover {
            background: rgba(255,255,255,0.1);
        }

        nav .btn-logout {
            background: rgba(255,255,255,0.2);
        }

        /* Main Content */
        main {
            min-height: calc(100vh - 200px);
            padding: 2rem 0;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            text-align: center;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #229954;
        }

        /* Forms */
        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-weight: 600;
            color: #333;
        }

        input, textarea, select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error {
            color: #e74c3c;
            font-size: 0.875rem;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        /* Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            nav {
                gap: 1rem;
            }

            header .container {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Event Cards */
        .event-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .event-card-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .event-card-content {
            padding: 1.5rem;
        }

        .event-card-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .event-card-date {
            color: #666;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        /* Footer */
        footer {
            background: #333;
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: 4rem;
        }

        /* Utilities */
        .text-center {
            text-align: center;
        }

        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .mt-4 { margin-top: 2rem; }

        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 2rem; }

        .hidden { display: none; }

        @yield('styles')
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="{{ route('home') }}" class="logo">🎭 Roig Arena</a>
            <nav>
                <a href="{{ route('eventos.index') }}">Eventos</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert">{{ $error }}</div>
                @endforeach
            @endif

            @if (session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Roig Arena. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Setup CSRF token para requests AJAX
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) {
            window.csrfToken = token;
        }
    </script>

    @yield('scripts')
</body>
</html>
