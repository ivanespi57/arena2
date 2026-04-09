<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sector;
use App\Models\Asiento;
use App\Models\Evento;
use App\Models\Precio;
use App\Models\EstadoAsiento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompraTest extends TestCase
{
    use RefreshDatabase;

    private function crearReservaConPrecio(User $user, array $extra = []): EstadoAsiento
    {
        $sector  = Sector::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        $evento  = Evento::factory()->create();
        Precio::factory()->create([
            'evento_id' => $evento->id,
            'sector_id' => $sector->id,
        ]);

        return EstadoAsiento::factory()->create(array_merge([
            'user_id'         => $user->id,
            'evento_id'       => $evento->id,
            'asiento_id'      => $asiento->id,
            'estado'          => 'bloqueado',
            'reservado_hasta' => now()->addMinutes(10),
        ], $extra));
    }

    public function test_usuario_puede_confirmar_compra()
    {
        $user    = User::factory()->create();
        $reserva = $this->crearReservaConPrecio($user);

        $response = $this->actingAs($user)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('entradas', [
            'user_id'   => $user->id,
            'evento_id' => $reserva->evento_id,
        ]);
        $this->assertDatabaseHas('estado_asientos', [
            'id'     => $reserva->id,
            'estado' => 'vendido',
        ]);
    }

    public function test_no_puede_comprar_reserva_expirada()
    {
        $user    = User::factory()->create();
        $reserva = EstadoAsiento::factory()->expirado()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        $response->assertStatus(400);
    }

    public function test_no_puede_comprar_reserva_de_otro_usuario()
    {
        $user1   = User::factory()->create();
        $user2   = User::factory()->create();
        $reserva = EstadoAsiento::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        $response->assertStatus(400);
    }

    public function test_entrada_genera_codigo_qr_automaticamente()
    {
        $user    = User::factory()->create();
        $reserva = $this->crearReservaConPrecio($user);

        $this->actingAs($user)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        $entrada = $user->entradas()->first();
        $this->assertNotNull($entrada->codigo_qr);
        $this->assertEquals(15, strlen($entrada->codigo_qr));
    }
}
