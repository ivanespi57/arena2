@extends('layouts.app')

@section('title', 'Mis Entradas | Roig Arena')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1>Mis entradas</h1>
    <a href="{{ route('eventos.index') }}" class="btn btn-primary">Ver eventos</a>
</div>

@if(empty($entradas))
    <div class="card text-center">
        <p style="font-size:1.1rem; color:#666; margin-bottom:1rem;">Todavia no tienes entradas.</p>
        <a href="{{ route('eventos.index') }}" class="btn btn-primary">Ver eventos disponibles</a>
    </div>
@else
    <div class="grid">
        @foreach($entradas as $entrada)
        <div class="card" style="padding:1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
                <div>
                    <h3 style="margin-bottom:0.2rem;">{{ $entrada['evento'] }}</h3>
                    <p style="color:#666; font-size:0.9rem;">
                        {{ $entrada['fecha'] }}
                        @if(!empty($entrada['hora']))
                            — {{ \Carbon\Carbon::parse($entrada['hora'])->format('H:i') }}
                        @endif
                    </p>
                </div>
                <span style="
                    background:{{ $entrada['valida'] ? '#d4edda' : '#f8d7da' }};
                    color:{{ $entrada['valida'] ? '#155724' : '#721c24' }};
                    padding:0.25rem 0.75rem; border-radius:20px;
                    font-size:0.8rem; font-weight:600; white-space:nowrap; flex-shrink:0;">
                    {{ $entrada['valida'] ? 'Valida' : 'Expirada' }}
                </span>
            </div>

            <hr style="margin:0.75rem 0; border:none; border-top:1px solid #eee;">

            <p style="margin-bottom:0.3rem;"><strong>Asiento:</strong> {{ $entrada['asiento'] }}</p>
            <p style="margin-bottom:0.75rem;"><strong>Precio pagado:</strong>
                <span style="color:#27ae60;">{{ $entrada['precio'] }}</span>
            </p>

            <a href="{{ route('entradas.show', $entrada['id']) }}"
               class="btn btn-primary"
               style="width:100%; text-align:center; display:block;">
                Ver entrada y QR
            </a>
        </div>
        @endforeach
    </div>
@endif
@endsection
