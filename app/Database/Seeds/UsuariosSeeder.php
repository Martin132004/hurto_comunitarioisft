<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Crea el usuario inicial para entrar al sistema.
 * Si ya existe no lo toca, así se puede volver a ejecutar sin pisar la contraseña.
 */
class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $tabla = $this->db->table('usuarios');

        if ($tabla->where('usuario', 'admin')->countAllResults() > 0) {
            return;
        }

        $tabla->insert([
            'nombre'        => 'Administrador',
            'usuario'       => 'admin',
            'password_hash' => password_hash('huerta2026', PASSWORD_DEFAULT),
            'activo'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
