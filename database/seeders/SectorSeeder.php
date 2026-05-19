<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectores = [];

        for ($i = 101; $i <= 122; $i++) { // Gradas laterales
            $sectores[] = ['nombre' => "Sector $i", 'descripcion' => 'Grada lateral', 'activo' => true];
        }

        for ($i = 301; $i <= 323; $i++) { // Gradas superiores
            $sectores[] = ['nombre' => "Sector $i", 'descripcion' => 'Grada superior', 'activo' => true];
        }

        for ($i = 1; $i <= 22; $i++) { // Palcos VIP
            $sectores[] = ['nombre' => "Palco $i", 'descripcion' => 'Palco VIP', 'activo' => true];
        }

        // Zonas especiales sin numeración
        $sectores[] = ['nombre' => 'CLUB',           'descripcion' => 'Zona Club',          'activo' => true];
        $sectores[] = ['nombre' => 'JOHNNIE WALKER', 'descripcion' => 'Zona Johnnie Walker', 'activo' => true];
        $sectores[] = ['nombre' => 'PISTA',          'descripcion' => 'Pista central',       'activo' => true];
        $sectores[] = ['nombre' => 'FRONT STAGE',    'descripcion' => 'Frente al escenario', 'activo' => true];

        $now = now();
        $sectoresConTimestamps = array_map(function ($sector) use ($now) {
            return array_merge($sector, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $sectores);

        Sector::insert($sectoresConTimestamps);

        $this->command->info('✅ Sectores creados: ' . count($sectores));
    }
}
