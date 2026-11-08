<?php

namespace App\Libraries;

use DateTimeInterface;

// Nombres de meses y días en castellano (no dependen de la configuración regional del servidor)
class Fechas
{
    public const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    public const DIAS = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];

    public static function mes(DateTimeInterface $fecha): string
    {
        return self::MESES[(int) $fecha->format('n') - 1];
    }

    public static function dia(DateTimeInterface $fecha): string
    {
        return self::DIAS[(int) $fecha->format('w')];
    }

    // "domingo 8 de noviembre"
    public static function larga(DateTimeInterface $fecha): string
    {
        return self::dia($fecha) . ' ' . $fecha->format('j') . ' de ' . self::mes($fecha);
    }

    // "09-15" (mes-día, como se guardan las heladas de cada zona) -> "15 de septiembre"
    public static function diaMes(string $mesDia): string
    {
        return (int) substr($mesDia, 3, 2) . ' de ' . self::MESES[(int) substr($mesDia, 0, 2) - 1];
    }
}
