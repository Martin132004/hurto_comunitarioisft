<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Carga el catálogo de especies de la guía de cultivo.
 *
 * Los meses corresponden a la zona de referencia (desfase 0); en las zonas más frías
 * el sistema corre el calendario de las especies sensibles a la helada.
 * - meses_almacigo: siembra en almácigo protegido (vacío si no se hace almácigo).
 * - meses_siembra: siembra directa o trasplante al lugar definitivo.
 * - Los vecinos deben escribirse con el mismo nombre que la especie en este catálogo.
 *
 * Uso: php spark db:seed CatalogoSeeder
 */
class EspeciesSeeder extends Seeder
{
    public function run()
    {
        $especies = [
            [
                'nombre' => 'Tomate', 'sinonimos' => 'jitomate, tomate cherry, tomate perita, tomate redondo',
                'familia' => 'Solanáceas', 'dias_cosecha' => 90, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1.5,
                'meses_almacigo' => '7,8', 'meses_siembra' => '9,10,11', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Almácigo y trasplante', 'exposicion' => 'Sol pleno', 'distancia_cm' => 50, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Albahaca, Zanahoria, Cebolla, Perejil, Lechuga', 'malos_vecinos' => 'Papa, Maíz, Repollo, Brócoli',
                'consejos' => "Hacé el almácigo bajo cubierta y trasplantá recién cuando pase la última helada.\nNo deshojes de más: las hojas protegen a los frutos del golpe de sol.\nColocá tutores firmes; con viento Zonda las plantas sin atar se quiebran.\nCubrí el suelo con paja o rastrojo para que no pierda humedad.",
            ],
            [
                'nombre' => 'Lechuga', 'sinonimos' => 'lechuga criolla, lechuga mantecosa, lechuga morada',
                'familia' => 'Asteráceas', 'dias_cosecha' => 60, 'frecuencia_riego_dias' => 1, 'horario_riego' => 'ambos', 'litros_riego' => 0.5,
                'meses_almacigo' => '', 'meses_siembra' => '2,3,4,5,6,7,8,9', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa o almácigo', 'exposicion' => 'Sol en invierno, media sombra en verano', 'distancia_cm' => 25, 'profundidad_cm' => 0.5,
                'buenos_vecinos' => 'Zanahoria, Rabanito, Frutilla, Cebolla, Tomate', 'malos_vecinos' => 'Perejil',
                'consejos' => "Es un cultivo de otoño a primavera: con el calor del verano se espiga y se pone amarga.\nSembrá de a poco cada 15 a 20 días para cosechar siempre.\nSi la sembrás a fines de invierno, poné malla media sombra cuando empiece el calor.",
            ],
            [
                'nombre' => 'Zanahoria', 'sinonimos' => '',
                'familia' => 'Apiáceas', 'dias_cosecha' => 100, 'frecuencia_riego_dias' => 3, 'horario_riego' => 'manana', 'litros_riego' => 1,
                'meses_almacigo' => '', 'meses_siembra' => '2,3,4,7,8,9', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 5, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Lechuga, Cebolla, Tomate, Rabanito, Arveja', 'malos_vecinos' => 'Perejil',
                'consejos' => "Necesita tierra suelta y sin piedras: en suelos pesados mezclá arena y compost.\nMantené húmeda la superficie hasta que nazca; si se forma costra no emerge.\nRaleá cuando tengan 5 cm dejando una planta cada 5 cm.",
            ],
            [
                'nombre' => 'Papa', 'sinonimos' => 'patata',
                'familia' => 'Solanáceas', 'dias_cosecha' => 120, 'frecuencia_riego_dias' => 4, 'horario_riego' => 'manana', 'litros_riego' => 2,
                'meses_almacigo' => '', 'meses_siembra' => '8,9,1,2', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa (tubérculo)', 'exposicion' => 'Sol pleno', 'distancia_cm' => 30, 'profundidad_cm' => 10,
                'buenos_vecinos' => 'Haba, Poroto, Repollo', 'malos_vecinos' => 'Tomate, Zapallo, Pepino, Berenjena, Melón, Sandía',
                'consejos' => "Hay dos épocas: papa temprana a fines de invierno y papa de segunda en verano.\nSi brota antes de la última helada, aporcá (arrimá tierra) para proteger los brotes.\nLas papas expuestas al sol se ponen verdes y no se comen.",
            ],
            [
                'nombre' => 'Zapallo', 'sinonimos' => 'calabaza, zapallo anco, anco, zapallo criollo',
                'familia' => 'Cucurbitáceas', 'dias_cosecha' => 120, 'frecuencia_riego_dias' => 3, 'horario_riego' => 'manana', 'litros_riego' => 4,
                'meses_almacigo' => '', 'meses_siembra' => '9,10,11,12', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 100, 'profundidad_cm' => 3,
                'buenos_vecinos' => 'Maíz, Poroto', 'malos_vecinos' => 'Papa',
                'consejos' => "Ocupa mucho espacio: dejale lugar para que se extienda.\nRegá al pie y en profundidad; un acolchado de paja reduce mucho el riego.\nCosechá cuando el cabo se seque y la cáscara esté dura, antes de las primeras heladas.",
            ],
            [
                'nombre' => 'Zapallito', 'sinonimos' => 'zapallito de tronco, zucchini, zuchini, calabacin',
                'familia' => 'Cucurbitáceas', 'dias_cosecha' => 60, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 2,
                'meses_almacigo' => '', 'meses_siembra' => '9,10,11,12,1', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 80, 'profundidad_cm' => 3,
                'buenos_vecinos' => 'Maíz, Poroto, Cebolla', 'malos_vecinos' => 'Papa',
                'consejos' => "Cosechalos chicos y tiernos: la planta produce más.\nSi lo sembrás en enero, alcanzás a cosechar antes del frío.\nSi las flores caen sin dar fruto, falta polinización: atraé abejas con flores.",
            ],
            [
                'nombre' => 'Pepino', 'sinonimos' => '',
                'familia' => 'Cucurbitáceas', 'dias_cosecha' => 60, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1.5,
                'meses_almacigo' => '', 'meses_siembra' => '10,11,12,1', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 50, 'profundidad_cm' => 2,
                'buenos_vecinos' => 'Poroto, Maíz, Lechuga, Rabanito', 'malos_vecinos' => 'Papa',
                'consejos' => "Guialo en una malla o espaldera para ahorrar espacio.\nLa falta de agua lo pone amargo: con el calor fuerte no dejes que el suelo se seque.",
            ],
            [
                'nombre' => 'Melón', 'sinonimos' => 'melon rocio de miel, melon escrito',
                'familia' => 'Cucurbitáceas', 'dias_cosecha' => 100, 'frecuencia_riego_dias' => 3, 'horario_riego' => 'manana', 'litros_riego' => 3,
                'meses_almacigo' => '8', 'meses_siembra' => '9,10,11', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa o almácigo protegido', 'exposicion' => 'Sol pleno', 'distancia_cm' => 100, 'profundidad_cm' => 2,
                'buenos_vecinos' => 'Maíz, Rabanito, Poroto', 'malos_vecinos' => 'Papa',
                'consejos' => "Necesita mucho calor y sol: es un cultivo ideal para zonas secas.\nReducí el riego cuando los frutos empiezan a madurar: concentra el azúcar.\nEstá listo cuando el cabo se despega fácil y tiene aroma.",
            ],
            [
                'nombre' => 'Sandía', 'sinonimos' => 'sandias',
                'familia' => 'Cucurbitáceas', 'dias_cosecha' => 100, 'frecuencia_riego_dias' => 3, 'horario_riego' => 'manana', 'litros_riego' => 4,
                'meses_almacigo' => '', 'meses_siembra' => '9,10,11', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 150, 'profundidad_cm' => 3,
                'buenos_vecinos' => 'Maíz, Rabanito', 'malos_vecinos' => 'Papa',
                'consejos' => "Ocupa mucho espacio: dejá al menos 1,5 m entre plantas.\nEstá lista cuando se seca el zarcillo junto al fruto y la mancha donde apoya se pone amarilla.\nRegá menos en la maduración para que no se rajen.",
            ],
            [
                'nombre' => 'Pimiento', 'sinonimos' => 'morron, aji, aji picante',
                'familia' => 'Solanáceas', 'dias_cosecha' => 110, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1,
                'meses_almacigo' => '7,8', 'meses_siembra' => '9,10,11', 'sensible_helada' => 1, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Almácigo y trasplante', 'exposicion' => 'Sol pleno (media sombra en pleno verano)', 'distancia_cm' => 45, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Albahaca, Cebolla, Zanahoria', 'malos_vecinos' => 'Poroto',
                'consejos' => "Es muy sensible al frío: trasplantá cuando no haya riesgo de heladas.\nCon el sol fuerte del verano los frutos se queman: una malla media sombra los protege.\nUn tutor corto evita que las ramas se quiebren con el viento.",
            ],
            [
                'nombre' => 'Berenjena', 'sinonimos' => '',
                'familia' => 'Solanáceas', 'dias_cosecha' => 120, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1.5,
                'meses_almacigo' => '7,8', 'meses_siembra' => '9,10,11', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Almácigo y trasplante', 'exposicion' => 'Sol pleno', 'distancia_cm' => 60, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Poroto, Albahaca', 'malos_vecinos' => 'Papa',
                'consejos' => "Necesita mucho calor para dar frutos: rinde muy bien en verano.\nCosechala con la piel brillante; si se pone opaca está pasada.",
            ],
            [
                'nombre' => 'Cebolla', 'sinonimos' => 'cebolla de verdeo, verdeo',
                'familia' => 'Amarilidáceas', 'dias_cosecha' => 150, 'frecuencia_riego_dias' => 4, 'horario_riego' => 'manana', 'litros_riego' => 0.5,
                'meses_almacigo' => '3,4', 'meses_siembra' => '6,7,8', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Almácigo y trasplante', 'exposicion' => 'Sol pleno', 'distancia_cm' => 10, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Tomate, Zanahoria, Lechuga, Frutilla', 'malos_vecinos' => 'Poroto, Arveja, Haba',
                'consejos' => "Hacé el almácigo en otoño y trasplantá en invierno.\nMantenela libre de yuyos: compite mal con otras plantas.\nCortá el riego cuando las hojas se empiezan a doblar, antes de cosechar.",
            ],
            [
                'nombre' => 'Ajo', 'sinonimos' => 'ajo blanco, ajo colorado',
                'familia' => 'Amarilidáceas', 'dias_cosecha' => 240, 'frecuencia_riego_dias' => 7, 'horario_riego' => 'manana', 'litros_riego' => 0.5,
                'meses_almacigo' => '', 'meses_siembra' => '2,3,4', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa (diente)', 'exposicion' => 'Sol pleno', 'distancia_cm' => 12, 'profundidad_cm' => 4,
                'buenos_vecinos' => 'Tomate, Frutilla, Zanahoria', 'malos_vecinos' => 'Poroto, Arveja, Haba',
                'consejos' => "Usá como semilla los dientes más grandes y sanos, con la punta hacia arriba.\nEl frío del invierno le hace bien: no necesita protección.\nDejá de regar unas 3 o 4 semanas antes de cosechar para que se conserve mejor.",
            ],
            [
                'nombre' => 'Acelga', 'sinonimos' => '',
                'familia' => 'Amarantáceas', 'dias_cosecha' => 60, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1,
                'meses_almacigo' => '', 'meses_siembra' => '2,3,4,8,9', 'sensible_helada' => 0, 'tolerancia_sal' => 'Alta',
                'tipo_siembra' => 'Directa o almácigo', 'exposicion' => 'Sol o media sombra', 'distancia_cm' => 30, 'profundidad_cm' => 2,
                'buenos_vecinos' => 'Lechuga, Cebolla, Poroto', 'malos_vecinos' => '',
                'consejos' => "Es de las hortalizas que mejor soportan suelos y aguas con sales.\nCosechá las hojas de afuera y dejá crecer las del centro: rinde meses.\nCada \"semilla\" da varias plantitas; raleá dejando la más fuerte.",
            ],
            [
                'nombre' => 'Espinaca', 'sinonimos' => '',
                'familia' => 'Amarantáceas', 'dias_cosecha' => 50, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 0.5,
                'meses_almacigo' => '', 'meses_siembra' => '3,4,5,6,7', 'sensible_helada' => 0, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol de invierno', 'distancia_cm' => 15, 'profundidad_cm' => 2,
                'buenos_vecinos' => 'Frutilla, Lechuga, Rabanito', 'malos_vecinos' => '',
                'consejos' => "Es de estación fría: el invierno es su mejor momento.\nCon los primeros calores florece enseguida.\nCosechá hojas sueltas a medida que las necesites.",
            ],
            [
                'nombre' => 'Rabanito', 'sinonimos' => 'rabano, rabanitos',
                'familia' => 'Brasicáceas', 'dias_cosecha' => 30, 'frecuencia_riego_dias' => 1, 'horario_riego' => 'ambos', 'litros_riego' => 0.3,
                'meses_almacigo' => '', 'meses_siembra' => '3,4,5,6,7,8,9', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol o media sombra', 'distancia_cm' => 5, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Lechuga, Zanahoria, Espinaca, Pepino', 'malos_vecinos' => '',
                'consejos' => "Es el cultivo más rápido: ideal para empezar.\nEvitá el pleno verano: con calor y poca agua se ponen picantes y huecos.\nNo te pases de fecha: se vuelven fibrosos.",
            ],
            [
                'nombre' => 'Albahaca', 'sinonimos' => '',
                'familia' => 'Lamiáceas', 'dias_cosecha' => 60, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 0.5,
                'meses_almacigo' => '8', 'meses_siembra' => '9,10,11,12', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Almácigo y trasplante', 'exposicion' => 'Sol pleno', 'distancia_cm' => 25, 'profundidad_cm' => 0.5,
                'buenos_vecinos' => 'Tomate, Pimiento, Berenjena', 'malos_vecinos' => '',
                'consejos' => "La primera helada la mata: cosechá todo antes del frío.\nCortá las flores apenas aparezcan para que siga dando hojas.\nCosechá cortando arriba de un par de hojas: rebrota más tupida.",
            ],
            [
                'nombre' => 'Perejil', 'sinonimos' => '',
                'familia' => 'Apiáceas', 'dias_cosecha' => 75, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 0.5,
                'meses_almacigo' => '', 'meses_siembra' => '2,3,4,8,9', 'sensible_helada' => 0, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol o media sombra', 'distancia_cm' => 15, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Tomate', 'malos_vecinos' => 'Lechuga, Zanahoria',
                'consejos' => "Germina lento (hasta 4 semanas): dejá las semillas en remojo 24 horas antes de sembrar.\nMantené húmeda la superficie hasta que nazca.\nCortá los tallos de afuera para que siga produciendo.",
            ],
            [
                'nombre' => 'Frutilla', 'sinonimos' => 'fresa, frutillas',
                'familia' => 'Rosáceas', 'dias_cosecha' => 120, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1,
                'meses_almacigo' => '', 'meses_siembra' => '3,4,5', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Plantín', 'exposicion' => 'Sol pleno', 'distancia_cm' => 30, 'profundidad_cm' => 0,
                'buenos_vecinos' => 'Lechuga, Espinaca, Ajo, Cebolla', 'malos_vecinos' => 'Repollo, Brócoli',
                'consejos' => "Plantá con la corona (donde nacen las hojas) a ras del suelo, sin enterrarla.\nEs sensible a las sales: regá en profundidad y evitá suelos con costra blanca.\nCubrí el suelo con paja: conserva humedad y los frutos no tocan la tierra.",
            ],
            [
                'nombre' => 'Arveja', 'sinonimos' => 'guisante',
                'familia' => 'Fabáceas (legumbres)', 'dias_cosecha' => 110, 'frecuencia_riego_dias' => 3, 'horario_riego' => 'manana', 'litros_riego' => 1,
                'meses_almacigo' => '', 'meses_siembra' => '4,5,6', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 10, 'profundidad_cm' => 3,
                'buenos_vecinos' => 'Zanahoria, Rabanito, Lechuga, Maíz', 'malos_vecinos' => 'Cebolla, Ajo',
                'consejos' => "Se siembra en otoño y crece durante el invierno.\nNecesita un tutor o red para trepar.\nAporta nitrógeno al suelo: después de cosechar dejá las raíces enterradas.",
            ],
            [
                'nombre' => 'Poroto', 'sinonimos' => 'chaucha, frijol, judia',
                'familia' => 'Fabáceas (legumbres)', 'dias_cosecha' => 70, 'frecuencia_riego_dias' => 3, 'horario_riego' => 'manana', 'litros_riego' => 1,
                'meses_almacigo' => '', 'meses_siembra' => '10,11,12,1', 'sensible_helada' => 1, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 15, 'profundidad_cm' => 3,
                'buenos_vecinos' => 'Maíz, Zapallo, Zanahoria, Papa, Berenjena', 'malos_vecinos' => 'Cebolla, Ajo, Pimiento',
                'consejos' => "Es de las más sensibles a las sales: elegí el mejor suelo del huerto.\nLas variedades trepadoras necesitan tutor; el maíz puede servir de guía.\nCosechá las chauchas tiernas, antes de que se marquen los granos.",
            ],
            [
                'nombre' => 'Haba', 'sinonimos' => '',
                'familia' => 'Fabáceas (legumbres)', 'dias_cosecha' => 150, 'frecuencia_riego_dias' => 4, 'horario_riego' => 'manana', 'litros_riego' => 1,
                'meses_almacigo' => '', 'meses_siembra' => '3,4,5', 'sensible_helada' => 0, 'tolerancia_sal' => 'Baja',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 25, 'profundidad_cm' => 4,
                'buenos_vecinos' => 'Papa, Lechuga', 'malos_vecinos' => 'Cebolla, Ajo',
                'consejos' => "Resiste bien el frío del invierno.\nSi aparece pulgón negro en las puntas, cortalas.\nCosechá antes de que lleguen los calores fuertes de fin de primavera.",
            ],
            [
                'nombre' => 'Maíz', 'sinonimos' => 'choclo',
                'familia' => 'Poáceas', 'dias_cosecha' => 100, 'frecuencia_riego_dias' => 3, 'horario_riego' => 'tarde', 'litros_riego' => 2,
                'meses_almacigo' => '', 'meses_siembra' => '9,10,11,12', 'sensible_helada' => 1, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Directa', 'exposicion' => 'Sol pleno', 'distancia_cm' => 30, 'profundidad_cm' => 4,
                'buenos_vecinos' => 'Poroto, Zapallo, Zapallito, Pepino, Arveja, Melón, Sandía', 'malos_vecinos' => 'Tomate',
                'consejos' => "Sembralo en bloque (varias filas cortas), no en una sola fila: se poliniza con el viento.\nNecesita mucha agua cuando aparecen las barbas del choclo.\nPlantado en el borde del huerto sirve de cortina contra el viento.",
            ],
            [
                'nombre' => 'Repollo', 'sinonimos' => 'col',
                'familia' => 'Brasicáceas', 'dias_cosecha' => 100, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1.5,
                'meses_almacigo' => '1,2,7', 'meses_siembra' => '3,4,8,9', 'sensible_helada' => 0, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Almácigo y trasplante', 'exposicion' => 'Sol pleno', 'distancia_cm' => 50, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Cebolla, Papa, Acelga', 'malos_vecinos' => 'Frutilla, Tomate',
                'consejos' => "Revisá el envés de las hojas: la oruga de la mariposa blanca es su plaga típica.\nEl almácigo de verano necesita media sombra.\nNecesita suelo rico en materia orgánica.",
            ],
            [
                'nombre' => 'Brócoli', 'sinonimos' => 'brecol',
                'familia' => 'Brasicáceas', 'dias_cosecha' => 90, 'frecuencia_riego_dias' => 2, 'horario_riego' => 'manana', 'litros_riego' => 1.5,
                'meses_almacigo' => '1,2', 'meses_siembra' => '2,3,4', 'sensible_helada' => 0, 'tolerancia_sal' => 'Media',
                'tipo_siembra' => 'Almácigo y trasplante', 'exposicion' => 'Sol pleno', 'distancia_cm' => 50, 'profundidad_cm' => 1,
                'buenos_vecinos' => 'Cebolla, Papa, Lechuga', 'malos_vecinos' => 'Frutilla, Tomate',
                'consejos' => "El almácigo se hace en pleno verano: protegelo con media sombra.\nCosechá la cabeza antes de que se abran las flores amarillas.\nDespués del primer corte da brotes laterales más chicos.",
            ],
        ];

        // Vaciamos la tabla para que el seeder se pueda ejecutar varias veces sin duplicar
        $this->db->table('especies')->truncate();
        $this->db->table('especies')->insertBatch($especies);
    }
}
