<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaUsuarios extends Migration
{
    public function up()
    {
        // Personas que pueden entrar al sistema
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'usuario' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            // Hash de password_hash(), nunca la contraseña en texto plano
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'ultimo_acceso' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('usuario');
        $this->forge->createTable('usuarios');
    }

    public function down()
    {
        $this->forge->dropTable('usuarios');
    }
}
