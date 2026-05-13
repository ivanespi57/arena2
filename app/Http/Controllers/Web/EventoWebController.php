<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EventoController;
use App\Models\Precio;
use App\Models\Sector;
use Illuminate\Http\Request;

class EventoWebController extends Controller
{
    public function index()
    {
        return view('eventos.index');
    }

    public function show($id)
    {
        return view('eventos.show', ['eventoId' => $id]);
    }

    public function create()
    {
        return view('eventos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'precio_base' => 'required|numeric|min:0',
        ]);

        $precioBase = (float) $request->input('precio_base');

        $response = app(EventoController::class)->store($request);
        $data     = $response->getData(true);
        $eventoId = $data['data']['id'];

        $sectores = Sector::where('activo', true)->get();
        foreach ($sectores as $sector) {
            Precio::create([
                'evento_id'  => $eventoId,
                'sector_id'  => $sector->id,
                'precio'     => $precioBase,
                'disponible' => true,
            ]);
        }

        return response()->json([
            'data'    => $data['data'],
            'message' => 'Evento creado con ' . $sectores->count() . ' sectores asignados.',
        ], 201);
    }
}
