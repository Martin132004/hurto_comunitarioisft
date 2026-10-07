<?php

namespace App\Controllers;

use App\Libraries\PlanRiego;
use App\Models\CosechaModel;
use App\Models\CultivoModel;
use App\Models\EspecieModel;
use App\Models\HuertoModel;
use App\Models\PlanoElementoModel;
use App\Models\ReporteProblemaModel;
use App\Models\SiembraPlanificadaModel;
use App\Models\ZonaModel;

/**
 * Plano 3D del huerto: diseño de canteros y elementos, cultivos ubicados en cada cantero,
 * planificación de siembras y análisis de sol, viento y agua.
 */
class Plano extends BaseController
{
    public function index()
    {
        if (! db_connect()->tableExists('plano_elementos')) {
            return redirect()->to('/')->with('error', 'Falta correr las migraciones: php spark migrate');
        }

        $huerto = (new HuertoModel())->actual();
        $zona = db_connect()->tableExists('zonas') ? (new ZonaModel())->porClave($huerto['zona']) : null;
        $plan = new PlanRiego($huerto, $zona);

        return view('plano/index', [
            'huerto'       => $huerto,
            'zona'         => $zona,
            'tipos'        => PlanoElementoModel::TIPOS,
            'elementos'    => (new PlanoElementoModel())->delHuerto($huerto['id']),
            'cultivos'     => $this->cultivos($huerto),
            'planificadas' => (new SiembraPlanificadaModel())->pendientes($huerto['id']),
            'especies'     => db_connect()->tableExists('especies') ? (new EspecieModel())->catalogo() : [],
            'riego'        => [
                'metodo'       => $plan->metodo()['nombre'],
                'eficiencia'   => $plan->metodo()['eficiencia'],
                'coeficientes' => array_map(fn ($mes) => $plan->coeficiente(new \DateTime(date('Y') . "-$mes-15")), range(1, 12)),
                'reservorio'   => (int) $huerto['reservorio_litros'],
                'usa_turno'    => $plan->usaTurno(),
                'entre_turnos' => $plan->diasEntreTurnos(),
            ],
        ]);
    }

    // guardar(): recibe el plano completo (terreno, elementos y cultivos ubicados) y lo reemplaza
    public function guardar()
    {
        $datos = $this->datosJson();
        if ($datos === null) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'Los datos enviados no son válidos.']);
        }
        $huertoModel = new HuertoModel();
        $huerto = $huertoModel->actual();

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
            $tipo = (string) ($elemento['tipo'] ?? '');
            if (! isset(PlanoElementoModel::TIPOS[$tipo])) {
                continue;
            }

            $fila = [
                'huerto_id'    => $huerto['id'],
                'tipo'         => $tipo,
                'nombre'       => mb_substr(trim((string) ($elemento['nombre'] ?? '')), 0, 60) ?: PlanoElementoModel::TIPOS[$tipo]['nombre'],
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
            $ids[(string) ($elemento['clave'] ?? $id)] = (int) $id;
        }

        // Los elementos que ya no están en el plano se borran con sus siembras planificadas
        if ($existentes) {
            $borrados = array_keys($existentes);
            $elementos->delete($borrados);
            (new CultivoModel())->whereIn('cantero_id', $borrados)->set('cantero_id', null)->update();
            (new SiembraPlanificadaModel())->whereIn('cantero_id', $borrados)->where('cultivo_id', null)->delete();
        }

        // Cultivos ubicados: {cultivo_id: clave del cantero o null}
        $plantables = (new PlanoElementoModel())->plantables($huerto['id']);
        $cultivos = new CultivoModel();
        foreach ((array) ($datos['ubicaciones'] ?? []) as $cultivoId => $clave) {
            $canteroId = $clave === null ? null : ($ids[(string) $clave] ?? null);
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

    // planificar(): crea o modifica una siembra planificada en un cantero
    public function planificar()
    {
        $datos = $this->datosJson() ?? [];
        $huerto = (new HuertoModel())->actual();
        $plantables = (new PlanoElementoModel())->plantables($huerto['id']);

        $canteroId = (int) ($datos['cantero_id'] ?? 0);
        $especie = mb_substr(trim((string) ($datos['especie'] ?? '')), 0, 60);
        $fecha = \DateTime::createFromFormat('!Y-m-d', (string) ($datos['fecha'] ?? ''));
        $dias = (int) ($datos['dias_cosecha'] ?? 0);
        $plantas = (int) ($datos['plantas'] ?? 0);

        $error = match (true) {
            ! isset($plantables[$canteroId]) => 'Elegí un cantero guardado del plano.',
            $especie === ''                  => 'Escribí qué vas a sembrar.',
            ! $fecha                         => 'Revisá la fecha de siembra.',
            $dias < 1 || $dias > 400         => 'Los días hasta la cosecha tienen que ser entre 1 y 400.',
            $plantas < 0 || $plantas > 5000  => 'Revisá la cantidad de plantas.',
            default                          => null,
        };
        if ($error) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => $error]);
        }

        $siembras = new SiembraPlanificadaModel();
        $fila = [
            'huerto_id'    => $huerto['id'],
            'cantero_id'   => $canteroId,
            'especie'      => $especie,
            'variedad'     => mb_substr(trim((string) ($datos['variedad'] ?? '')), 0, 60) ?: null,
            'fecha'        => $fecha->format('Y-m-d'),
            'dias_cosecha' => $dias,
            'plantas'      => $plantas ?: null,
            'notas'        => mb_substr(trim((string) ($datos['notas'] ?? '')), 0, 255) ?: null,
        ];

        $id = (int) ($datos['id'] ?? 0);
        $actual = $id ? $siembras->find($id) : null;
        if ($actual && (int) $actual['huerto_id'] === (int) $huerto['id'] && $actual['cultivo_id'] === null) {
            $siembras->update($id, $fila);
        } else {
            $id = $siembras->insert($fila);
        }

        return $this->response->setJSON(['ok' => true, 'plan' => $siembras->find($id)]);
    }

    public function eliminarPlan($id)
    {
        $huerto = (new HuertoModel())->actual();
        $siembras = new SiembraPlanificadaModel();
        $plan = $siembras->find($id);

        if ($plan && (int) $plan['huerto_id'] === (int) $huerto['id'] && $plan['cultivo_id'] === null) {
            $siembras->delete($id);
        }

        return $this->response->setJSON(['ok' => true]);
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

    // Cultivos en curso y los ya cosechados que estuvieron en un cantero (sirven para la rotación)
    private function cultivos(array $huerto): array
    {
        $cultivos = (new CultivoModel())
            ->groupStart()->where('estado !=', 'Cosechado')->orWhere('cantero_id IS NOT NULL')->groupEnd()
            ->orderBy('fecha_siembra')
            ->findAll();

        $problemas = db_connect()->tableExists('reportes_problemas') ? (new ReporteProblemaModel())->abiertosPorCultivo($huerto['id']) : [];
        $kg = db_connect()->tableExists('cosechas') ? (new CosechaModel())->kgPorCultivo() : [];
        $hoy = new \DateTime('today');

        return array_map(function (array $c) use ($hoy, $problemas, $kg) {
            $siembra = new \DateTime($c['fecha_siembra']);
            $cosecha = (clone $siembra)->modify('+' . (int) $c['dias_cosecha_estimados'] . ' days');
            $ultimoRiego = new \DateTime($c['ultimo_riego'] ? substr($c['ultimo_riego'], 0, 10) : $c['fecha_siembra']);
            $proximoRiego = $ultimoRiego->modify('+' . max(1, (int) $c['frecuencia_riego_dias']) . ' days');

            return [
                'id'           => (int) $c['id'],
                'cantero_id'   => $c['cantero_id'] === null ? null : (int) $c['cantero_id'],
                'nombre'       => $c['nombre_planta'],
                'variedad'     => $c['variedad'],
                'fecha'        => $c['fecha_siembra'],
                'dias_cosecha' => (int) $c['dias_cosecha_estimados'],
                'frecuencia'   => (int) $c['frecuencia_riego_dias'],
                'litros'       => (float) $c['cantidad_riego_litros'],
                'cosechado'    => $c['estado'] === 'Cosechado',
                'regar'        => $c['estado'] !== 'Cosechado' && $hoy >= $proximoRiego,
                'cosechar'     => $c['estado'] !== 'Cosechado' && $hoy >= $cosecha,
                'problemas'    => $problemas[$c['id']] ?? 0,
                'kg'           => isset($kg[$c['id']]) ? (float) $kg[$c['id']] : null,
            ];
        }, $cultivos);
    }
}
