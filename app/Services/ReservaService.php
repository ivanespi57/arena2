<?php

namespace App\Services;

use App\Models\Asiento;
use App\Models\EstadoAsiento;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;

class ReservaService
{
    // Reserva un asiento durante 15 minutos; usa bloqueo pesimista para evitar dobles reservas
    public function reservarAsiento($eventoId, $asientoId, $userId)
    {
        DB::beginTransaction();
        try {
            $existeReserva = EstadoAsiento::where('evento_id', $eventoId)
                ->where('asiento_id', $asientoId)
                ->lockForUpdate()
                ->first();

            if ($existeReserva) {
                throw new \Exception('El asiento no está disponible');
            }

            $asiento = Asiento::findOrFail($asientoId);
            $evento  = Evento::findOrFail($eventoId);

            $this->verificarSectorDisponible($evento, $asiento->sector_id);

            $reserva = EstadoAsiento::create([
                'evento_id'       => $eventoId,
                'asiento_id'      => $asientoId,
                'user_id'         => $userId,
                'estado'          => 'bloqueado',
                'reservado_hasta' => now()->addMinutes(15),
            ]);

            DB::commit();

            return $reserva->load(['evento', 'asiento.sector']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Elimina la reserva si pertenece al usuario y sigue bloqueada
    public function cancelarReserva($reservaId, $userId)
    {
        $reserva = EstadoAsiento::where('id', $reservaId)
            ->where('user_id', $userId)
            ->where('estado', 'bloqueado')
            ->firstOrFail();

        $reserva->delete();

        return true;
    }

    // Devuelve las reservas bloqueadas que aún no han caducado
    public function obtenerReservasActivas($userId)
    {
        return EstadoAsiento::where('user_id', $userId)
            ->where('estado', 'bloqueado')
            ->where('reservado_hasta', '>', now())
            ->with(['evento', 'asiento.sector'])
            ->get();
    }

    // Lanza excepción si el sector no está disponible para el evento
    private function verificarSectorDisponible($evento, $sectorId)
    {
        $precio = $evento->precioDelSector($sectorId);

        if (!$precio || !$precio->disponible) {
            throw new \Exception('El sector no está disponible para este evento');
        }
    }
}
