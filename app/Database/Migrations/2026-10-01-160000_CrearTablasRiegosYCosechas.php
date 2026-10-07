<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablasRiegosYCosechas extends Migration
{
    public function up()
    {
        // Historial de riegos: cultivos.ultimo_riego solo guarda el último
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cultivo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'fecha' => [
                'type' => 'DATETIME',
            ],
            // Litros aplicados, ya ajustados al mes y al método de riego
            'litros' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'default'    => 0,
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
        $this->forge->addKey(['cultivo_id', 'fecha']);
        $this->forge->createTable('riegos');

        // Cosechas: un cultivo puede cosecharse varias veces (tomate, acelga) antes de terminar
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cultivo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'fecha' => [
                'type' => 'DATE',
            ],
            // null cuando no se pesó
            'kg' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ],
            // 1 si con esta cosecha terminó el cultivo
            'final' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'observaciones' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->addKey(['cultivo_id', 'fecha']);
        $this->forge->createTable('cosechas');

        // Pasamos al historial lo que ya estaba cargado
        $ahora = date('Y-m-d H:i:s');
        foreach ($this->db->table('cultivos')->get()->getResultArray() as $cultivo) {
            if ($cultivo['ultimo_riego']) {
                $this->db->table('riegos')->insert([
                    'cultivo_id' => $cultivo['id'],
                    'fecha'      => $cultivo['ultimo_riego'],
                    'litros'     => $cultivo['cantidad_riego_litros'],
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
            if ($cultivo['estado'] === 'Cosechado') {
                $this->db->table('cosechas')->insert([
                    'cultivo_id' => $cultivo['id'],
                    'fecha'      => substr($cultivo['updated_at'] ?? $ahora, 0, 10),
                    'kg'         => null,
                    'final'      => 1,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('cosechas');
        $this->forge->dropTable('riegos');
    }
}
