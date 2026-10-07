<?php

namespace App\Models;

use CodeIgniter\Model;

class SiembraPlanificadaModel extends Model
{
    protected $table      = 'siembras_planificadas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'huerto_id',
        'cantero_id',
        'especie',
        'variedad',
        'fecha',
        'dias_cosecha',
        'plantas',
        'notas',
        'cultivo_id',
    ];

    protected $useTimestamps = true;

    // Siembras que todavía no se hicieron, de la más próxima a la más lejana
    public function pendientes(?int $huertoId): array
    {
        return $this->where('huerto_id', $huertoId)->where('cultivo_id', null)->orderBy('fecha')->findAll();
    }
}
