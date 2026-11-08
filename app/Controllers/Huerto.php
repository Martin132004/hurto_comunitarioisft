<?php

namespace App\Controllers;

use App\Libraries\Fechas;
use App\Libraries\PlanRiego;
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
    // Panel: el controlador calcula todo lo que se muestra; la vista solo lo dibuja
    public function index()
    {
        $plan = $this->planRiego();
        $hoy = new \DateTime('today');
        $riego = $plan->resumen($this->cultivosActivos(), $hoy);
        $hoyHayTurno = $plan->esDiaDeTurno($hoy);
        $cultivos = array_map(fn ($c) => $this->datosDeTarjeta($c, $plan, $hoy), (new CultivoModel())->findAll());

        // Lo urgente primero: regar, cosechar, en crecimiento y al final lo cosechado
        $orden = ['riego' => 0, 'cosecha' => 1, 'crecimiento' => 2, 'cosechado' => 3];
        usort($cultivos, fn ($a, $b) => $orden[$a['estado_panel']] <=> $orden[$b['estado_panel']]);

        $cuentas = ['todos' => count($cultivos)] + array_fill_keys(array_keys($orden), 0);
        foreach ($cultivos as $c) {
            $cuentas[$c['estado_panel']]++;
        }
        // Los que están para cosechar también cuentan si además les toca riego
        $cuentas['riego'] = count(array_filter($cultivos, fn ($c) => $c['necesita_riego']));

        // Lo que hay que hacer hoy
        $paraRegar = array_filter($cultivos, fn ($c) => $c['necesita_riego']);
        $paraCosechar = array_filter($cultivos, fn ($c) => $c['estado_panel'] === 'cosecha');
        $regadosHoy = array_filter($cultivos, fn ($c) => $c['regado_hoy']);
        $nombresRegados = array_values(array_map(fn ($c) => mb_strtolower($c['nombre_planta']), $regadosHoy));

        // Si hoy no hay nada, el riego más cercano
        $proximoRiego = null;
        foreach ($cultivos as $c) {
            if ($c['estado_panel'] !== 'cosechado' && ($proximoRiego === null || $c['dias_proximo_riego'] < $proximoRiego['dias_proximo_riego'])) {
                $proximoRiego = $c;
            }
        }

        return view('huerto/index', [
            'cultivos'      => $cultivos,
            'zona'          => $this->zona(),
            'huerto'        => $this->huerto(),
            'riego'         => $riego,
            'hoyHayTurno'   => $hoyHayTurno,
            'problemas'     => $this->resumenProblemas(),
            'kgCosechados'  => (new CosechaModel())->kgPorCultivo(),
            'hoyFecha'      => $hoy,
            'hoyTexto'      => Fechas::larga($hoy),
            'cuentas'       => $cuentas,
            'paraRegar'     => $paraRegar,
            'paraCosechar'  => $paraCosechar,
            'regadosHoy'    => $regadosHoy,
            'listaRegados'  => count($nombresRegados) > 1
                ? implode(', ', array_slice($nombresRegados, 0, -1)) . ' y ' . end($nombresRegados)
                : implode('', $nombresRegados),
            'hayTareas'     => $paraRegar || $paraCosechar || ($riego['usa_turno'] && $hoyHayTurno && $riego['reservorio'] > 0),
            'proximoRiego'  => $proximoRiego,
            'semana'        => $this->semanaDeRiego($cultivos, $plan, $riego['usa_turno'], $hoy),
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
        $zonas = (new ZonaModel())->orderBy('id')->findAll();

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
        (new RiegoModel())->insert([
            'cultivo_id' => $id,
            'fecha'      => $ahora->format('Y-m-d H:i:s'),
            'litros'     => $this->planRiego()->litrosAplicar((float) $planta['cantidad_riego_litros'], $ahora),
        ]);

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

    // Datos de la tarjeta de un cultivo: estado, avance del ciclo, próximo riego y litros de este mes
    private function datosDeTarjeta(array $planta, PlanRiego $plan, \DateTime $hoy): array
    {
        $siembra = new \DateTime($planta['fecha_siembra']);
        $cosecha = CultivoModel::fechaCosecha($planta);
        $proximoRiego = CultivoModel::proximoRiego($planta);
        $diasDesdeSiembra = $siembra <= $hoy ? $siembra->diff($hoy)->days : 0;
        $diasProximoRiego = $hoy >= $proximoRiego ? 0 : $hoy->diff($proximoRiego)->days;
        $cosechado = $planta['estado'] === CultivoModel::COSECHADO;
        // Una sola regla para todo el panel: le toca riego si su próximo riego cae hoy o ya pasó
        $necesitaRiego = ! $cosechado && $diasProximoRiego === 0;

        return $planta + [
            'alerta_cosecha'      => $hoy >= $cosecha,
            'alerta_riego'        => $hoy >= $proximoRiego,
            'dias_desde_siembra'  => $diasDesdeSiembra,
            'progreso'            => min(100, (int) round($diasDesdeSiembra * 100 / max(1, (int) $planta['dias_cosecha_estimados']))),
            'fecha_cosecha'       => $cosecha,
            'dias_a_cosecha'      => $hoy->diff($cosecha)->days,
            'dias_proximo_riego'  => $diasProximoRiego,
            'litros_hoy'          => $plan->litrosAplicar((float) $planta['cantidad_riego_litros'], $hoy),
            'necesita_riego'      => $necesitaRiego,
            'regado_hoy'          => ! $cosechado && ! empty($planta['ultimo_riego']) && date('Y-m-d', strtotime($planta['ultimo_riego'])) === date('Y-m-d'),
            // Estado de la tarjeta, en orden de prioridad
            'estado_panel'        => match (true) {
                $cosechado            => 'cosechado',
                $hoy >= $cosecha      => 'cosecha',
                $necesitaRiego        => 'riego',
                default               => 'crecimiento',
            },
            'proximo_riego_texto' => match (true) {
                $cosechado              => '—',
                $diasProximoRiego === 0 => 'Hoy',
                $diasProximoRiego === 1 => 'Mañana',
                default                 => 'En ' . $diasProximoRiego . ' días',
            },
        ];
    }

    // Los próximos 7 días: cuántos riegos tocan cada día y si hay turno de agua
    private function semanaDeRiego(array $cultivos, PlanRiego $plan, bool $usaTurno, \DateTime $hoy): array
    {
        $diasCortos = ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb'];
        $semana = [];
        for ($d = 0; $d < 7; $d++) {
            $fecha = (clone $hoy)->modify("+{$d} day");
            $riegos = 0;
            foreach ($cultivos as $c) {
                $desde = max(0, (int) $c['dias_proximo_riego']);
                $cada = max(1, (int) $c['frecuencia_riego_dias']);
                if ($c['estado_panel'] !== 'cosechado' && $d >= $desde && ($d - $desde) % $cada === 0) {
                    $riegos++;
                }
            }
            $semana[] = [
                'nombre' => $d === 0 ? 'hoy' : $diasCortos[(int) $fecha->format('w')],
                'largo'  => Fechas::dia($fecha) . ' ' . $fecha->format('j'),
                'numero' => $fecha->format('j'),
                'riegos' => $riegos,
                'turno'  => $usaTurno && $plan->esDiaDeTurno($fecha),
            ];
        }

        return $semana;
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
            'especies' => (new EspecieModel())->catalogo(),
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
        return (new PlanoElementoModel())->plantables($this->huerto()['id']);
    }

    // Reportes sin resolver y problemas repetidos para mostrar en el panel
    private function resumenProblemas(): array
    {
        $huerto = $this->huerto();
        $reportes = new ReporteProblemaModel();
        $nombres = array_column((new ProblemaModel())->catalogo(), 'nombre', 'clave');
        $nombrar = fn (array $claves) => array_map(fn ($clave) => $nombres[$clave] ?? $clave, $claves);

        return [
            'sin_resolver' => $reportes->where('huerto_id', $huerto['id'])->where('estado !=', 'resuelto')->countAllResults(),
            'respondidos'  => $reportes->where('huerto_id', $huerto['id'])->where('estado', 'respondido')->countAllResults(),
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
