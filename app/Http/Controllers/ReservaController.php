<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservaResource;
use App\Services\ReservaService;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // Bloquea el asiento durante 15 minutos para el usuario
    public function store(Request $request, ReservaService $service)
    {
        $request->validate([
            'evento_id'  => 'required|exists:eventos,id',
            'asiento_id' => 'required|exists:asientos,id',
        ]);

        try {
            $reserva = $service->reservarAsiento(
                $request->evento_id,
                $request->asiento_id,
                auth()->id()
            );

            return response()->json([
                'data'    => new ReservaResource($reserva),
                'message' => 'Asiento reservado por 15 minutos',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    // Lista las reservas que aún no han expirado
    public function index(Request $request, ReservaService $service)
    {
        $reservas = $service->obtenerReservasActivas(auth()->id());

        return ReservaResource::collection($reservas);
    }

    // Libera el asiento antes de que expire el tiempo
    public function destroy($id, ReservaService $service)
    {
        try {
            $service->cancelarReserva($id, auth()->id());

            return response()->json([
                'message' => 'Reserva cancelada correctamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
