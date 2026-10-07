<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Catálogo de especies usado por la guía de cultivo del formulario de alta
class CrearTablaEspecies extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            // Otros nombres con los que el usuario puede escribirla, separados por coma
            'sinonimos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'familia' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'dias_cosecha' => [
                'type' => 'INT',
            ],
            'frecuencia_riego_dias' => [
                'type' => 'INT',
            ],
            'horario_riego' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'manana',
            ],
            'litros_riego' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            // Números de mes (1 a 12) separados por coma, para la zona de referencia
            'meses_almacigo' => [
                'type'       => 'VARCHAR',
                'constraint' => '40',
                'null'       => true,
            ],
            // Siembra directa o trasplante al lugar definitivo
            'meses_siembra' => [
                'type'       => 'VARCHAR',
                'constraint' => '40',
            ],
            // Las especies sensibles a la helada corren su calendario en las zonas más frías
            'sensible_helada' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'tolerancia_sal' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'Media',
            ],
            'tipo_siembra' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'exposicion' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'distancia_cm' => [
                'type' => 'INT',
            ],
            'profundidad_cm' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,1',
            ],
            // Nombres de otras especies del catálogo, separados por coma
            'buenos_vecinos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'malos_vecinos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'consejos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nombre');

        $this->forge->createTable('especies');
    }

    public function down()
    {
        $this->forge->dropTable('especies');
    }
}
