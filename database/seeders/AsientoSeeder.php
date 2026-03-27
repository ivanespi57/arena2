<?php

namespace Database\Seeders;

use App\Models\Asiento;
use App\Models\Sector;
use Illuminate\Database\Seeder;

class AsientoSeeder extends Seeder
{
    public function run(): void
    {
        $sectores     = Sector::all();
        $totalAsientos = 0;

        foreach ($sectores as $sector) {
            $count = $this->generarAsientosPorSector($sector);
            $totalAsientos += $count;
        }

        $this->command->info("✅ Asientos creados: {$totalAsientos}");
    }

    private function generarAsientosPorSector(Sector $sector): int
    {
        $count = 0;

        // Sectores 101-122 y 301-323: 20 filas x 15 asientos = 300
        if (preg_match('/^Sector (10[1-9]|1[1-2][0-9]|30[1-9]|3[1-2][0-9]|122|323)$/', $sector->nombre)) {
            for ($fila = 1; $fila <= 20; $fila++) {
                for ($numero = 1; $numero <= 15; $numero++) {
                    Asiento::create([
                        'sector_id' => $sector->id,
                        'fila'      => (string) $fila,
                        'numero'    => $numero,
                    ]);
                    $count++;
                }
            }
        }
        // Palcos: 1 fila x 8 asientos
        elseif (str_starts_with($sector->nombre, 'Palco')) {
            for ($numero = 1; $numero <= 8; $numero++) {
                Asiento::create([
                    'sector_id' => $sector->id,
                    'fila'      => 'A',
                    'numero'    => $numero,
                ]);
                $count++;
            }
        }
        // CLUB: 10 filas x 20 asientos
        elseif ($sector->nombre === 'CLUB') {
            for ($fila = 1; $fila <= 10; $fila++) {
                for ($numero = 1; $numero <= 20; $numero++) {
                    Asiento::create([
                        'sector_id' => $sector->id,
                        'fila'      => (string) $fila,
                        'numero'    => $numero,
                    ]);
                    $count++;
                }
            }
        }
        // JOHNNIE WALKER: 8 filas x 15 asientos
        elseif ($sector->nombre === 'JOHNNIE WALKER') {
            for ($fila = 1; $fila <= 8; $fila++) {
                for ($numero = 1; $numero <= 15; $numero++) {
                    Asiento::create([
                        'sector_id' => $sector->id,
                        'fila'      => (string) $fila,
                        'numero'    => $numero,
                    ]);
                    $count++;
                }
            }
        }
        // PISTA: 30 filas x 25 asientos
        elseif ($sector->nombre === 'PISTA') {
            for ($fila = 1; $fila <= 30; $fila++) {
                for ($numero = 1; $numero <= 25; $numero++) {
                    Asiento::create([
                        'sector_id' => $sector->id,
                        'fila'      => (string) $fila,
                        'numero'    => $numero,
                    ]);
                    $count++;
                }
            }
        }
        // FRONT STAGE: 5 filas x 30 asientos
        elseif ($sector->nombre === 'FRONT STAGE') {
            for ($fila = 1; $fila <= 5; $fila++) {
                for ($numero = 1; $numero <= 30; $numero++) {
                    Asiento::create([
                        'sector_id' => $sector->id,
                        'fila'      => (string) $fila,
                        'numero'    => $numero,
                    ]);
                    $count++;
                }
            }
        }

        return $count;
    }
}
