<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Se quita el plano del huerto: su tabla de elementos, las medidas del terreno y el cantero de cada cultivo
class QuitarPlanoDelHuerto extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('cultivos', 'cantero_id');
        $this->forge->dropTable('plano_elementos', true);
        $this->forge->dropColumn('huertos', ['plano_ancho', 'plano_largo']);
    }

    // Vuelve a crear la estructura vacía (los elementos y ubicaciones que tenía no se recuperan)
    public function down()
    {
        $this->forge->addColumn('huertos', [
            'plano_ancho' => ['type' => 'DECIMAL', 'constraint' => '5,1', 'default' => 12, 'after' => 'reservorio_litros'],
            'plano_largo' => ['type' => 'DECIMAL', 'constraint' => '5,1', 'default' => 10, 'after' => 'plano_ancho'],
        ]);
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'huerto_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'tipo'         => ['type' => 'VARCHAR', 'constraint' => '20'],
            'nombre'       => ['type' => 'VARCHAR', 'constraint' => '60'],
            'x'            => ['type' => 'DECIMAL', 'constraint' => '6,2'],
            'y'            => ['type' => 'DECIMAL', 'constraint' => '6,2'],
            'ancho'        => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'largo'        => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'rotacion'     => ['type' => 'INT', 'default' => 0],
            'media_sombra' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('huerto_id');
        $this->forge->createTable('plano_elementos');
        $this->forge->addColumn('cultivos', [
            'cantero_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'id'],
        ]);
    }
}
