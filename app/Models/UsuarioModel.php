<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombre',
        'usuario',
        'password_hash',
        'activo',
        'ultimo_acceso',
        'created_at',
    ];

    /**
     * Devuelve el usuario si los datos son correctos y está activo; null en cualquier otro caso.
     */
    public function verificar(string $usuario, string $password): ?array
    {
        $fila = $this->where('usuario', $usuario)->where('activo', 1)->first();

        if (! $fila || ! password_verify($password, $fila['password_hash'])) {
            return null;
        }

        return $fila;
    }
}
