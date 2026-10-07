<?php

namespace App\Models;

use CodeIgniter\Model;

// Catálogo de plagas, enfermedades y daños
class ProblemaModel extends Model
{
    public const TIPOS = [
        'plaga'      => ['nombre' => 'Plaga', 'icono' => 'bi-bug', 'color' => 'danger'],
        'enfermedad' => ['nombre' => 'Enfermedad', 'icono' => 'bi-virus', 'color' => 'warning'],
        'fisiopatia' => ['nombre' => 'Problema de riego o suelo', 'icono' => 'bi-moisture', 'color' => 'primary'],
        'clima'      => ['nombre' => 'Daño por clima', 'icono' => 'bi-cloud-lightning-rain', 'color' => 'info'],
        'animal'     => ['nombre' => 'Animales', 'icono' => 'bi-bug-fill', 'color' => 'secondary'],
    ];

    protected $table      = 'problemas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'clave',
        'nombre',
        'tipo',
        'afecta',
        'meses_riesgo',
        'sintomas',
        'manejo',
        'prevencion',
    ];

    // Catálogo indexado por clave, con las listas ya separadas
    public function catalogo(): array
    {
        $lista = fn ($texto, $separador) => array_values(array_filter(array_map('trim', explode($separador, (string) $texto))));
        $catalogo = [];

        foreach ($this->orderBy('nombre')->findAll() as $problema) {
            $catalogo[$problema['clave']] = [
                'clave'      => $problema['clave'],
                'nombre'     => $problema['nombre'],
                'tipo'       => $problema['tipo'],
                'afecta'     => $lista($problema['afecta'], ','),
                'meses'      => array_map('intval', $lista($problema['meses_riesgo'], ',')),
                'sintomas'   => $lista($problema['sintomas'], "\n"),
                'manejo'     => $lista($problema['manejo'], "\n"),
                'prevencion' => $lista($problema['prevencion'], "\n"),
            ];
        }

        return $catalogo;
    }

    // ¿El problema puede afectar a esta especie? Se compara por nombre de especie o de familia
    public static function afectaA(array $problema, ?array $especie): bool
    {
        if (in_array('*', $problema['afecta'], true)) {
            return true;
        }
        if (! $especie) {
            return false;
        }

        return in_array($especie['nombre'], $problema['afecta'], true)
            || in_array($especie['familia'], $problema['afecta'], true);
    }

    // ¿Es un mes de riesgo? En las zonas más frías el calendario se corre según el desfase
    public static function enRiesgo(array $problema, int $mes, int $desfase = 0): bool
    {
        $mesReferencia = (($mes - 1 - $desfase) % 12 + 12) % 12 + 1;

        return in_array($mesReferencia, $problema['meses'], true);
    }
}
