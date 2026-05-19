<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Sector;
use App\Models\Asiento;
use Illuminate\Http\Request;

class AsientoController extends Controller
{
    // Devuelve todos los asientos de un evento con su estado (disponible/ocupado)
    public function porEvento($eventoId)
    {
        $evento = Evento::findOrFail($eventoId);

        $sectoresDisponibles = $evento->sectoresDisponibles()->pluck('id');

        $asientos = Asiento::whereIn('sector_id', $sectoresDisponibles)
            ->with('sector')
            ->get()
            ->map(function ($asiento) use ($eventoId) {
                return [
                    'id'         => $asiento->id,
                    'sector'     => $asiento->sector->nombre,
                    'fila'       => $asiento->fila,
                    'numero'     => $asiento->numero,
                    'disponible' => $asiento->estaDisponibleParaEvento($eventoId),
                    'precio'     => $asiento->sector->precios()
                        ->where('evento_id', $eventoId)
                        ->first()?->precio,
                ];
            });

        return response()->json([
            'data' => $asientos,
        ]);
    }

    // Devuelve los asientos de un sector concreto junto con el precio
    public function porSector($eventoId, $sectorId)
    {
        $evento = Evento::findOrFail($eventoId);
        $sector = Sector::findOrFail($sectorId);

        if (!$evento->sectorEstaDisponible($sectorId)) {
            return response()->json([
                'error' => 'El sector no está disponible para este evento',
            ], 400);
        }

        $asientos = $sector->asientos()
            ->get()
            ->map(function ($asiento) use ($eventoId) {
                return [
                    'id'         => $asiento->id,
                    'fila'       => $asiento->fila,
                    'numero'     => $asiento->numero,
                    'disponible' => $asiento->estaDisponibleParaEvento($eventoId),
                ];
            });

        $precio = $evento->precioDelSector($sectorId);

        return response()->json([
            'data' => [
                'sector'   => $sector,
                'precio'   => $precio?->precio,
                'asientos' => $asientos,
            ],
        ]);
    }
}
