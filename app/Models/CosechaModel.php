<?php

namespace App\Models;

use CodeIgniter\Model;

// Cosechas de cada cultivo, con los kg obtenidos
class CosechaModel extends Model
{
    protected $table      = 'cosechas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'cultivo_id',
        'fecha',
        'kg',
        'final',
        'observaciones',
    ];

    protected $useTimestamps = true;

    // Kg cosechados por cultivo: [cultivo_id => kg]
    public function kgPorCultivo(): array
    {
        $filas = $this->builder()
            ->select('cultivo_id, SUM(kg) AS kg')
            ->where('kg IS NOT NULL')
            ->groupBy('cultivo_id')
            ->get()->getResultArray();

        return array_map('floatval', array_column($filas, 'kg', 'cultivo_id'));
    }
}
