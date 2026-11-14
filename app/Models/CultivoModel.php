<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class CultivoModel extends Model
{
    // Franjas horarias recomendadas para regar (clave guardada en la db => detalle)
    public const HORARIOS_RIEGO = [
        'manana' => ['nombre' => 'Mañana', 'franjas' => ['06:00 - 09:00']],
        'tarde'  => ['nombre' => 'Tarde',  'franjas' => ['18:00 - 20:00']],
        'ambos'  => ['nombre' => 'Mañana y tarde', 'franjas' => ['06:00 - 09:00', '18:00 - 20:00']],
    ];

    public const COSECHADO = 'Cosechado';

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

    // Solo los cultivos que todavía no se cosecharon (se puede seguir encadenando la consulta)
    public function enCurso(): static
    {
        return $this->where('estado !=', self::COSECHADO);
    }

    // Fecha estimada de cosecha: siembra + días a cosecha
    public static function fechaCosecha(array $cultivo): DateTime
    {
        return (new DateTime(substr($cultivo['fecha_siembra'], 0, 10)))->modify('+' . (int) $cultivo['dias_cosecha_estimados'] . ' days');
    }

    // Próximo riego: último riego (o la siembra, si nunca se regó) + frecuencia
    public static function proximoRiego(array $cultivo): DateTime
    {
        $desde = substr($cultivo['ultimo_riego'] ?: $cultivo['fecha_siembra'], 0, 10);

        return (new DateTime($desde))->modify('+' . max(1, (int) $cultivo['frecuencia_riego_dias']) . ' days');
    }
}
