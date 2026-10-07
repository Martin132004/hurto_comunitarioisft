<?php

namespace App\Libraries;

use App\Models\ProblemaModel;
use App\Models\ReporteProblemaModel;
use DateTime;
use DateTimeImmutable;

/**
 * Reúne los datos de un mes del huerto: producción, riego, siembras y problemas.
 * Los mismos datos se muestran en pantalla y se exportan a PDF.
 */
class ReporteMensual
{
    public const MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    private $db;

    public function __construct(private array $huerto, private ?array $zona, private PlanRiego $plan)
    {
        $this->db = db_connect();
    }

    // $mes en formato "2026-09"
    public function generar(string $mes): array
    {
        $inicio = new DateTimeImmutable($mes . '-01');
        $finMes = $inicio->modify('last day of this month');
        $hoy = new DateTimeImmutable('today');
        // El mes en curso se cuenta hasta hoy
        $parcial = $finMes > $hoy;
        $fin = $parcial ? $hoy : $finMes;

        $desde = $inicio->format('Y-m-d');
        $hasta = $fin->format('Y-m-d');

        $cultivos = $this->db->table('cultivos')->get()->getResultArray();
        $cosechas = $this->cosechas($desde, $hasta);
        $riego = $this->riego($cultivos, $inicio, $fin);
        $siembras = array_values(array_filter($cultivos, fn ($c) => $c['fecha_siembra'] >= $desde && $c['fecha_siembra'] <= $hasta));
        usort($siembras, fn ($a, $b) => strcmp($a['fecha_siembra'], $b['fecha_siembra']));
        foreach ($siembras as &$siembra) {
            $siembra['cosecha_estimada'] = date('Y-m-d', strtotime($siembra['fecha_siembra'] . ' +' . (int) $siembra['dias_cosecha_estimados'] . ' days'));
        }
        unset($siembra);
        $problemas = $this->problemas($desde, $hasta);

        $kg = array_sum(array_map(fn ($c) => (float) $c['kg'], $cosechas));
        $litros = array_sum(array_column($riego, 'litros'));
        $litrosPlan = array_sum(array_column($riego, 'litros_plan'));
        $registrados = array_sum(array_column($riego, 'registrados'));
        $programados = array_sum(array_column($riego, 'programados'));

        return [
            'numero'       => sprintf('%04d-%s', $this->huerto['id'] ?? 1, $inicio->format('Ym')),
            'periodo'      => self::MESES[(int) $inicio->format('n') - 1] . ' ' . $inicio->format('Y'),
            'desde'        => $inicio,
            'hasta'        => $fin,
            'parcial'      => $parcial,
            'emitido'      => new DateTime(),
            'huerto'       => $this->huerto,
            'zona'         => $this->zona,
            'metodo'       => $this->plan->metodo()['nombre'],
            'cosechas'     => $cosechas,
            'riego'        => $riego,
            'siembras'     => $siembras,
            'problemas'    => $problemas,
            'totales'      => [
                'kg'            => $kg,
                'sin_pesar'     => count(array_filter($cosechas, fn ($c) => $c['kg'] === null)),
                'litros'        => $litros,
                'litros_plan'   => $litrosPlan,
                'registrados'   => $registrados,
                'programados'   => $programados,
                // Cuánta agua llevó cada kg cosechado en el mes
                'litros_por_kg' => $kg > 0 ? $litros / $kg : null,
                'cumplimiento'  => $programados > 0 ? min(100, round($registrados / $programados * 100)) : null,
                'activos'       => count(array_filter($cultivos, fn ($c) => $c['fecha_siembra'] <= $hasta && ! $this->terminoAntesDe($c, $hasta))),
                'problemas'     => count($problemas),
                'resueltos'     => count(array_filter($problemas, fn ($p) => $p['estado'] === 'resuelto')),
            ],
        ];
    }

    private function cosechas(string $desde, string $hasta): array
    {
        if (! $this->db->tableExists('cosechas')) {
            return [];
        }

        return $this->db->table('cosechas c')
            ->select('c.fecha, c.kg, c.final, c.observaciones, cu.nombre_planta, cu.variedad')
            ->join('cultivos cu', 'cu.id = c.cultivo_id', 'left')
            ->where('c.fecha >=', $desde)
            ->where('c.fecha <=', $hasta)
            ->orderBy('c.fecha')
            ->get()->getResultArray();
    }

    // Riegos registrados contra los que correspondían según la frecuencia de cada cultivo
    private function riego(array $cultivos, DateTimeImmutable $inicio, DateTimeImmutable $fin): array
    {
        $registrados = [];
        if ($this->db->tableExists('riegos')) {
            $filas = $this->db->table('riegos')
                ->select('cultivo_id, COUNT(*) AS cantidad, SUM(litros) AS litros')
                ->where('fecha >=', $inicio->format('Y-m-d 00:00:00'))
                ->where('fecha <=', $fin->format('Y-m-d 23:59:59'))
                ->groupBy('cultivo_id')
                ->get()->getResultArray();
            foreach ($filas as $fila) {
                $registrados[$fila['cultivo_id']] = $fila;
            }
        }

        // Los litros del plan se calculan con el coeficiente de mitad de mes
        $mitad = $inicio->modify('+14 days');
        $filas = [];

        foreach ($cultivos as $cultivo) {
            $desde = max($inicio, new DateTimeImmutable($cultivo['fecha_siembra']));
            $hasta = $fin;
            $final = $this->fechaFinal($cultivo);
            if ($final && $final < $hasta) {
                $hasta = $final;
            }

            $dias = $desde <= $hasta ? (int) $desde->diff($hasta)->days + 1 : 0;
            $frecuencia = max(1, (int) $cultivo['frecuencia_riego_dias']);
            $programados = (int) ceil($dias / $frecuencia);
            $hecho = $registrados[$cultivo['id']] ?? null;
            unset($registrados[$cultivo['id']]);

            if ($programados === 0 && ! $hecho) {
                continue;
            }

            $filas[] = [
                'cultivo'     => $cultivo['nombre_planta'],
                'variedad'    => $cultivo['variedad'],
                'frecuencia'  => $frecuencia,
                'registrados' => (int) ($hecho['cantidad'] ?? 0),
                'programados' => $programados,
                'litros'      => (float) ($hecho['litros'] ?? 0),
                'litros_plan' => $programados * $this->plan->litrosAplicar((float) $cultivo['cantidad_riego_litros'], $mitad),
            ];
        }

        // Riegos de cultivos que ya se eliminaron
        foreach ($registrados as $hecho) {
            $filas[] = [
                'cultivo'     => 'Cultivo eliminado',
                'variedad'    => null,
                'frecuencia'  => null,
                'registrados' => (int) $hecho['cantidad'],
                'programados' => 0,
                'litros'      => (float) $hecho['litros'],
                'litros_plan' => 0,
            ];
        }

        return $filas;
    }

    private function problemas(string $desde, string $hasta): array
    {
        if (! $this->db->tableExists('reportes_problemas')) {
            return [];
        }

        $catalogo = $this->db->tableExists('problemas') ? (new ProblemaModel())->catalogo() : [];
        $filas = (new ReporteProblemaModel())
            ->where('huerto_id', $this->huerto['id'])
            ->where('created_at >=', $desde . ' 00:00:00')
            ->where('created_at <=', $hasta . ' 23:59:59')
            ->orderBy('created_at')
            ->findAll();

        foreach ($filas as &$fila) {
            $fila['nombre'] = $catalogo[$fila['problema']]['nombre'] ?? 'Sin identificar';
        }

        return $filas;
    }

    // Fecha de la última cosecha de un cultivo terminado
    private function fechaFinal(array $cultivo): ?DateTimeImmutable
    {
        if ($cultivo['estado'] !== 'Cosechado') {
            return null;
        }

        $final = $this->db->tableExists('cosechas')
            ? $this->db->table('cosechas')->selectMax('fecha')->where('cultivo_id', $cultivo['id'])->where('final', 1)->get()->getRow('fecha')
            : null;

        return new DateTimeImmutable($final ?? $cultivo['updated_at'] ?? 'today');
    }

    private function terminoAntesDe(array $cultivo, string $fecha): bool
    {
        $final = $this->fechaFinal($cultivo);

        return $final !== null && $final->format('Y-m-d') <= $fecha;
    }
}
