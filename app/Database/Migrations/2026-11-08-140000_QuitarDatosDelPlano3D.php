<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// El plano ahora es 2D: se borran los datos que solo usaba el 3D (norte, ubicación y viento para el sol
// y el reparo, y el alto de cada elemento para las sombras) y la posición pasa a llamarse x e y
class QuitarDatosDelPlano3D extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('huertos', ['norte_grados', 'latitud', 'longitud', 'viento_desde']);
        $this->forge->dropColumn('plano_elementos', 'alto');
        $this->forge->modifyColumn('plano_elementos', [
            'z' => ['name' => 'y', 'type' => 'DECIMAL', 'constraint' => '6,2', 'null' => false],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('plano_elementos', [
            'y' => ['name' => 'z', 'type' => 'DECIMAL', 'constraint' => '6,2', 'null' => false],
        ]);
        $this->forge->addColumn('plano_elementos', [
            'alto' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.3, 'after' => 'largo'],
        ]);
        $this->forge->addColumn('huertos', [
            'norte_grados' => ['type' => 'INT', 'default' => 0, 'after' => 'plano_largo'],
            'latitud'      => ['type' => 'DECIMAL', 'constraint' => '7,4', 'default' => -31.5375, 'after' => 'norte_grados'],
            'longitud'     => ['type' => 'DECIMAL', 'constraint' => '7,4', 'default' => -68.5364, 'after' => 'latitud'],
            'viento_desde' => ['type' => 'INT', 'null' => true, 'after' => 'longitud'],
        ]);
    }
}
