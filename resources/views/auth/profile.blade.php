@extends('layouts.app')

@section('title', 'Mi Perfil | Roig Arena')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h1>Mi Perfil</h1>

    <div class="mt-3">
        <p><strong>Nombre:</strong> {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</p>
        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        <p><strong>Miembro desde:</strong> {{ auth()->user()->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al Dashboard</a>
        <a href="{{ route('entradas.index') }}" class="btn btn-primary" style="margin-left:0.5rem;">Mis entradas</a>
    </div>
</div>
@endsection
