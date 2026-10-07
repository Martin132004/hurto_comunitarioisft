<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablasProblemas extends Migration
{
    public function up()
    {
        // Catálogo de plagas, enfermedades y daños frecuentes, con su manejo
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'clave' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '80',
            ],
            // plaga, enfermedad, fisiopatia, clima o animal
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            // Especies o familias del catálogo separadas por coma; "*" afecta a todas
            'afecta' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            // Meses de mayor riesgo en la zona de referencia (1 a 12, separados por coma)
            'meses_riesgo' => [
                'type'       => 'VARCHAR',
                'constraint' => '40',
                'null'       => true,
            ],
            // Listas de una línea por ítem
            'sintomas' => [
                'type' => 'TEXT',
            ],
            'manejo' => [
                'type' => 'TEXT',
            ],
            'prevencion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('clave');
        $this->forge->createTable('problemas');

        // Problemas que reportan los huertos
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'huerto_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            // Zona del huerto al momento del reporte: permite detectar el mismo problema en varias huertas de una zona
            'zona' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            // Se guarda también el nombre por si después se elimina el cultivo
            'cultivo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'cultivo_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            // Clave del catálogo; null cuando el huertero no sabe qué es
            'problema' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // pocas, varias o todo
            'gravedad' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'pocas',
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            // abierto, respondido o resuelto
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'default'    => 'abierto',
            ],
            'respuesta' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'respondido_por' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'respondido_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resuelto_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey(['zona', 'problema', 'created_at']);
        $this->forge->createTable('reportes_problemas');
    }

    public function down()
    {
        $this->forge->dropTable('reportes_problemas');
        $this->forge->dropTable('problemas');
    }
}
