<?php

namespace App\Controllers;

use App\Models\CultivoModel;
use App\Models\EspecieModel;
use App\Models\HuertoModel;
use App\Models\PlanoElementoModel;
use App\Models\ReporteProblemaModel;

/**
 * Plano del huerto (2D): diseño de canteros y elementos, y en qué cantero está cada cultivo.
 */
class Plano extends BaseController
{
    public function index()
    {
        $huerto = $this->huerto();
        $plan = $this->planRiego();

        return view('plano/index', [
            'huerto'    => $huerto,
            'zona'      => $this->zona(),
            'tipos'     => PlanoElementoModel::TIPOS,
            'elementos' => (new PlanoElementoModel())->delHuerto($huerto['id']),
            'cultivos'  => $this->cultivos($huerto),
            'especies'  => (new EspecieModel())->catalogo(),
            // Para los litros de la pestaña Hoy: ajuste de cada mes y eficiencia del método de riego
            'riego'     => [
                'metodo'       => $plan->metodo()['nombre'],
                'eficiencia'   => $plan->metodo()['eficiencia'],
                'coeficientes' => array_map(fn ($mes) => $plan->coeficiente(new \DateTime(date('Y') . "-$mes-15")), range(1, 12)),
            ],
        ]);
    }

    // guardar(): recibe el plano completo (terreno, elementos y cultivos ubicados) y lo reemplaza
    public function guardar()
    {
        $datos = $this->datosJson();
        // Sin terreno y elementos como listas no se toca nada: tomarlos como vacíos borraría los canteros
        if ($datos === null || ! is_array($datos['terreno'] ?? null) || ! is_array($datos['elementos'] ?? null)) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'Los datos enviados no son válidos.']);
        }
        $huertoModel = new HuertoModel();
        $huerto = $this->huerto();

        $numero = fn ($valor, float $min, float $max) => round(min($max, max($min, (float) $valor)), 2);

        $terreno = $datos['terreno'] ?? [];
        $ancho = $numero($terreno['ancho'] ?? 12, 2, 200);
        $largo = $numero($terreno['largo'] ?? 10, 2, 200);
        $camposTerreno = [
            'plano_ancho'  => $ancho,
            'plano_largo'  => $largo,
            'norte_grados' => ((int) ($terreno['norte'] ?? 0) % 360 + 360) % 360,
            'latitud'      => $numero($terreno['latitud'] ?? $huerto['latitud'], -60, 60),
            'longitud'     => $numero($terreno['longitud'] ?? $huerto['longitud'], -180, 180),
            'viento_desde' => isset($terreno['viento']) && $terreno['viento'] !== '' && $terreno['viento'] !== null
                ? ((int) $terreno['viento'] % 360 + 360) % 360
                : null,
        ];

        if ($huerto['id']) {
            $huertoModel->update($huerto['id'], $camposTerreno);
        } else {
            $huerto['id'] = $huertoModel->insert(array_merge(['nombre' => $huerto['nombre']], $camposTerreno));
        }

        $elementos = new PlanoElementoModel();
        $existentes = array_column($elementos->delHuerto($huerto['id']), null, 'id');
        $ids = [];

        foreach (array_slice((array) ($datos['elementos'] ?? []), 0, 300) as $elemento) {
            $tipo = is_array($elemento) ? $this->texto($elemento['tipo'] ?? '') : '';
            if (! isset(PlanoElementoModel::TIPOS[$tipo])) {
                continue;
            }

            $fila = [
                'huerto_id'    => $huerto['id'],
                'tipo'         => $tipo,
                'nombre'       => mb_substr(trim($this->texto($elemento['nombre'] ?? '')), 0, 60) ?: PlanoElementoModel::TIPOS[$tipo]['nombre'],
                'x'            => $numero($elemento['x'] ?? 0, 0, $ancho),
                'z'            => $numero($elemento['z'] ?? 0, 0, $largo),
                'ancho'        => $numero($elemento['ancho'] ?? 1, 0.2, 50),
                'largo'        => $numero($elemento['largo'] ?? 1, 0.2, 100),
                'alto'         => $numero($elemento['alto'] ?? 0.3, 0.02, 30),
                'rotacion'     => ((int) ($elemento['rotacion'] ?? 0) % 360 + 360) % 360,
                'media_sombra' => empty($elemento['media_sombra']) ? 0 : 1,
            ];

            $id = (int) ($elemento['id'] ?? 0);
            if ($id && isset($existentes[$id])) {
                $elementos->update($id, $fila);
                unset($existentes[$id]);
            } else {
                $id = $elementos->insert($fila);
            }

            // La clave temporal del navegador queda asociada al id guardado
            $ids[$this->texto($elemento['clave'] ?? $id)] = (int) $id;
        }

        // Los elementos que ya no están en el plano se borran y sus cultivos quedan sin ubicar
        if ($existentes) {
            $borrados = array_keys($existentes);
            $elementos->delete($borrados);
            (new CultivoModel())->whereIn('cantero_id', $borrados)->set('cantero_id', null)->update();
        }

        // Cultivos ubicados: {cultivo_id: clave del cantero o null}
        $plantables = (new PlanoElementoModel())->plantables($huerto['id']);
        $cultivos = new CultivoModel();
        foreach ((array) ($datos['ubicaciones'] ?? []) as $cultivoId => $clave) {
            $canteroId = $clave === null ? null : ($ids[$this->texto($clave)] ?? null);
            if ($cultivos->find((int) $cultivoId)) {
                $cultivos->update((int) $cultivoId, ['cantero_id' => isset($plantables[$canteroId]) ? $canteroId : null]);
            }
        }

        return $this->response->setJSON([
            'ok'        => true,
            'ids'       => $ids,
            'elementos' => (new PlanoElementoModel())->delHuerto($huerto['id']),
        ]);
    }

    // Cuerpo JSON del pedido, o null si no se puede leer
    private function datosJson(): ?array
    {
        try {
            $datos = $this->request->getJSON(true);
        } catch (\Throwable) {
            return null;
        }

        return is_array($datos) ? $datos : null;
    }

    // Texto de un valor del JSON; una lista u objeto da '' en lugar de un error 500
    private function texto($valor): string
    {
        return is_scalar($valor) ? (string) $valor : '';
    }

    // Cultivos en curso y los ya cosechados que estuvieron en un cantero (sirven para la rotación)
    private function cultivos(array $huerto): array
    {
        $cultivos = (new CultivoModel())
            ->groupStart()->where('estado !=', CultivoModel::COSECHADO)->orWhere('cantero_id IS NOT NULL')->groupEnd()
            ->orderBy('fecha_siembra')
            ->findAll();

        $problemas = (new ReporteProblemaModel())->abiertosPorCultivo($huerto['id']);
        $hoy = new \DateTime('today');

        return array_map(function (array $c) use ($hoy, $problemas) {
            $cosechado = $c['estado'] === CultivoModel::COSECHADO;

            return [
                'id'           => (int) $c['id'],
                'cantero_id'   => $c['cantero_id'] === null ? null : (int) $c['cantero_id'],
                'nombre'       => $c['nombre_planta'],
                'variedad'     => $c['variedad'],
                'fecha'        => $c['fecha_siembra'],
                'dias_cosecha' => (int) $c['dias_cosecha_estimados'],
                'litros'       => (float) $c['cantidad_riego_litros'],
                'cosechado'    => $cosechado,
                'regar'        => ! $cosechado && $hoy >= CultivoModel::proximoRiego($c),
                'cosechar'     => ! $cosechado && $hoy >= CultivoModel::fechaCosecha($c),
                'problemas'    => $problemas[$c['id']] ?? 0,
            ];
        }, $cultivos);
    }
}
