<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class EventoWebController extends Controller
{
    /**
     * Mostrar listado de eventos
     */
    public function index()
    {
        return view('eventos.index');
    }

    /**
     * Mostrar detalle de evento
     */
    public function show($id)
    {
        return view('eventos.show');
    }

    /**
     * Mostrar formulario para crear evento (admin)
     */
    public function create()
    {
        return view('eventos.create');
    }
}

        return view('eventos.show', compact('evento'));
    }
}
