<?php

namespace App\Models;

use CodeIgniter\Model;

class EmergenciaModel extends Model
{
    protected $table      = 'emergencias';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tipo',
        'estado',
        'activa',
        'inicio',
        'fin',
        'pasos',
        'danos',
        'cultivos',
        'notas',
        'usuario_id',
    ];

    // La emergencia en curso, si hay una (el índice único de "activa" garantiza que sea una sola)
    public function activa(): ?array
    {
        return $this->where('activa', 1)->first();
    }

    // Convierte una columna "a,b,c" en lista
    public static function lista(?string $texto): array
    {
        return array_values(array_filter(explode(',', (string) $texto), fn ($v) => $v !== ''));
    }

    // Cultivos afectados guardados al cerrar: [['id' => , 'nombre' => , 'variedad' => ], ...]
    public static function afectados(?string $json): array
    {
        $lista = json_decode((string) $json, true);

        return is_array($lista) ? $lista : [];
    }
}
