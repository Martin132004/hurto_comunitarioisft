<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

// Carga todos los datos de referencia: zonas, especies y problemas
class CatalogoSeeder extends Seeder
{
    public function run()
    {
        $this->call('ZonasSeeder');
        $this->call('EspeciesSeeder');
        $this->call('ProblemasSeeder');
    }
}
