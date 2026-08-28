<?php

namespace App\Models;

use CodeIgniter\Model;

class CultivoModel extends Model
{
    // Nombre de la tabla y clave primaria definidos
    protected $table      = 'cultivos';
    protected $primaryKey = 'id';

    // Lista de campos permitidos para insertar o modificar
    protected $allowedFields = [
        'nombre_planta',
        'variedad',
        'fecha_siembra',
        'dias_cosecha_estimados',
        'frecuencia_riego_dias',
        'ultimo_riego',
        'estado'
    ];

    // Activación de la propiedad para la gestión automática de fechas
    protected $useTimestamps = true;
}