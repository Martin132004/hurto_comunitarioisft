<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaEmergencias extends Migration
{
    public function up()
    {
        // Emergencias climáticas avisadas por el huertero (Zonda, granizo, helada) y los daños que dejaron
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            // activa, terminada o cancelada (falsa alarma)
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'activa',
            ],
            // 1 mientras está activa y NULL al cerrarse: el índice único impide dos activas a la vez
            'activa' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
            ],
            'inicio' => [
                'type' => 'DATETIME',
            ],
            'fin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Claves de los pasos ya hechos (plan y reparación), separadas por coma
            'pasos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Claves de los daños encontrados al terminar, separadas por coma
            'danos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Cultivos afectados en JSON (id, nombre y variedad al cerrar), así el historial sobrevive si se borran
            'cultivos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'usuario_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('estado');
        $this->forge->addUniqueKey('activa');
        $this->forge->createTable('emergencias');
    }

    public function down()
    {
        $this->forge->dropTable('emergencias');
    }
}
