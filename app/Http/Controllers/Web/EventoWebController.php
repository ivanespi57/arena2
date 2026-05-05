<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;

class EventoWebController extends Controller
{
    /**
     * Mostrar catálogo de eventos
     */
    public function index()
    {
        $eventos = Evento::futuros()
            ->with(['precios.sector'])
            ->get();

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Mostrar detalle de evento y selector de asientos
     */
    public function show($id)
    {
        $evento = Evento::with(['precios.sector'])
            ->findOrFail($id);

        return view('eventos.show', compact('evento'));
    }
}
