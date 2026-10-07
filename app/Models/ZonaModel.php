<?php

namespace App\Models;

use CodeIgniter\Model;

class ZonaModel extends Model
{
    protected $table      = 'zonas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'clave',
        'nombre',
        'departamentos',
        'altitud_m',
        'ultima_helada',
        'primera_helada',
        'desfase_meses',
        'coef_riego_mensual',
        'descripcion',
    ];

    public function porClave(?string $clave): ?array
    {
        return $clave ? $this->where('clave', $clave)->first() : null;
    }
}
