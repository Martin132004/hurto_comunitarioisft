<?php

namespace App\Controllers;

use App\Libraries\PlanEmergencia;
use App\Models\CultivoModel;
use App\Models\EmergenciaModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

// Emergencias climáticas: el huertero avisa que viene Zonda, granizo o helada y sigue un plan de contingencia
class Emergencia extends BaseController
{
    public function _remap(string $metodo, ...$parametros)
    {
        if (! db_connect()->tableExists('emergencias')) {
            return redirect()->to('/')->with('error', 'Falta correr las migraciones: php spark migrate');
        }

        if (! method_exists($this, $metodo) || $metodo === '_remap' || ! (new \ReflectionMethod($this, $metodo))->isPublic()) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->{$metodo}(...$parametros);
    }

    // Elegir qué viene; si ya hay una emergencia en curso, se va directo a su plan
    public function index()
    {
        $model = new EmergenciaModel();
        $activa = $model->activa();
        if ($activa) {
            return redirect()->to('huerto/emergencia/' . $activa['id']);
        }

        return view('emergencia/index', [
            'tipos'     => PlanEmergencia::TIPOS,
            'historial' => $model->orderBy('inicio', 'DESC')->findAll(10),
        ]);
    }

    public function crear()
    {
        $model = new EmergenciaModel();
        $tipo = $this->request->getPost('tipo');

        if (! is_string($tipo) || ! isset(PlanEmergencia::TIPOS[$tipo])) {
            return redirect()->to('huerto/emergencia');
        }

        // Una sola emergencia activa: si ya hay una, se sigue con esa
        $activa = $model->activa();
        if (! $activa) {
            try {
                $model->insert([
                    'tipo'       => $tipo,
                    'estado'     => 'activa',
                    'activa'     => 1,
                    'inicio'     => date('Y-m-d H:i:s'),
                    'usuario_id' => session('usuario_id'),
                ]);
            } catch (DatabaseException) {
                // Otra persona la activó en el mismo instante: el índice único rechazó esta y se usa la suya
            }
            $activa = $model->activa();
        }

        if (! $activa) {
            return redirect()->to('huerto/emergencia')->with('error', 'No se pudo activar la emergencia. Probá de nuevo.');
        }

        return redirect()->to('huerto/emergencia/' . $activa['id']);
    }

    // Plan de contingencia si está activa; daños y reparación si ya terminó
    public function ver($id)
    {
        $emergencia = $this->buscar($id);

        $datos = [
            'emergencia' => $emergencia,
            'tipo'       => PlanEmergencia::TIPOS[$emergencia['tipo']],
            'hechos'     => EmergenciaModel::lista($emergencia['pasos']),
        ];

        if ($emergencia['estado'] === 'activa') {
            $cultivos = (new CultivoModel())->orderBy('nombre_planta')->findAll();

            return view('emergencia/plan', $datos + [
                'pasos'    => PlanEmergencia::pasosDelHuerto($emergencia['tipo']),
                'enRiesgo' => PlanEmergencia::enRiesgo($emergencia['tipo'], $cultivos),
            ]);
        }

        // Solo los cultivos que siguen en curso pueden reportarse al técnico
        $enCurso = array_column((new CultivoModel())->where('estado !=', 'Cosechado')->findAll(), 'id');

        return view('emergencia/resumen', $datos + [
            'danos'     => array_intersect_key(PlanEmergencia::DANOS, array_flip(EmergenciaModel::lista($emergencia['danos']))),
            'afectados' => EmergenciaModel::afectados($emergencia['cultivos']),
            'enCurso'   => array_map('intval', $enCurso),
        ]);
    }

    // Marca o desmarca un paso del plan o de la reparación (lo llama la casilla por fetch)
    public function paso($id)
    {
        $emergencia = $this->buscar($id);
        $clave = $this->request->getPost('clave');
        $db = db_connect();

        // Se bloquea la fila mientras se lee y se guarda la lista: dos personas marcando a la vez no se pisan
        $db->transStart();
        $fila = $db->query('SELECT * FROM emergencias WHERE id = ? FOR UPDATE', [$emergencia['id']])->getRowArray();

        if (! is_string($clave) || ! isset(PlanEmergencia::clavesValidas($fila)[$clave])) {
            $db->transComplete();

            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Ese paso no se puede marcar en esta emergencia.']);
        }

        $hechos = array_diff(EmergenciaModel::lista($fila['pasos']), [$clave]);
        if ($this->request->getPost('hecho') === '1') {
            $hechos[] = $clave;
        }
        $db->table('emergencias')->where('id', $fila['id'])->update(['pasos' => implode(',', $hechos) ?: null]);
        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'error' => 'No se pudo guardar el paso.']);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    // Falsa alarma: se cierra sin daños
    public function cancelar($id)
    {
        $emergencia = $this->buscar($id);
        if ($emergencia['estado'] === 'activa') {
            (new EmergenciaModel())->update($emergencia['id'], ['estado' => 'cancelada', 'activa' => null, 'fin' => date('Y-m-d H:i:s')]);
        }

        return redirect()->to('huerto/emergencia')->with('mensaje', 'Listo, quedó registrada como falsa alarma.');
    }

    // "Ya terminó": qué daños dejó y qué cultivos se vieron afectados
    public function terminar($id)
    {
        $emergencia = $this->buscar($id);
        if ($emergencia['estado'] !== 'activa') {
            return redirect()->to('huerto/emergencia/' . $emergencia['id']);
        }

        $tipo = PlanEmergencia::TIPOS[$emergencia['tipo']];
        $cultivos = (new CultivoModel())->where('estado !=', 'Cosechado')->orderBy('nombre_planta')->findAll();

        if ($this->request->getMethod() === 'GET') {
            return view('emergencia/terminar', [
                'emergencia' => $emergencia,
                'tipo'       => $tipo,
                'danos'      => array_intersect_key(PlanEmergencia::DANOS, array_flip($tipo['danos'])),
                'cultivos'   => $cultivos,
            ]);
        }

        $notas = $this->request->getPost('notas');
        if ($notas !== null && ! is_string($notas)) {
            return redirect()->back()->with('error', 'Revisá las notas: tienen que ser un texto.');
        }

        $sinDanos = $this->request->getPost('sin_danos') === '1';
        // Solo textos: una lista anidada daría error al compararla
        $elegidos = fn ($campo) => array_filter((array) $this->request->getPost($campo), 'is_string');
        $danos = $sinDanos ? [] : array_unique(array_intersect($elegidos('danos'), $tipo['danos']));
        $afectados = $sinDanos ? [] : array_filter($cultivos, fn ($c) => in_array((string) $c['id'], $elegidos('cultivos'), true));

        // Se guardan nombre y variedad: el historial no depende de que el cultivo siga existiendo
        $foto = array_map(fn ($c) => ['id' => (int) $c['id'], 'nombre' => $c['nombre_planta'], 'variedad' => $c['variedad']], array_values($afectados));

        (new EmergenciaModel())->update($emergencia['id'], [
            'estado'   => 'terminada',
            'activa'   => null,
            'fin'      => date('Y-m-d H:i:s'),
            'danos'    => implode(',', $danos) ?: null,
            'cultivos' => $foto ? json_encode($foto, JSON_UNESCAPED_UNICODE) : null,
            'notas'    => $sinDanos ? null : (mb_substr(trim((string) $notas), 0, 1000) ?: null),
        ]);

        return redirect()->to('huerto/emergencia/' . $emergencia['id']);
    }

    private function buscar($id): array
    {
        $emergencia = (new EmergenciaModel())->find((int) $id);
        if (! $emergencia || ! isset(PlanEmergencia::TIPOS[$emergencia['tipo']])) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $emergencia;
    }
}
