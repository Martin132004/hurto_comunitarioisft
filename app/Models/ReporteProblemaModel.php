<?php

namespace App\Models;

use CodeIgniter\Model;

// Problemas reportados por los huertos y la respuesta del técnico
class ReporteProblemaModel extends Model
{
    public const GRAVEDADES = [
        'pocas' => ['nombre' => 'Pocas plantas', 'detalle' => 'Algunas hojas o una planta', 'color' => 'success'],
        'varias' => ['nombre' => 'Varias plantas', 'detalle' => 'Se está extendiendo', 'color' => 'warning'],
        'todo'  => ['nombre' => 'Casi todo el cultivo', 'detalle' => 'Peligra la cosecha', 'color' => 'danger'],
    ];

    public const ESTADOS = [
        'abierto'    => ['nombre' => 'Esperando respuesta', 'color' => 'warning', 'icono' => 'bi-hourglass-split'],
        'respondido' => ['nombre' => 'Respondido', 'color' => 'primary', 'icono' => 'bi-chat-left-text'],
        'resuelto'   => ['nombre' => 'Resuelto', 'color' => 'success', 'icono' => 'bi-check2-circle'],
    ];

    // Días hacia atrás en los que un problema repetido en varios cultivos se considera reciente
    public const DIAS_RECIENTES = 21;

    protected $table      = 'reportes_problemas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'huerto_id',
        'zona',
        'cultivo_id',
        'cultivo_nombre',
        'problema',
        'descripcion',
        'gravedad',
        'foto',
        'estado',
        'respuesta',
        'respondido_por',
        'respondido_at',
        'resuelto_at',
    ];

    protected $useTimestamps = true;

    private function desde(): string
    {
        return date('Y-m-d H:i:s', strtotime('-' . self::DIAS_RECIENTES . ' days'));
    }

    // Problemas sin resolver que aparecen en más de un cultivo del huerto: [clave => cultivos afectados]
    public function repetidosEnHuerto(?int $huertoId): array
    {
        $filas = $this->builder()
            ->select('problema, GROUP_CONCAT(DISTINCT cultivo_nombre SEPARATOR ", ") AS cultivos, COUNT(DISTINCT cultivo_nombre) AS cantidad')
            ->where('huerto_id', $huertoId)
            ->where('problema IS NOT NULL')
            ->where('estado !=', 'resuelto')
            ->where('created_at >=', $this->desde())
            ->groupBy('problema')
            ->having('cantidad >=', 2)
            ->get()->getResultArray();

        return array_column($filas, 'cultivos', 'problema');
    }

    // Cantidad de reportes sin resolver por cultivo: [cultivo_id => cantidad]
    public function abiertosPorCultivo(?int $huertoId): array
    {
        $filas = $this->builder()
            ->select('cultivo_id, COUNT(*) AS cantidad')
            ->where('huerto_id', $huertoId)
            ->where('cultivo_id IS NOT NULL')
            ->where('estado !=', 'resuelto')
            ->groupBy('cultivo_id')
            ->get()->getResultArray();

        return array_map('intval', array_column($filas, 'cantidad', 'cultivo_id'));
    }
}
