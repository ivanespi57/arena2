<?php

namespace App\Services;

use App\Models\EstadoAsiento;
use Illuminate\Support\Facades\Log;

class LiberarReservasService
{
    // Elimina reservas expiradas y devuelve cuántas se borraron
    public function liberarExpiradas(): int
    {
        $expiradas = EstadoAsiento::expirados()->get();

        $count = 0;

        foreach ($expiradas as $reserva) {
            $reserva->delete();
            $count++;

            Log::info('Reserva expirada liberada', [
                'reserva_id' => $reserva->id,
                'evento_id'  => $reserva->evento_id,
                'asiento_id' => $reserva->asiento_id,
                'user_id'    => $reserva->user_id,
            ]);
        }

        return $count;
    }

    // Igual que el método anterior pero filtrado por usuario
    public function liberarDeUsuario($userId): int
    {
        $expiradas = EstadoAsiento::expirados()
            ->where('user_id', $userId)
            ->get();

        foreach ($expiradas as $reserva) {
            $reserva->delete();
        }

        return $expiradas->count();
    }
}
