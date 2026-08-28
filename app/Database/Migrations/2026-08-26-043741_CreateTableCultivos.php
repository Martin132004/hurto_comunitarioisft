<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaCultivos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ // Clave primaria auto-incremental (INT)
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre_planta' => [ // Nombre común del cultivo (VARCHAR)
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'variedad' => [ // Subtipo o variedad (VARCHAR, opcional)
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true, 
            ],
            'fecha_siembra' => [ // Fecha de plantación (DATE)
                'type' => 'DATE',
            ],
            'dias_cosecha_estimados' => [ // Días aproximados para la cosecha (INT)
                'type' => 'INT',
            ],
            'frecuencia_riego_dias' => [ // Cada cuántos días requiere riego (INT)
                'type' => 'INT',
            ],
            'ultimo_riego' => [ // Fecha y hora del último riego registrado (DATETIME o DATE)
                'type' => 'DATETIME',
                'null' => true,
            ],
            'estado' => [ // Estado actual del cultivo (ENUM o VARCHAR)
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'En Crecimiento',
            ],
            'created_at' => [ // Gestión automática de fechas por el framework
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [ // Gestión automática de fechas por el framework
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Asignamos la clave primaria
        $this->forge->addKey('id', true);
        
        // Creamos la tabla llamada 'cultivos'
        $this->forge->createTable('cultivos');
    }

    public function down()
    {
        // Esto elimina la tabla si deshaces la migración
        $this->forge->dropTable('cultivos');
    }
}