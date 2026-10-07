<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Carga las zonas agroclimáticas.
 * Las fechas de helada son medias aproximadas (MM-DD); desfase_meses indica cuánto
 * se atrasa la siembra de las especies sensibles a la helada respecto de la zona de referencia.
 * coef_riego_mensual: factor de enero a diciembre que multiplica los litros de riego
 * (1 = primavera u otoño templados; más de 1 en verano, menos de 1 en invierno).
 */
class ZonasSeeder extends Seeder
{
    public function run()
    {
        $zonas = [
            [
                'clave' => 'tulum', 'nombre' => 'Valle de Tulum, Ullum y Zonda',
                'departamentos' => 'Capital, Rawson, Rivadavia, Chimbas, Santa Lucía, Pocito, 9 de Julio, 25 de Mayo, Sarmiento, Caucete, San Martín, Angaco, Albardón, Ullum, Zonda',
                'altitud_m' => 650, 'ultima_helada' => '09-15', 'primera_helada' => '05-20', 'desfase_meses' => 0,
                'coef_riego_mensual' => '1.6,1.4,1.1,0.8,0.55,0.4,0.45,0.65,0.9,1.2,1.45,1.6',
                'descripcion' => 'Valle central irrigado. Veranos muy calurosos, inviernos con heladas y viento Zonda frecuente a fines de invierno y primavera.',
            ],
            [
                'clave' => 'valle_fertil', 'nombre' => 'Valle Fértil',
                'departamentos' => 'Valle Fértil',
                'altitud_m' => 850, 'ultima_helada' => '08-31', 'primera_helada' => '06-01', 'desfase_meses' => 0,
                'coef_riego_mensual' => '1.5,1.35,1.05,0.8,0.55,0.45,0.5,0.65,0.9,1.15,1.35,1.5',
                'descripcion' => 'Zona serrana más templada y con algo más de lluvia en verano. Período sin heladas un poco más largo.',
            ],
            [
                'clave' => 'jachal', 'nombre' => 'Jáchal',
                'departamentos' => 'Jáchal',
                'altitud_m' => 1150, 'ultima_helada' => '10-01', 'primera_helada' => '05-10', 'desfase_meses' => 1,
                'coef_riego_mensual' => '1.55,1.35,1.05,0.75,0.5,0.4,0.45,0.6,0.85,1.15,1.4,1.55',
                'descripcion' => 'Valle del norte, más alto y frío que el valle central. Las siembras de verano empiezan más tarde.',
            ],
            [
                'clave' => 'calingasta', 'nombre' => 'Calingasta',
                'departamentos' => 'Calingasta (Barreal, Tamberías, Villa Calingasta)',
                'altitud_m' => 1500, 'ultima_helada' => '10-20', 'primera_helada' => '04-25', 'desfase_meses' => 1,
                'coef_riego_mensual' => '1.5,1.3,1.0,0.7,0.45,0.35,0.4,0.55,0.8,1.1,1.35,1.5',
                'descripcion' => 'Valle precordillerano con gran amplitud térmica: días cálidos y noches frías. Temporada de verano más corta.',
            ],
            [
                'clave' => 'iglesia', 'nombre' => 'Iglesia',
                'departamentos' => 'Iglesia (Rodeo, Las Flores, Tudcum, Angualasto)',
                'altitud_m' => 1900, 'ultima_helada' => '11-05', 'primera_helada' => '04-10', 'desfase_meses' => 2,
                'coef_riego_mensual' => '1.45,1.25,0.95,0.65,0.4,0.3,0.35,0.5,0.75,1.05,1.3,1.45',
                'descripcion' => 'Zona de altura, la más fría. Heladas tardías hasta noviembre y tempranas desde abril: conviene elegir variedades de ciclo corto.',
            ],
        ];

        $this->db->table('zonas')->truncate();
        $this->db->table('zonas')->insertBatch($zonas);
    }
}
