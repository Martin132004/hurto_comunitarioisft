<?php

namespace App\Controllers;

use App\Models\CosechaModel;
use App\Models\CultivoModel;
use App\Models\EspecieModel;
use App\Models\HuertoModel;
use App\Models\PlanoElementoModel;
use App\Models\ProblemaModel;
use App\Models\ReporteProblemaModel;
use App\Models\RiegoModel;
use App\Models\ZonaModel;

class Huerto extends BaseController
{
    public function index()
    {
        $plan = $this->planRiego();
        $hoy = new \DateTime('today');
        $cultivos = (new CultivoModel())->findAll();

        // Datos de cada tarjeta: alertas, avance del ciclo, próximo riego y litros de este mes
        foreach ($cultivos as &$planta) {
            $siembra = new \DateTime($planta['fecha_siembra']);
            $cosecha = CultivoModel::fechaCosecha($planta);
            $proximoRiego = CultivoModel::proximoRiego($planta);
            $diasDesdeSiembra = $siembra <= $hoy ? $siembra->diff($hoy)->days : 0;

            $planta += [
                'alerta_cosecha'     => $hoy >= $cosecha,
                'alerta_riego'       => $hoy >= $proximoRiego,
                'dias_desde_siembra' => $diasDesdeSiembra,
                'progreso'           => min(100, (int) round($diasDesdeSiembra * 100 / max(1, (int) $planta['dias_cosecha_estimados']))),
                'fecha_cosecha'      => $cosecha,
                'dias_proximo_riego' => $hoy >= $proximoRiego ? 0 : $hoy->diff($proximoRiego)->days,
                'litros_hoy'         => $plan->litrosAplicar((float) $planta['cantidad_riego_litros'], $hoy),
            ];
        }
        unset($planta);

        return view('huerto/index', [
            'cultivos'     => $cultivos,
            'zona'         => $this->zona(),
            'huerto'       => $this->huerto(),
            'riego'        => $plan->resumen($this->cultivosActivos(), $hoy),
            'plan'         => $plan,
            'hoyHayTurno'  => $plan->esDiaDeTurno($hoy),
            'problemas'    => $this->resumenProblemas(),
            'kgCosechados' => db_connect()->tableExists('cosechas') ? (new CosechaModel())->kgPorCultivo() : [],
        ]);
    }

    public function crear()
    {
        if ($this->request->getMethod() === 'GET') {
            return $this->formularioCultivo();
        }

        $post = fn ($campo) => trim((string) $this->request->getPost($campo));
        $nombre = $post('nombre_planta');
        $variedad = $post('variedad');
        $litros = str_replace(',', '.', $post('cantidad_riego_litros'));
        // ctype_digit antes del cast: (int) '60abc' daría 60 sin avisar
        $diasCosecha = ctype_digit($post('dias_cosecha_estimados')) ? (int) $post('dias_cosecha_estimados') : 0;
        $frecuencia = ctype_digit($post('frecuencia_riego_dias')) ? (int) $post('frecuencia_riego_dias') : 0;
        $fechaSiembra = (string) $this->request->getPost('fecha_siembra');
        $fecha = \DateTime::createFromFormat('!Y-m-d', $fechaSiembra);

        $error = match (true) {
            $nombre === ''                                          => 'Poné el nombre de la planta.',
            mb_strlen($nombre) > 100 || mb_strlen($variedad) > 100  => 'El nombre y la variedad pueden tener hasta 100 caracteres.',
            ! $fecha || $fecha->format('Y-m-d') !== $fechaSiembra   => 'Revisá la fecha de siembra.',
            $diasCosecha < 1 || $diasCosecha > 400 || $frecuencia < 1 || $frecuencia > 60
                => 'Los días para cosecha van de 1 a 400 y la frecuencia de riego de 1 a 60, en números enteros.',
            ! is_numeric($litros) || $litros <= 0 || $litros > 999  => 'Los litros por riego tienen que ser un número, por ejemplo 1,5.',
            default                                                 => null,
        };
        if ($error) {
            return $this->volverConError($error);
        }

        $horario = (string) $this->request->getPost('horario_riego');
        $datos = [
            'nombre_planta'          => $nombre,
            'variedad'               => $variedad,
            'fecha_siembra'          => $fechaSiembra,
            'dias_cosecha_estimados' => $diasCosecha,
            'frecuencia_riego_dias'  => $frecuencia,
            'horario_riego'          => array_key_exists($horario, CultivoModel::HORARIOS_RIEGO) ? $horario : 'manana',
            'cantidad_riego_litros'  => $litros,
            'estado'                 => 'En Crecimiento',
            'ultimo_riego'           => null,
        ];

        $canteroId = (int) $this->request->getPost('cantero_id');
        if (isset($this->canteros()[$canteroId])) {
            $datos['cantero_id'] = $canteroId;
        }

        (new CultivoModel())->save($datos);

        return redirect()->to('/');
    }

    // configuracion(): Datos del huerto: zona y sistema de riego que usa
    public function configuracion()
    {
        $zonas = db_connect()->tableExists('zonas') ? (new ZonaModel())->orderBy('id')->findAll() : [];

        if ($this->request->getMethod() === 'GET') {
            return view('huerto/configuracion', [
                'huerto'  => $this->huerto(),
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
        if ($referencia && $referencia->format('Y-m-d') !== $post('turno_referencia')) {
            return $this->volverConError('Revisá la fecha de referencia del turno.');
        }
        $metodo = (string) $post('metodo_riego');
        if (! in_array($fuente, HuertoModel::METODOS_RIEGO[$metodo]['fuentes'] ?? [], true)) {
            return $this->volverConError('Ese método de riego no se puede usar con ' . mb_strtolower(HuertoModel::FUENTES_AGUA[$fuente]['nombre']) . '.');
        }
        $diasSemana = array_intersect(array_map('intval', (array) $post('turno_dias_semana')), array_keys(HuertoModel::DIAS_SEMANA));
        $turno = $fuente === 'turno';

        $datos = [
            'nombre'            => trim((string) $post('nombre')) ?: 'Huerto Comunitario',
            'zona'              => in_array($post('zona'), array_column($zonas, 'clave'), true) ? $post('zona') : null,
            'fuente_agua'       => $fuente,
            'metodo_riego'      => $metodo,
            'reservorio_litros' => max(0, (int) $post('reservorio_litros')),
            // Los datos del turno solo se guardan si el agua llega por turno de canal
            'turno_modo'        => $turno ? $modo : null,
            'turno_cada_dias'   => $turno && $modo === 'intervalo' ? min(60, max(1, (int) $post('turno_cada_dias'))) : null,
            'turno_referencia'  => $turno && $modo === 'intervalo' && $referencia ? $referencia->format('Y-m-d') : null,
            'turno_dias_semana' => $turno && $modo === 'semana' ? implode(',', $diasSemana) : null,
        ];

        $huertoModel = new HuertoModel();
        if ($this->huerto()['id']) {
            $huertoModel->update($this->huerto()['id'], $datos);
        } else {
            $huertoModel->insert($datos);
        }

        return redirect()->to('huerto/configuracion')->with('mensaje', 'Datos del huerto guardados.');
    }

    public function registrarRiego($id)
    {
        $model = new CultivoModel();
        $planta = $model->find($id);

        if (! $planta || $planta['estado'] === CultivoModel::COSECHADO) {
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
        $planta = (new CultivoModel())->find($id);
        if (! $planta) {
            return redirect()->to('/');
        }

        $plan = $this->planRiego();
        $litrosPlanta = (float) $planta['cantidad_riego_litros'];
        $frecuencia = max(1, (int) $planta['frecuencia_riego_dias']);
        $ultimoRiego = (new \DateTime($planta['ultimo_riego'] ?? 'now'))->setTime(0, 0);
        $proximoRiego = (clone $ultimoRiego)->modify('+' . $frecuencia . ' days');
        $fechaCosecha = CultivoModel::fechaCosecha($planta);

        // Próximos riegos programados hasta la cosecha (máximo 5 para mostrar), con los litros de ese mes
        $calendario = [];
        for ($fecha = clone $proximoRiego; $fecha <= $fechaCosecha && count($calendario) < 5; $fecha->modify('+' . $frecuencia . ' days')) {
            $calendario[] = [
                'fecha'  => clone $fecha,
                'litros' => $plan->litrosAplicar($litrosPlanta, $fecha),
                'turno'  => $plan->esDiaDeTurno($fecha),
            ];
        }

        return view('huerto/regando', [
            'planta'            => $planta,
            'frecuencia'        => $frecuencia,
            'proximoRiego'      => $proximoRiego,
            'calendario'        => $calendario,
            'riegosRestantes'   => $proximoRiego <= $fechaCosecha ? intdiv($proximoRiego->diff($fechaCosecha)->days, $frecuencia) + 1 : 0,
            'horario'           => CultivoModel::HORARIOS_RIEGO[$planta['horario_riego']] ?? CultivoModel::HORARIOS_RIEGO['manana'],
            'cantidad'          => $plan->litrosAplicar($litrosPlanta, $ultimoRiego),
            'litrosPlanta'      => $litrosPlanta,
            'metodo'            => $plan->metodo(),
            'usaTurno'          => $plan->usaTurno(),
            'proximosTurnos'    => $plan->proximosTurnos(new \DateTime()),
            'riegosEntreTurnos' => $plan->riegosEntreTurnos($frecuencia),
        ]);
    }

    // cosechar(): registra los kg cosechados; si es la última cosecha, el cultivo pasa a Cosechado
    public function cosechar($id)
    {
        $model = new CultivoModel();
        $planta = $model->find($id);

        if (! $planta || $planta['estado'] === CultivoModel::COSECHADO) {
            return redirect()->to('/');
        }

        $cosechas = new CosechaModel();

        if ($this->request->getMethod() === 'GET') {
            return view('huerto/cosechar', [
                'planta'     => $planta,
                'anteriores' => $cosechas->where('cultivo_id', $id)->orderBy('fecha')->findAll(),
            ]);
        }

        $fechaTexto = (string) $this->request->getPost('fecha');
        $fecha = \DateTime::createFromFormat('Y-m-d', $fechaTexto);
        $kg = str_replace(',', '.', trim((string) $this->request->getPost('kg')));
        $final = $this->request->getPost('final') === '1';

        if (! $fecha || $fecha->format('Y-m-d') !== $fechaTexto || $fecha > new \DateTime() || $fecha->format('Y-m-d') < $planta['fecha_siembra']) {
            return $this->volverConError('Revisá la fecha: tiene que ser entre la siembra y hoy.');
        }
        if ($kg !== '' && (! is_numeric($kg) || $kg < 0 || $kg > 100000)) {
            return $this->volverConError('Los kg tienen que ser un número, por ejemplo 2,5.');
        }

        $cosechas->insert([
            'cultivo_id'    => $id,
            'fecha'         => $fecha->format('Y-m-d'),
            'kg'            => $kg === '' ? null : round((float) $kg, 2),
            'final'         => $final ? 1 : 0,
            'observaciones' => mb_substr(trim((string) $this->request->getPost('observaciones')), 0, 255) ?: null,
        ]);

        if ($final) {
            $model->update($id, ['estado' => CultivoModel::COSECHADO]);
        }

        return redirect()->to('/');
    }

    public function eliminar($id)
    {
        (new CultivoModel())->delete($id);

        return redirect()->to('/');
    }

    // Formulario de alta con la guía de cultivo
    private function formularioCultivo()
    {
        $plan = $this->planRiego();
        $hoy = new \DateTime();
        // Cultivos en curso: la guía los usa para avisar vecinos, familias repetidas y consumo de agua
        $activos = $this->cultivosActivos();

        return view('huerto/crear', [
            'canteros' => $this->canteros(),
            'horarios' => CultivoModel::HORARIOS_RIEGO,
            // Si todavía no se corrió la migración de especies, el formulario funciona sin guía
            'especies' => db_connect()->tableExists('especies') ? (new EspecieModel())->catalogo() : [],
            'activos'  => $activos,
            'zona'     => $this->zona(),
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

    // Canteros del plano del huerto donde se puede ubicar un cultivo: [id => nombre]
    private function canteros(): array
    {
        return db_connect()->tableExists('plano_elementos') ? (new PlanoElementoModel())->plantables($this->huerto()['id']) : [];
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
        return (new CultivoModel())->enCurso()
            ->select('nombre_planta, fecha_siembra, frecuencia_riego_dias, cantidad_riego_litros')
            ->findAll();
    }
}
