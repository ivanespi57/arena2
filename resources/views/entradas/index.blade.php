@extends('layouts.app')

@section('title', 'Mis Entradas | Roig Arena')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1>Mis entradas</h1>
    <a href="{{ route('eventos.index') }}" class="btn btn-primary">Ver eventos</a>
</div>

@if(empty($entradas))
    <div class="card text-center">
        <p style="font-size:1.1rem; color:#666;">Todavía no tienes entradas.</p>
        <a href="{{ route('eventos.index') }}" class="btn btn-primary mt-3">Ver eventos disponibles</a>
    </div>
@else
    <div class="grid">
        @foreach($entradas as $entrada)
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <h3 style="margin-bottom:0.25rem;">{{ $entrada['evento']['nombre'] }}</h3>
                    <p style="color:#666; font-size:0.9rem;">
                        {{ $entrada['evento']['fecha'] }}
                        @if($entrada['evento']['hora'])
                            — {{ $entrada['evento']['hora'] }}
                        @endif
                    </p>
                </div>
                <span style="background:{{ $entrada['valida'] ? '#d4edda' : '#f8d7da' }};
                             color:{{ $entrada['valida'] ? '#155724' : '#721c24' }};
                             padding:0.25rem 0.75rem; border-radius:20px; font-size:0.8rem; white-space:nowrap;">
                    {{ $entrada['valida'] ? 'Válida' : 'Expirada' }}
                </span>
            </div>

            <hr style="margin:1rem 0; border:none; border-top:1px solid #eee;">

            <p><strong>Asiento:</strong> {{ $entrada['asiento']['nombre'] }}</p>
            <p><strong>Sector:</strong> {{ $entrada['asiento']['sector'] }}</p>
            <p><strong>Precio pagado:</strong> {{ $entrada['precio_pagado'] }}</p>
            <p style="color:#666; font-size:0.85rem; margin-top:0.5rem;">
                Comprada el {{ $entrada['fecha_compra'] }}
            </p>

            <a href="{{ route('entradas.show', $entrada['id']) }}" class="btn btn-primary mt-3" style="width:100%; text-align:center;">
                Ver entrada y QR
            </a>
        </div>
        @endforeach
    </div>
@endif
@endsection
