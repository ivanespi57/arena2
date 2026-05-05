<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Entrada;
use Illuminate\Http\Request;

class EntradaWebController extends Controller
{
    /**
     * Listar mis entradas
     */
    public function index(Request $request)
    {
        $entradas = $request->user()
            ->entradas()
            ->with(['evento', 'asiento.sector'])
            ->latest()
            ->get();

        return view('entradas.index', compact('entradas'));
    }

    /**
     * Ver detalle de una entrada
     */
    public function show($id)
    {
        $entrada = Entrada::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['evento', 'asiento.sector'])
            ->firstOrFail();

        return view('entradas.show', compact('entrada'));
    }
}
