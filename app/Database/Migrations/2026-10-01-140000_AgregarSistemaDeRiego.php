<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarSistemaDeRiego extends Migration
{
    public function up()
    {
        // 12 factores (enero a diciembre) que ajustan los litros según la demanda de agua de cada mes
        $this->forge->addColumn('zonas', [
            'coef_riego_mensual' => [
                'type'       => 'VARCHAR',
                'constraint' => '120',
                'null'       => true,
                'after'      => 'desfase_meses',
            ],
        ]);

        // Sistema de riego que eligió cada huerto
        $this->forge->addColumn('huertos', [
            'fuente_agua' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'red',
                'after'      => 'zona',
            ],
            'metodo_riego' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'manguera',
                'after'      => 'fuente_agua',
            ],
            // Turno de canal: cada N días a partir de una fecha, o días fijos de la semana
            'turno_modo' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'metodo_riego',
            ],
            'turno_cada_dias' => [
                'type'  => 'INT',
                'null'  => true,
                'after' => 'turno_modo',
            ],
            'turno_referencia' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'turno_cada_dias',
            ],
            // Días ISO separados por coma (1 = lunes ... 7 = domingo)
            'turno_dias_semana' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'turno_referencia',
            ],
            'reservorio_litros' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
                'after'    => 'turno_dias_semana',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('huertos', ['fuente_agua', 'metodo_riego', 'turno_modo', 'turno_cada_dias', 'turno_referencia', 'turno_dias_semana', 'reservorio_litros']);
        $this->forge->dropColumn('zonas', 'coef_riego_mensual');
    }
}
