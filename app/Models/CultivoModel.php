<?php

namespace App\Models;

use CodeIgniter\Model;

class CultivoModel extends Model
{
    // Franjas horarias recomendadas para regar (clave guardada en la db => detalle)
    public const HORARIOS_RIEGO = [
        'manana' => ['nombre' => 'Mañana', 'franjas' => ['06:00 - 09:00']],
        'tarde'  => ['nombre' => 'Tarde',  'franjas' => ['18:00 - 20:00']],
        'ambos'  => ['nombre' => 'Mañana y tarde', 'franjas' => ['06:00 - 09:00', '18:00 - 20:00']],
    ];

    protected $table      = 'cultivos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombre_planta',
        'variedad',
        'fecha_siembra',
        'dias_cosecha_estimados',
        'frecuencia_riego_dias',
        'horario_riego',
        'cantidad_riego_litros',
        'ultimo_riego',
        'estado'
    ];

    protected $useTimestamps = true;
}