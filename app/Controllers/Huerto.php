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
            
            // Si hoy es mayor o igual a la fecha de cosecha, activamos la alerta verde
            $planta['alerta_cosecha'] = ($hoy >= $fechaCosecha);

            // 2. CÁLCULO DE RIEGO
            // Si nunca se regó, tomamos la fecha de siembra como base
            $ultimoRiego = $planta['ultimo_riego'] ? new \DateTime($planta['ultimo_riego']) : $fechaSiembra;
            $proximoRiego = clone $ultimoRiego;
            // Sumamos la frecuencia de riego a la fecha del último riego
            $proximoRiego->modify('+' . $planta['frecuencia_riego_dias'] . ' days');

            // Si hoy es mayor o igual al día del próximo riego, activamos la alerta roja
            $planta['alerta_riego'] = ($hoy >= $proximoRiego);
        }

        $datos['cultivos'] = $cultivos;
        
        return view('huerto/index', $datos);
    }
}