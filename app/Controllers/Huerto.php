<?php

namespace App\Controllers;

use App\Models\CultivoModel;

class Huerto extends BaseController
{
    public function index()
    {
        $model = new CultivoModel();
        $cultivos = $model->findAll();

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
        }

        $datos['cultivos'] = $cultivos;

        return view('huerto/index', $datos);
    }

    public function crear()
    {
        if ($this->request->getMethod() === 'GET') {
            return view('huerto/crear', ['horarios' => CultivoModel::HORARIOS_RIEGO]);
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

        $model->save($datos);
        return redirect()->to('/');
    }

    public function registrarRiego($id)
    {
        $model = new CultivoModel();
        $model->update($id, ['ultimo_riego' => date('Y-m-d H:i:s')]);

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

        $frecuencia = max(1, (int) $planta['frecuencia_riego_dias']);
        $ultimoRiego = new \DateTime($planta['ultimo_riego'] ?? 'now');

        $proximoRiego = clone $ultimoRiego;
        $proximoRiego->modify('+' . $frecuencia . ' days');

        $fechaCosecha = new \DateTime($planta['fecha_siembra']);
        $fechaCosecha->modify('+' . $planta['dias_cosecha_estimados'] . ' days');

        // Próximos riegos programados hasta la cosecha (máximo 5 para mostrar)
        $calendario = [];
        $fecha = clone $proximoRiego;
        while ($fecha <= $fechaCosecha && count($calendario) < 5) {
            $calendario[] = clone $fecha;
            $fecha->modify('+' . $frecuencia . ' days');
        }

        $riegosRestantes = 0;
        if ($proximoRiego <= $fechaCosecha) {
            $diasHastaCosecha = (int) $proximoRiego->diff($fechaCosecha)->days;
            $riegosRestantes = intdiv($diasHastaCosecha, $frecuencia) + 1;
        }

        $horarios = CultivoModel::HORARIOS_RIEGO;

        $datos = [
            'planta'          => $planta,
            'frecuencia'      => $frecuencia,
            'proximoRiego'    => $proximoRiego,
            'calendario'      => $calendario,
            'riegosRestantes' => $riegosRestantes,
            'horario'         => $horarios[$planta['horario_riego']] ?? $horarios['manana'],
            'cantidad'        => (float) $planta['cantidad_riego_litros'],
        ];

        return view('huerto/regando', $datos);
    }

    public function cambiarEstado($id)
    {
        $model = new CultivoModel();
        $model->update($id, ['estado' => 'Cosechado']);
        return redirect()->to('/');
    }

    public function eliminar($id)
    {
        $model = new CultivoModel();
        $model->delete($id);
        return redirect()->to('/');
    }
}