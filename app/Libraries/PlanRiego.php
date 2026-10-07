<?php

namespace App\Libraries;

use App\Models\HuertoModel;
use DateTime;
use DateTimeInterface;

/**
 * Cálculos de riego según el sistema que eligió el huerto:
 * - litros ajustados al mes (zona) y a la eficiencia del método de riego;
 * - días de turno de agua y agua que hay que guardar entre turnos.
 */
class PlanRiego
{
    public function __construct(private array $huerto, private ?array $zona = null)
    {
    }

    // ---------- Litros ----------

    // Factor del mes: más de 1 en verano, menos de 1 en invierno (1 si no hay zona elegida)
    public function coeficiente(DateTimeInterface $fecha): float
    {
        $coeficientes = array_map('floatval', explode(',', (string) ($this->zona['coef_riego_mensual'] ?? '')));

        return $coeficientes[(int) $fecha->format('n') - 1] ?? 1.0;
    }

    public function metodo(): array
    {
        return HuertoModel::METODOS_RIEGO[$this->huerto['metodo_riego']] ?? HuertoModel::METODOS_RIEGO['manguera'];
    }

    // Litros que hay que aplicar en una fecha, a partir de lo que necesita la planta en temporada templada
    public function litrosAplicar(float $litrosPlanta, DateTimeInterface $fecha): float
    {
        return round($litrosPlanta * $this->coeficiente($fecha) / $this->metodo()['eficiencia'], 1);
    }

    public function litrosSemanales(array $cultivos, DateTimeInterface $fecha): float
    {
        $total = 0;
        foreach ($cultivos as $cultivo) {
            $frecuencia = max(1, (int) $cultivo['frecuencia_riego_dias']);
            $total += $this->litrosAplicar((float) $cultivo['cantidad_riego_litros'], $fecha) * 7 / $frecuencia;
        }

        return round($total, 1);
    }

    // ---------- Turno de agua ----------

    public function usaTurno(): bool
    {
        if ($this->huerto['fuente_agua'] !== 'turno') {
            return false;
        }

        return $this->huerto['turno_modo'] === 'semana'
            ? $this->diasSemanaTurno() !== []
            : (int) $this->huerto['turno_cada_dias'] > 0 && ! empty($this->huerto['turno_referencia']);
    }

    public function esDiaDeTurno(DateTimeInterface $fecha): bool
    {
        if (! $this->usaTurno()) {
            return false;
        }

        if ($this->huerto['turno_modo'] === 'semana') {
            return in_array((int) $fecha->format('N'), $this->diasSemanaTurno(), true);
        }

        $referencia = new DateTime($this->huerto['turno_referencia']);
        $dia = new DateTime($fecha->format('Y-m-d'));
        $dias = (int) $referencia->diff($dia)->format('%r%a');

        return $dias % (int) $this->huerto['turno_cada_dias'] === 0;
    }

    /** @return DateTime[] */
    public function proximosTurnos(DateTimeInterface $desde, int $cantidad = 3): array
    {
        $turnos = [];
        $dia = new DateTime($desde->format('Y-m-d'));

        for ($i = 0; $i < 60 && count($turnos) < $cantidad && $this->usaTurno(); $i++) {
            if ($this->esDiaDeTurno($dia)) {
                $turnos[] = clone $dia;
            }
            $dia->modify('+1 day');
        }

        return $turnos;
    }

    // Mayor cantidad de días que pueden pasar entre un turno y el siguiente
    public function diasEntreTurnos(): ?int
    {
        if (! $this->usaTurno()) {
            return null;
        }

        if ($this->huerto['turno_modo'] !== 'semana') {
            return (int) $this->huerto['turno_cada_dias'];
        }

        $dias = $this->diasSemanaTurno();
        $mayor = $dias[0] + 7 - end($dias);
        for ($i = 1; $i < count($dias); $i++) {
            $mayor = max($mayor, $dias[$i] - $dias[$i - 1]);
        }

        return $mayor;
    }

    // Riegos que un cultivo necesita entre dos turnos (sin contar el del día del turno)
    public function riegosEntreTurnos(int $frecuencia): int
    {
        $intervalo = $this->diasEntreTurnos();

        return $intervalo ? max(0, (int) ceil($intervalo / max(1, $frecuencia)) - 1) : 0;
    }

    // Litros que hay que tener guardados para cubrir los riegos entre turnos
    public function aguaEntreTurnos(array $cultivos, DateTimeInterface $fecha): float
    {
        $total = 0;
        foreach ($cultivos as $cultivo) {
            $riegos = $this->riegosEntreTurnos((int) $cultivo['frecuencia_riego_dias']);
            $total += $riegos * $this->litrosAplicar((float) $cultivo['cantidad_riego_litros'], $fecha);
        }

        return round($total, 1);
    }

    // Resumen del mes para mostrar en el panel y en "Mi huerto"
    public function resumen(array $cultivosActivos, DateTimeInterface $fecha): array
    {
        $entreTurnos = $this->aguaEntreTurnos($cultivosActivos, $fecha);
        $reservorio = (int) $this->huerto['reservorio_litros'];

        return [
            'coeficiente'        => $this->coeficiente($fecha),
            'metodo'             => $this->metodo(),
            'litros_semana'      => $this->litrosSemanales($cultivosActivos, $fecha),
            'usa_turno'          => $this->usaTurno(),
            'proximos_turnos'    => $this->proximosTurnos($fecha),
            'dias_entre_turnos'  => $this->diasEntreTurnos(),
            'agua_entre_turnos'  => $entreTurnos,
            'reservorio'         => $reservorio,
            'reservorio_alcanza' => $entreTurnos <= $reservorio,
        ];
    }

    private function diasSemanaTurno(): array
    {
        $dias = array_map('intval', array_filter(explode(',', (string) $this->huerto['turno_dias_semana'])));
        sort($dias);

        return array_values(array_unique($dias));
    }
}
