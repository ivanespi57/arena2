<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EntradaController;

class EntradaWebController extends Controller
{
    public function index()
    {
        try {
            $response = app(EntradaController::class)->index(request());
            $entradas = $response->getData(true)['data'] ?? [];
        } catch (\Throwable) {
            $entradas = [];
        }

        return view('entradas.index', compact('entradas'));
    }

    public function show($id)
    {
        try {
            $indexResp = app(EntradaController::class)->index(request());
            $all       = $indexResp->getData(true)['data'] ?? [];
            $fromList  = collect($all)->firstWhere('id', (int) $id);

            if (!$fromList) {
                abort(404, 'Entrada no encontrada');
            }

            $showResp = app(EntradaController::class)->show($id);
            $detail   = $showResp->getData(true)['data'] ?? [];

            // index aporta 'valida'; show aporta 'comprador' y 'codigo_qr'
            $entrada        = array_merge($fromList, $detail);
            $entrada['id']  = (int) $id;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            abort(404, 'Entrada no encontrada');
        }

        return view('entradas.show', compact('entrada'));
    }
}
