<?php

namespace App\Models;

use CodeIgniter\Model;

// Historial de riegos de cada cultivo
class RiegoModel extends Model
{
    protected $table      = 'riegos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'cultivo_id',
        'fecha',
        'litros',
    ];

    protected $useTimestamps = true;
}
