<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanoElementoModel extends Model
{
    /**
     * Tipos de elementos del plano.
     * - medidas: ancho y largo en metros con que se agrega uno nuevo.
     * - plantable: se le pueden ubicar cultivos (canteros, tablones, macetas y túneles).
     * - protege_helada: cubierta que baja el riesgo de helada (cambia el aviso del cantero).
     */
    public const TIPOS = [
        'cantero'    => ['nombre' => 'Cantero elevado', 'icono' => 'bi-bounding-box', 'medidas' => [1.2, 3], 'plantable' => true, 'protege_helada' => false],
        'tablon'     => ['nombre' => 'Tablón en el suelo', 'icono' => 'bi-distribute-vertical', 'medidas' => [1, 4], 'plantable' => true, 'protege_helada' => false],
        'maceta'     => ['nombre' => 'Maceta o cajón', 'icono' => 'bi-cup', 'medidas' => [0.5, 0.5], 'plantable' => true, 'protege_helada' => false],
        'tunel'      => ['nombre' => 'Túnel o invernadero', 'icono' => 'bi-house', 'medidas' => [2, 5], 'plantable' => true, 'protege_helada' => true],
        'arbol'      => ['nombre' => 'Árbol', 'icono' => 'bi-tree', 'medidas' => [3, 3], 'plantable' => false, 'protege_helada' => false],
        'cortina'    => ['nombre' => 'Cortina rompeviento', 'icono' => 'bi-wind', 'medidas' => [0.6, 8], 'plantable' => false, 'protege_helada' => false],
        'pared'      => ['nombre' => 'Muro o galpón', 'icono' => 'bi-bricks', 'medidas' => [0.3, 5], 'plantable' => false, 'protege_helada' => false],
        'tanque'     => ['nombre' => 'Tanque o reservorio', 'icono' => 'bi-database', 'medidas' => [1.2, 1.2], 'plantable' => false, 'protege_helada' => false],
        'compostera' => ['nombre' => 'Compostera', 'icono' => 'bi-recycle', 'medidas' => [1, 1], 'plantable' => false, 'protege_helada' => false],
        'acequia'    => ['nombre' => 'Acequia o canal', 'icono' => 'bi-water', 'medidas' => [0.6, 10], 'plantable' => false, 'protege_helada' => false],
        'camino'     => ['nombre' => 'Camino', 'icono' => 'bi-signpost-split', 'medidas' => [0.8, 6], 'plantable' => false, 'protege_helada' => false],
    ];

    protected $table      = 'plano_elementos';
    protected $primaryKey = 'id';

    // x e y: centro del elemento en metros desde la esquina de arriba a la izquierda del terreno
    protected $allowedFields = [
        'huerto_id',
        'tipo',
        'nombre',
        'x',
        'y',
        'ancho',
        'largo',
        'rotacion',
        'media_sombra',
    ];

    protected $useTimestamps = true;

    public function delHuerto(?int $huertoId): array
    {
        return $this->where('huerto_id', $huertoId)->orderBy('id')->findAll();
    }

    // Canteros donde se puede plantar: [id => nombre]
    public function plantables(?int $huertoId): array
    {
        $tipos = array_keys(array_filter(self::TIPOS, fn ($tipo) => $tipo['plantable']));
        $filas = $this->where('huerto_id', $huertoId)->whereIn('tipo', $tipos)->orderBy('nombre')->findAll();

        return array_column($filas, 'nombre', 'id');
    }
}
