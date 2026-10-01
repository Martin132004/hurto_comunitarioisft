<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaCultivos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre_planta' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'variedad' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'fecha_siembra' => [
                'type' => 'DATE',
            ],
            'dias_cosecha_estimados' => [
                'type' => 'INT',
            ],
            'frecuencia_riego_dias' => [
                'type' => 'INT',
            ],
            'ultimo_riego' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'En Crecimiento',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('cultivos');
    }

    public function down()
    {
        $this->forge->dropTable('cultivos');
    }
}