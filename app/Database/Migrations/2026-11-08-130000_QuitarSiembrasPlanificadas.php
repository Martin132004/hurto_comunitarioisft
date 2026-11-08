<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// El plano ya no planifica siembras: la tabla queda sin uso y se elimina
class QuitarSiembrasPlanificadas extends Migration
{
    public function up()
    {
        $this->forge->dropTable('siembras_planificadas', true);
    }

    // Vuelve a crear la tabla vacía (las siembras que tenía no se recuperan)
    public function down()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'huerto_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cantero_id'   => ['type' => 'INT', 'unsigned' => true],
            'especie'      => ['type' => 'VARCHAR', 'constraint' => '60'],
            'variedad'     => ['type' => 'VARCHAR', 'constraint' => '60', 'null' => true],
            'fecha'        => ['type' => 'DATE'],
            'dias_cosecha' => ['type' => 'INT'],
            'plantas'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'notas'        => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'cultivo_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['huerto_id', 'fecha']);
        $this->forge->createTable('siembras_planificadas');
    }
}
