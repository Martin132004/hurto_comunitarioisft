<?php

namespace App\Controllers;

use App\Models\CultivoModel;

class Huerto extends BaseController
{
    // index() (Panel de Alertas): Consulta la BD y determina riegos/cosechas
    public function index()
    {
        $model = new CultivoModel();
        $cultivos = $model->findAll();
        
        $hoy = new \DateTime(); // Fecha actual del sistema

        // Recorremos cada cultivo para calcular sus alertas
        foreach ($cultivos as &$planta) {
            
            // 1. CÁLCULO DE COSECHA
            $fechaSiembra = new \DateTime($planta['fecha_siembra']);
            $fechaCosecha = clone $fechaSiembra;
            // Sumamos los días estimados a la fecha de siembra
            $fechaCosecha->modify('+' . $planta['dias_cosecha_estimados'] . ' days'); 
            
            // Si hoy es mayor o igual a la fecha de cosecha, activamos alerta
            $planta['alerta_cosecha'] = ($hoy >= $fechaCosecha);

            // 2. CÁLCULO DE RIEGO
            // Si nunca se regó, tomamos la fecha de siembra como base
            $ultimoRiego = $planta['ultimo_riego'] ? new \DateTime($planta['ultimo_riego']) : clone $fechaSiembra;
            $proximoRiego = clone $ultimoRiego;
            // Sumamos la frecuencia de riego a la fecha del último riego
            $proximoRiego->modify('+' . $planta['frecuencia_riego_dias'] . ' days');

            // Si hoy es mayor o igual al día del próximo riego, activamos alerta
            $planta['alerta_riego'] = ($hoy >= $proximoRiego);
        }

        $datos['cultivos'] = $cultivos;
        
        return view('huerto/index', $datos);
    }

    // crear(): Procesa los datos del formulario e inserta el nuevo cultivo
    public function crear()
    {
        // Si el usuario entra a ver la página, mostramos el formulario
        if ($this->request->getMethod() === 'GET') {
            return view('huerto/crear');
        }

        // Si envió el formulario, guardamos los datos
        $model = new CultivoModel();
        
        $datos = [
            'nombre_planta'          => $this->request->getPost('nombre_planta'),
            'variedad'               => $this->request->getPost('variedad'),
            'fecha_siembra'          => $this->request->getPost('fecha_siembra'),
            'dias_cosecha_estimados' => $this->request->getPost('dias_cosecha_estimados'),
            'frecuencia_riego_dias'  => $this->request->getPost('frecuencia_riego_dias'),
            'estado'                 => 'En Crecimiento',
            'ultimo_riego'           => date('Y-m-d H:i:s')
        ];

        $model->save($datos);
        return redirect()->to('/');
    }

    // registrarRiego($id): Actualiza el campo ultimo_riego a la fecha/hora actual
    public function registrarRiego($id)
    {
        $model = new CultivoModel();
        $model->update($id, ['ultimo_riego' => date('Y-m-d H:i:s')]);
        return redirect()->to('/');
    }

    // cambiarEstado($id): Permite modificar el estado de la planta a Cosechado
    public function cambiarEstado($id)
    {
        $model = new CultivoModel();
        $model->update($id, ['estado' => 'Cosechado']);
        return redirect()->to('/');
    }

    // eliminar($id): Elimina el registro del cultivo
    public function eliminar($id)
    {
        $model = new CultivoModel();
        $model->delete($id);
        return redirect()->to('/');
    }
}