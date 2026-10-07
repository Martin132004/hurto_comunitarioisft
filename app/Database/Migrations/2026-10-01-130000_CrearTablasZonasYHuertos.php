<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablasZonasYHuertos extends Migration
{
    public function up()
    {
        // Zonas agroclimáticas: definen las fechas de helada y cuánto se corre el calendario de siembra
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
                'constraint' => '60',
            ],
            'departamentos' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'altitud_m' => [
                'type' => 'INT',
            ],
            // Fechas medias en formato MM-DD
            'ultima_helada' => [
                'type'       => 'CHAR',
                'constraint' => 5,
            ],
            'primera_helada' => [
                'type'       => 'CHAR',
                'constraint' => 5,
            ],
            // Meses que se atrasa la siembra de las especies sensibles a la helada
            'desfase_meses' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('clave');
        $this->forge->createTable('zonas');

        // Datos del huerto: por ahora el sistema gestiona un solo huerto
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            // Se guarda la clave de la zona para que recargar el catálogo no rompa la referencia
            'zona' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
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
        $this->forge->createTable('huertos');

        $this->db->table('huertos')->insert([
            'nombre'     => 'Huerto Comunitario',
            'zona'       => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('huertos');
        $this->forge->dropTable('zonas');
    }
}
