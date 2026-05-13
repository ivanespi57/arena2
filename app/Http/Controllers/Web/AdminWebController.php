<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\SectorController;

class AdminWebController extends Controller
{
    public function index()
    {
        try {
            $evResp  = app(EventoController::class)->index();
            $eventos = $evResp->getData(true)['data'] ?? [];
        } catch (\Throwable) {
            $eventos = [];
        }

        try {
            $secResp  = app(SectorController::class)->index();
            $sectores = $secResp->getData(true)['data'] ?? [];
        } catch (\Throwable) {
            $sectores = [];
        }

        return view('admin.index', compact('eventos', 'sectores'));
    }

    public function editEvento($id)
    {
        try {
            $resp  = app(EventoController::class)->show($id);
            $data  = $resp->getData(true)['data'] ?? [];
            $evento = $data['evento'] ?? null;

            if (!$evento) {
                abort(404, 'Evento no encontrado');
            }

            // Si 'evento' es objeto Eloquent serializado, getData(true) ya da array
            if (!is_array($evento)) {
                $evento = (array) $evento;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            abort(404, 'Evento no encontrado');
        }

        return view('admin.eventos.edit', compact('evento'));
    }

    public function createSector()
    {
        return view('admin.sectores.create');
    }

    public function editSector($id)
    {
        return view('admin.sectores.edit', ['sectorId' => $id]);
    }
}
