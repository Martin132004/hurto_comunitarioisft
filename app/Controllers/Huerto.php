<?php

namespace App\Controllers;

use App\Models\CultivoModel;

class Huerto extends BaseController
{
    // index() (Panel de Alertas): Consulta la BD y determina riegos/cosechas
    public function index()
    {
        $model = new CultivoModel();
        
        // Obtenemos todos los cultivos de la base de datos
        $datos['cultivos'] = $model->findAll();
        
        // Más adelante agregaremos aquí el algoritmo de comparación de fechas en PHP
        
        // Cargamos la vista principal enviándole los datos
        return view('huerto/index', $datos);
    }

    // crear(): Procesa y valida los datos del formulario e inserta el nuevo cultivo
    public function crear()
    {
        $model = new CultivoModel();
        
        // Aquí programaremos la validación y el $model->save()
    }

    // registrarRiego($id): Actualiza el campo ultimo_riego a la fecha/hora actual
    public function registrarRiego($id)
    {
        $model = new CultivoModel();
        
        // Aquí programaremos la actualización de la fecha
    }

    // cambiarEstado($id): Permite modificar el estado de la planta
    public function cambiarEstado($id)
    {
        $model = new CultivoModel();
        
        // Aquí programaremos el cambio de "En Crecimiento" a "Cosechado"
    }

    // eliminar($id): Elimina el registro del cultivo
    public function eliminar($id)
    {
        $model = new CultivoModel();
        $model->delete($id); // Elimina el registro mediante su ID
        
        return redirect()->to('/'); // Redirige al panel principal
    }
}