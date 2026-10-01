<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarPlanRiegoACultivos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('cultivos', [
            'horario_riego' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'manana',
                'after'      => 'frecuencia_riego_dias',
            ],
            'cantidad_riego_litros' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '1.00',
                'after'      => 'horario_riego',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('cultivos', ['horario_riego', 'cantidad_riego_litros']);
    }
}
