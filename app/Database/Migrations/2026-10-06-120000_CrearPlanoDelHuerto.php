<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearPlanoDelHuerto extends Migration
{
    public function up()
    {
        // Terreno del huerto para el plano 3D: medidas, orientación y ubicación (para calcular el sol)
        $this->forge->addColumn('huertos', [
            'plano_ancho' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 12,
                'after'      => 'reservorio_litros',
            ],
            'plano_largo' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 10,
                'after'      => 'plano_ancho',
            ],
            // Grados que hay que girar desde el lado de arriba del plano para mirar al norte (sentido horario)
            'norte_grados' => [
                'type'    => 'INT',
                'default' => 0,
                'after'   => 'plano_largo',
            ],
            'latitud' => [
                'type'       => 'DECIMAL',
                'constraint' => '7,4',
                'default'    => -31.5375,
                'after'      => 'norte_grados',
            ],
            'longitud' => [
                'type'       => 'DECIMAL',
                'constraint' => '7,4',
                'default'    => -68.5364,
                'after'      => 'latitud',
            ],
            // Dirección desde la que sopla el viento fuerte de la zona (0 = norte, 270 = oeste); null si no se indicó
            'viento_desde' => [
                'type'  => 'INT',
                'null'  => true,
                'after' => 'longitud',
            ],
        ]);

        // Elementos del plano: canteros, tablones, macetas y lo que da sombra o reparo (árboles, muros, tanques)
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
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '60',
            ],
            // Centro del elemento en metros, desde la esquina superior izquierda del terreno
            'x' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'z' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'ancho' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'largo' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'alto' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'rotacion' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            // Media sombra colocada sobre el cantero
            'media_sombra' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
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
        $this->forge->addKey('huerto_id');
        $this->forge->createTable('plano_elementos');

        // Cantero donde está plantado cada cultivo
        $this->forge->addColumn('cultivos', [
            'cantero_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'id',
            ],
        ]);

        // Siembras planificadas: cuando se siembran pasan a ser un cultivo (cultivo_id)
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
            'cantero_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'especie' => [
                'type'       => 'VARCHAR',
                'constraint' => '60',
            ],
            'variedad' => [
                'type'       => 'VARCHAR',
                'constraint' => '60',
                'null'       => true,
            ],
            'fecha' => [
                'type' => 'DATE',
            ],
            'dias_cosecha' => [
                'type' => 'INT',
            ],
            // Cantidad de plantas (null si no se indicó)
            'plantas' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'notas' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'cultivo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addKey(['huerto_id', 'fecha']);
        $this->forge->createTable('siembras_planificadas');
    }

    public function down()
    {
        $this->forge->dropTable('siembras_planificadas', true);
        $this->forge->dropColumn('cultivos', 'cantero_id');
        $this->forge->dropTable('plano_elementos', true);
        $this->forge->dropColumn('huertos', ['plano_ancho', 'plano_largo', 'norte_grados', 'latitud', 'longitud', 'viento_desde']);
    }
}
