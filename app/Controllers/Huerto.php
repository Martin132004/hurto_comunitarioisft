<?php

namespace App\Controllers;

use App\Libraries\PlanRiego;
use App\Models\CosechaModel;
use App\Models\CultivoModel;
use App\Models\EspecieModel;
use App\Models\HuertoModel;
use App\Models\PlanoElementoModel;
use App\Models\ProblemaModel;
use App\Models\ReporteProblemaModel;
use App\Models\RiegoModel;
use App\Models\SiembraPlanificadaModel;
use App\Models\ZonaModel;

class Huerto extends BaseController
{
    public function index()
    {
        $model = new CultivoModel();
        $cultivos = $model->findAll();
        $plan = $this->planRiego();

        $hoy = new \DateTime();

        foreach ($cultivos as &$planta) {

            $fechaSiembra = new \DateTime($planta['fecha_siembra']);
            $fechaCosecha = clone $fechaSiembra;
            $fechaCosecha->modify('+' . $planta['dias_cosecha_estimados'] . ' days');

            $planta['alerta_cosecha'] = ($hoy >= $fechaCosecha);

            $ultimoRiego = $planta['ultimo_riego'] ? new \DateTime($planta['ultimo_riego']) : clone $fechaSiembra;
            $proximoRiego = clone $ultimoRiego;
            $proximoRiego->modify('+' . $planta['frecuencia_riego_dias'] . ' days');

            $planta['alerta_riego'] = ($hoy >= $proximoRiego);

            // Datos para la tarjeta: avance del ciclo y días hasta el próximo riego
            $diasCiclo = max(1, (int) $planta['dias_cosecha_estimados']);
            $diasDesdeSiembra = $fechaSiembra <= $hoy ? (int) $fechaSiembra->diff($hoy)->days : 0;
            $planta['dias_desde_siembra'] = $diasDesdeSiembra;
            $planta['progreso'] = min(100, (int) round($diasDesdeSiembra * 100 / $diasCiclo));
            $planta['fecha_cosecha'] = $fechaCosecha;
            $planta['dias_proximo_riego'] = $planta['alerta_riego'] ? 0 : (int) (clone $hoy)->setTime(0, 0)->diff((clone $proximoRiego)->setTime(0, 0))->days;

            // Litros ajustados al mes y al método de riego del huerto
            $planta['litros_hoy'] = $plan->litrosAplicar((float) $planta['cantidad_riego_litros'], $hoy);
        }

        $datos['cultivos'] = $cultivos;
        $datos['zona'] = $this->zonaDelHuerto();
        $datos['huerto'] = $this->huerto();
        $datos['riego'] = $plan->resumen($this->cultivosActivos(), $hoy);
        $datos['hoyHayTurno'] = $plan->esDiaDeTurno($hoy);
        $datos['problemas'] = $this->resumenProblemas();
        $datos['kgCosechados'] = db_connect()->tableExists('cosechas') ? (new CosechaModel())->kgPorCultivo() : [];

        return view('huerto/index', $datos);
    }

    public function crear()
    {
        if ($this->request->getMethod() === 'GET') {
            // Si todavía no se corrió la migración de especies, el formulario funciona sin guía
            $especies = db_connect()->tableExists('especies') ? (new EspecieModel())->catalogo() : [];

            // Cultivos en curso: la guía los usa para avisar vecinos, familias repetidas y consumo de agua
            $activos = $this->cultivosActivos();

            $plan = $this->planRiego();
            $hoy = new \DateTime();

            // Siembra planificada en el plano 3D que se está por hacer: precarga el formulario
            $siembra = null;
            if ($this->request->getGet('plan') && db_connect()->tableExists('siembras_planificadas')) {
                $siembra = (new SiembraPlanificadaModel())->where('cultivo_id', null)->find((int) $this->request->getGet('plan'));
            }

            return view('huerto/crear', [
                'canteros' => $this->canteros(),
                'plan'     => $siembra,
                'horarios' => CultivoModel::HORARIOS_RIEGO,
                'especies' => $especies,
                'activos'  => $activos,
                'zona'     => $this->zonaDelHuerto(),
                // Sistema de riego del huerto: la guía ajusta los litros y avisa sobre el turno de agua
                'riego'    => [
                    'coeficiente'       => $plan->coeficiente($hoy),
                    'eficiencia'        => $plan->metodo()['eficiencia'],
                    'metodo'            => $plan->metodo()['nombre'],
                    'usa_turno'         => $plan->usaTurno(),
                    'dias_entre_turnos' => $plan->diasEntreTurnos(),
                    'agua_entre_turnos' => $plan->aguaEntreTurnos($activos, $hoy),
                    'reservorio'        => (int) $this->huerto()['reservorio_litros'],
                ],
            ]);
        }

        $model = new CultivoModel();

        $datos = [
            'nombre_planta'          => $this->request->getPost('nombre_planta'),
            'variedad'               => $this->request->getPost('variedad'),
            'fecha_siembra'          => $this->request->getPost('fecha_siembra'),
            'dias_cosecha_estimados' => $this->request->getPost('dias_cosecha_estimados'),
            'frecuencia_riego_dias'  => $this->request->getPost('frecuencia_riego_dias'),
            'horario_riego'          => $this->request->getPost('horario_riego'),
            'cantidad_riego_litros'  => $this->request->getPost('cantidad_riego_litros'),
            'estado'                 => 'En Crecimiento',
            'ultimo_riego'           => null
        ];

        $canteroId = (int) $this->request->getPost('cantero_id');
        if (isset($this->canteros()[$canteroId])) {
            $datos['cantero_id'] = $canteroId;
        }

        $model->save($datos);

        // Si venía de una siembra planificada, queda marcada como hecha
        $planId = (int) $this->request->getPost('plan_id');
        if ($planId && db_connect()->tableExists('siembras_planificadas')) {
            (new SiembraPlanificadaModel())->where('id', $planId)->where('cultivo_id', null)
                ->set('cultivo_id', $model->getInsertID())->update();

            return redirect()->to('huerto/plano');
        }

        return redirect()->to('/');
    }

    // configuracion(): Datos del huerto: zona y sistema de riego que usa
    public function configuracion()
    {
        $huertoModel = new HuertoModel();
        $huerto = $huertoModel->actual();
        $zonas = db_connect()->tableExists('zonas') ? (new ZonaModel())->orderBy('id')->findAll() : [];

        if ($this->request->getMethod() === 'GET') {
            return view('huerto/configuracion', [
                'huerto'  => $huerto,
                'zonas'   => $zonas,
                'fuentes' => HuertoModel::FUENTES_AGUA,
                'metodos' => HuertoModel::METODOS_RIEGO,
                'dias'    => HuertoModel::DIAS_SEMANA,
                'resumen' => $this->planRiego()->resumen($this->cultivosActivos(), new \DateTime()),
            ]);
        }

        $post = fn ($campo) => $this->request->getPost($campo);
        $fuente = array_key_exists((string) $post('fuente_agua'), HuertoModel::FUENTES_AGUA) ? $post('fuente_agua') : 'red';
        $modo = $post('turno_modo') === 'semana' ? 'semana' : 'intervalo';
        $referencia = \DateTime::createFromFormat('Y-m-d', (string) $post('turno_referencia'));
        $diasSemana = array_intersect(array_map('intval', (array) $post('turno_dias_semana')), array_keys(HuertoModel::DIAS_SEMANA));

        $datos = [
            'nombre'            => trim((string) $post('nombre')) ?: 'Huerto Comunitario',
            'zona'              => in_array($post('zona'), array_column($zonas, 'clave'), true) ? $post('zona') : null,
            'fuente_agua'       => $fuente,
            'metodo_riego'      => array_key_exists((string) $post('metodo_riego'), HuertoModel::METODOS_RIEGO) ? $post('metodo_riego') : 'manguera',
            'reservorio_litros' => max(0, (int) $post('reservorio_litros')),
            // Los datos del turno solo se guardan si el agua llega por turno de canal
            'turno_modo'        => $fuente === 'turno' ? $modo : null,
            'turno_cada_dias'   => $fuente === 'turno' && $modo === 'intervalo' ? min(60, max(1, (int) $post('turno_cada_dias'))) : null,
            'turno_referencia'  => $fuente === 'turno' && $modo === 'intervalo' && $referencia ? $referencia->format('Y-m-d') : null,
            'turno_dias_semana' => $fuente === 'turno' && $modo === 'semana' ? implode(',', $diasSemana) : null,
        ];

        if ($huerto['id']) {
            $huertoModel->update($huerto['id'], $datos);
        } else {
            $huertoModel->insert($datos);
        }

        return redirect()->to('huerto/configuracion')->with('mensaje', 'Datos del huerto guardados.');
    }

    public function registrarRiego($id)
    {
        $model = new CultivoModel();
        $planta = $model->find($id);

        if (! $planta) {
            return redirect()->to('/');
        }

        $ahora = new \DateTime();
        $model->update($id, ['ultimo_riego' => $ahora->format('Y-m-d H:i:s')]);

        // Se guarda en el historial con los litros que corresponden a este mes y al método de riego
        if (db_connect()->tableExists('riegos')) {
            (new RiegoModel())->insert([
                'cultivo_id' => $id,
                'fecha'      => $ahora->format('Y-m-d H:i:s'),
                'litros'     => $this->planRiego()->litrosAplicar((float) $planta['cantidad_riego_litros'], $ahora),
            ]);
        }

        // Redirigimos a la pantalla de riego para que recargarla no registre otro riego
        return redirect()->to('huerto/regando/' . $id);
    }

    public function regando($id)
    {
        $model = new CultivoModel();
        $planta = $model->find($id);

        if (! $planta) {
            return redirect()->to('/');
        }

        $plan = $this->planRiego();
        $litrosPlanta = (float) $planta['cantidad_riego_litros'];

        $frecuencia = max(1, (int) $planta['frecuencia_riego_dias']);
        $ultimoRiego = new \DateTime($planta['ultimo_riego'] ?? 'now');

        $proximoRiego = clone $ultimoRiego;
        $proximoRiego->modify('+' . $frecuencia . ' days');

        $fechaCosecha = new \DateTime($planta['fecha_siembra']);
        $fechaCosecha->modify('+' . $planta['dias_cosecha_estimados'] . ' days');

        // Próximos riegos programados hasta la cosecha (máximo 5 para mostrar), con los litros de ese mes
        $calendario = [];
        $fecha = clone $proximoRiego;
        while ($fecha <= $fechaCosecha && count($calendario) < 5) {
            $calendario[] = [
                'fecha'  => clone $fecha,
                'litros' => $plan->litrosAplicar($litrosPlanta, $fecha),
                'turno'  => $plan->esDiaDeTurno($fecha),
            ];
            $fecha->modify('+' . $frecuencia . ' days');
        }

        $riegosRestantes = 0;
        if ($proximoRiego <= $fechaCosecha) {
            $diasHastaCosecha = (int) $proximoRiego->diff($fechaCosecha)->days;
            $riegosRestantes = intdiv($diasHastaCosecha, $frecuencia) + 1;
        }

        $horarios = CultivoModel::HORARIOS_RIEGO;

        $datos = [
            'planta'            => $planta,
            'frecuencia'        => $frecuencia,
            'proximoRiego'      => $proximoRiego,
            'calendario'        => $calendario,
            'riegosRestantes'   => $riegosRestantes,
            'horario'           => $horarios[$planta['horario_riego']] ?? $horarios['manana'],
            'cantidad'          => $plan->litrosAplicar($litrosPlanta, $ultimoRiego),
            'litrosPlanta'      => $litrosPlanta,
            'metodo'            => $plan->metodo(),
            'usaTurno'          => $plan->usaTurno(),
            'proximosTurnos'    => $plan->proximosTurnos(new \DateTime()),
            'riegosEntreTurnos' => $plan->riegosEntreTurnos($frecuencia),
        ];

        return view('huerto/regando', $datos);
    }

    // cosechar(): registra los kg cosechados; si es la última cosecha, el cultivo pasa a Cosechado
    public function cosechar($id)
    {
        $model = new CultivoModel();
        $planta = $model->find($id);

        if (! $planta || $planta['estado'] === 'Cosechado') {
            return redirect()->to('/');
        }

        $cosechas = new CosechaModel();

        if ($this->request->getMethod() === 'GET') {
            return view('huerto/cosechar', [
                'planta'     => $planta,
                'anteriores' => $cosechas->where('cultivo_id', $id)->orderBy('fecha')->findAll(),
            ]);
        }

        $fecha = \DateTime::createFromFormat('Y-m-d', (string) $this->request->getPost('fecha'));
        $kg = str_replace(',', '.', trim((string) $this->request->getPost('kg')));
        $final = $this->request->getPost('final') === '1';

        if (! $fecha || $fecha > new \DateTime() || $fecha->format('Y-m-d') < $planta['fecha_siembra']) {
            return redirect()->back()->withInput()->with('error', 'Revisá la fecha: tiene que ser entre la siembra y hoy.');
        }
        if ($kg !== '' && (! is_numeric($kg) || $kg < 0 || $kg > 100000)) {
            return redirect()->back()->withInput()->with('error', 'Los kg tienen que ser un número, por ejemplo 2,5.');
        }

        $cosechas->insert([
            'cultivo_id'    => $id,
            'fecha'         => $fecha->format('Y-m-d'),
            'kg'            => $kg === '' ? null : round((float) $kg, 2),
            'final'         => $final ? 1 : 0,
            'observaciones' => mb_substr(trim((string) $this->request->getPost('observaciones')), 0, 255) ?: null,
        ]);

        if ($final) {
            $model->update($id, ['estado' => 'Cosechado']);
        }

        return redirect()->to('/');
    }

    public function eliminar($id)
    {
        $model = new CultivoModel();
        $model->delete($id);
        return redirect()->to('/');
    }

    // Canteros del plano 3D donde se puede ubicar un cultivo: [id => nombre]
    private function canteros(): array
    {
        return db_connect()->tableExists('plano_elementos') ? (new PlanoElementoModel())->plantables($this->huerto()['id']) : [];
    }

    private function huerto(): array
    {
        return (new HuertoModel())->actual();
    }

    // Zona configurada para el huerto, o null si todavía no se eligió
    private function zonaDelHuerto(): ?array
    {
        if (! db_connect()->tableExists('zonas')) {
            return null;
        }

        return (new ZonaModel())->porClave($this->huerto()['zona']);
    }

    private function planRiego(): PlanRiego
    {
        return new PlanRiego($this->huerto(), $this->zonaDelHuerto());
    }

    // Reportes sin resolver y alertas de la zona para mostrar en el panel
    private function resumenProblemas(): ?array
    {
        if (! db_connect()->tableExists('reportes_problemas')) {
            return null;
        }

        $huerto = $this->huerto();
        $reportes = new ReporteProblemaModel();
        $nombres = array_column((new ProblemaModel())->catalogo(), 'nombre', 'clave');
        $nombrar = fn (array $claves) => array_map(fn ($clave) => $nombres[$clave] ?? $clave, $claves);

        return [
            'sin_resolver' => $reportes->where('huerto_id', $huerto['id'])->where('estado !=', 'resuelto')->countAllResults(),
            'respondidos'  => $reportes->where('huerto_id', $huerto['id'])->where('estado', 'respondido')->countAllResults(),
            'brotes'       => $nombrar(array_keys($reportes->brotesEnZona($huerto['zona']))),
            'repetidos'    => $nombrar(array_keys($reportes->repetidosEnHuerto($huerto['id']))),
            'por_cultivo'  => $reportes->abiertosPorCultivo($huerto['id']),
        ];
    }

    private function cultivosActivos(): array
    {
        return (new CultivoModel())
            ->select('nombre_planta, fecha_siembra, frecuencia_riego_dias, cantidad_riego_litros')
            ->where('estado !=', 'Cosechado')
            ->findAll();
    }
}
