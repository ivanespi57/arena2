@extends('layouts.app')

@section('title', 'Entrada | Roig Arena')

@section('content')
<div class="card" style="max-width:600px; margin:0 auto;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h1 style="font-size:1.5rem;">Tu entrada</h1>
        <span style="
            background:{{ ($entrada['valida'] ?? true) ? '#d4edda' : '#f8d7da' }};
            color:{{ ($entrada['valida'] ?? true) ? '#155724' : '#721c24' }};
            padding:0.4rem 1rem; border-radius:20px; font-weight:600; font-size:0.9rem;">
            {{ ($entrada['valida'] ?? true) ? '✅ Válida' : '❌ Expirada' }}
        </span>
    </div>

    <hr style="border:none; border-top:2px solid #eee; margin-bottom:1.5rem;">

    <h2 style="font-size:1.3rem; margin-bottom:0.25rem;">{{ $entrada['evento'] }}</h2>
    <p style="color:#666; margin-bottom:1.5rem;">
        📅 {{ $entrada['fecha'] }}
        @if(!empty($entrada['hora']))
            — 🕐 {{ $entrada['hora'] }}
        @endif
    </p>

    <div style="background:#f8f9fa; border-radius:8px; padding:1.25rem; margin-bottom:1.5rem;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="padding:0.4rem 0; color:#666; width:40%;">Asiento</td>
                <td style="padding:0.4rem 0; font-weight:600;">{{ $entrada['asiento'] }}</td>
            </tr>
            <tr>
                <td style="padding:0.4rem 0; color:#666;">Precio pagado</td>
                <td style="padding:0.4rem 0; font-weight:600; color:#27ae60;">{{ $entrada['precio'] }}</td>
            </tr>
            @if(!empty($entrada['comprador']))
            <tr>
                <td style="padding:0.4rem 0; color:#666;">Comprador</td>
                <td style="padding:0.4rem 0;">{{ $entrada['comprador'] }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Código QR --}}
    <div style="text-align:center; padding:1.5rem; background:#f8f9fa; border-radius:8px; margin-bottom:1.5rem;">
        <p style="font-size:0.85rem; color:#666; margin-bottom:0.75rem;">Código de acceso</p>
        <div style="background:white; display:inline-block; padding:1rem; border-radius:8px; border:2px solid #eee;">
            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($entrada['codigo_qr']) }}"
                alt="Código QR de la entrada"
                style="display:block; width:200px; height:200px;"
            >
        </div>
        <p style="font-family:monospace; font-size:0.8rem; color:#666; margin-top:0.75rem; word-break:break-all;">
            {{ $entrada['codigo_qr'] }}
        </p>
    </div>

    <div style="display:flex; gap:0.75rem;">
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Mis entradas</a>
        <a href="{{ route('eventos.index') }}" class="btn btn-primary">Ver más eventos</a>
    </div>
</div>
@endsection
