<?php

namespace App\Models;

use CodeIgniter\Model;

class HuertoModel extends Model
{
    // De dónde sale el agua: define si se puede regar cualquier día o solo en el turno
    public const FUENTES_AGUA = [
        'red'   => ['nombre' => 'Agua de red', 'detalle' => 'Canilla o agua corriente: se puede regar cualquier día.', 'icono' => 'bi-droplet'],
        'pozo'  => ['nombre' => 'Pozo o bomba', 'detalle' => 'Perforación propia: se puede regar cualquier día.', 'icono' => 'bi-moisture'],
        'turno' => ['nombre' => 'Turno de canal', 'detalle' => 'El agua llega solo los días de turno; entre turnos se riega con agua guardada.', 'icono' => 'bi-water'],
    ];

    // Eficiencia: parte del agua aplicada que realmente aprovecha la planta
    public const METODOS_RIEGO = [
        'goteo'     => ['nombre' => 'Goteo', 'eficiencia' => 0.9],
        'manguera'  => ['nombre' => 'Manguera o regadera al pie', 'eficiencia' => 0.8],
        'aspersion' => ['nombre' => 'Aspersión', 'eficiencia' => 0.7],
        'surco'     => ['nombre' => 'Surco', 'eficiencia' => 0.6],
        'manto'     => ['nombre' => 'Manto o inundación', 'eficiencia' => 0.5],
    ];

    public const DIAS_SEMANA = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

    protected $table      = 'huertos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombre',
        'zona',
        'fuente_agua',
        'metodo_riego',
        'turno_modo',
        'turno_cada_dias',
        'turno_referencia',
        'turno_dias_semana',
        'reservorio_litros',
        'plano_ancho',
        'plano_largo',
        'norte_grados',
        'latitud',
        'longitud',
        'viento_desde',
    ];

    protected $useTimestamps = true;

    private const POR_DEFECTO = [
        'id'                => null,
        'nombre'            => 'Huerto Comunitario',
        'zona'              => null,
        'fuente_agua'       => 'red',
        'metodo_riego'      => 'manguera',
        'turno_modo'        => null,
        'turno_cada_dias'   => null,
        'turno_referencia'  => null,
        'turno_dias_semana' => null,
        'reservorio_litros' => 0,
        'plano_ancho'       => 12,
        'plano_largo'       => 10,
        'norte_grados'      => 0,
        'latitud'           => -31.5375,
        'longitud'          => -68.5364,
        'viento_desde'      => null,
    ];

    // Por ahora el sistema gestiona un solo huerto: devolvemos el primero (o valores por defecto)
    public function actual(): array
    {
        if (! $this->db->tableExists($this->table)) {
            return self::POR_DEFECTO;
        }

        return array_merge(self::POR_DEFECTO, $this->orderBy('id')->first() ?? []);
    }
}
