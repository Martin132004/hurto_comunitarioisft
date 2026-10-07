<?php

namespace App\Models;

use CodeIgniter\Model;

class EspecieModel extends Model
{
    protected $table      = 'especies';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombre',
        'sinonimos',
        'familia',
        'dias_cosecha',
        'frecuencia_riego_dias',
        'horario_riego',
        'litros_riego',
        'meses_almacigo',
        'meses_siembra',
        'sensible_helada',
        'tolerancia_sal',
        'tipo_siembra',
        'exposicion',
        'distancia_cm',
        'profundidad_cm',
        'buenos_vecinos',
        'malos_vecinos',
        'consejos',
    ];

    // Devuelve el catálogo con las listas ya separadas, listo para enviarlo a la vista como JSON
    public function catalogo(): array
    {
        $lista = fn ($texto, $separador = ',') => array_values(array_filter(array_map('trim', explode($separador, (string) $texto))));

        return array_map(fn ($especie) => [
            'nombre'         => $especie['nombre'],
            'sinonimos'      => $lista($especie['sinonimos']),
            'familia'        => $especie['familia'],
            'dias_cosecha'   => (int) $especie['dias_cosecha'],
            'frecuencia'     => (int) $especie['frecuencia_riego_dias'],
            'horario'        => $especie['horario_riego'],
            'litros'         => (float) $especie['litros_riego'],
            'meses_almacigo' => array_map('intval', $lista($especie['meses_almacigo'])),
            'meses'          => array_map('intval', $lista($especie['meses_siembra'])),
            'sensible_helada' => (bool) $especie['sensible_helada'],
            'tolerancia_sal' => $especie['tolerancia_sal'],
            'tipo_siembra'   => $especie['tipo_siembra'],
            'exposicion'     => $especie['exposicion'],
            'distancia_cm'   => (int) $especie['distancia_cm'],
            'profundidad_cm' => (float) $especie['profundidad_cm'],
            'buenos_vecinos' => $lista($especie['buenos_vecinos']),
            'malos_vecinos'  => $lista($especie['malos_vecinos']),
            'consejos'       => $lista($especie['consejos'], "\n"),
        ], $this->orderBy('nombre')->findAll());
    }

    // Índice: nombre o sinónimo normalizado -> especie
    private ?array $indice = null;

    // Reconoce la especie de un cultivo por su nombre ("Tomates cherry" -> Tomate), igual que la guía de cultivo
    public function identificar(string $texto): ?array
    {
        if ($this->indice === null) {
            $this->indice = [];
            foreach ($this->findAll() as $especie) {
                foreach (array_merge([$especie['nombre']], explode(',', (string) $especie['sinonimos'])) as $nombre) {
                    if (trim($nombre) !== '') {
                        $this->indice[self::normalizar($nombre)] = $especie;
                    }
                }
            }
        }

        $palabras = array_values(array_filter(explode(' ', self::normalizar($texto))));

        // Si no encontramos "tomate cherry grande", probamos "tomate cherry" y después "tomate"
        for ($n = count($palabras); $n > 0; $n--) {
            $parte = array_slice($palabras, 0, $n);
            // Probamos el texto tal cual y en singular ("tomates" -> "tomate", "morrones" -> "morron")
            foreach ([$parte, preg_replace('/s$/', '', $parte), preg_replace('/es$/', '', $parte)] as $variante) {
                $clave = implode(' ', $variante);
                if (isset($this->indice[$clave])) {
                    return $this->indice[$clave];
                }
            }
        }

        return null;
    }

    // "Tomátes  Cherry" -> "tomates cherry"
    public static function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto));
        $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);

        return preg_replace('/\s+/', ' ', $texto);
    }
}
