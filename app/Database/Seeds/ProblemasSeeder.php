<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Carga el catálogo de plagas, enfermedades y daños que se pueden reportar.
 *
 * - afecta: nombres de especies o familias tal como figuran en el catálogo de especies; "*" = todas.
 * - meses_riesgo: meses de mayor riesgo en la zona de referencia; en las zonas más frías
 *   el sistema los corre según el desfase de la zona.
 * - sintomas, manejo y prevencion: una línea por ítem. El manejo prioriza prácticas agroecológicas.
 * - La clave no debe cambiarse: los reportes guardados la usan.
 *
 * Uso: php spark db:seed CatalogoSeeder
 */
class ProblemasSeeder extends Seeder
{
    public function run()
    {
        $problemas = [
            [
                'clave' => 'pulgon', 'nombre' => 'Pulgones', 'tipo' => 'plaga',
                'afecta' => 'Solanáceas, Brasicáceas, Asteráceas, Cucurbitáceas, Fabáceas (legumbres), Apiáceas, Amarantáceas',
                'meses_riesgo' => '8,9,10,11,3,4',
                'sintomas' => "Colonias de insectos chiquitos verdes, negros o grises en los brotes y debajo de las hojas.\nHojas enruladas, brotes deformados y pegajosos.\nHormigas que suben y bajan por la planta.",
                'manejo' => "Lavá las colonias con un chorro de agua por la mañana.\nPulverizá jabón blanco o potásico (10 g por litro de agua) mojando bien el envés de las hojas.\nCortá y sacá del huerto los brotes muy atacados.\nNo uses insecticidas de amplio espectro: matan a las vaquitas y crisopas que se comen a los pulgones.",
                'prevencion' => "No abones con estiércol fresco ni de más: los brotes tiernos atraen pulgones.\nDejá florecer algunas plantas (caléndula, cilantro, eneldo) para atraer insectos benéficos.",
            ],
            [
                'clave' => 'aranuela', 'nombre' => 'Arañuela roja', 'tipo' => 'plaga',
                'afecta' => 'Solanáceas, Cucurbitáceas, Fabáceas (legumbres), Frutilla',
                'meses_riesgo' => '11,12,1,2,3',
                'sintomas' => "Hojas con puntitos amarillentos que después se ponen color bronce y se secan.\nTelitas muy finas debajo de las hojas y en los brotes.\nAparece con calor seco y polvo.",
                'manejo' => "Mojá el envés de las hojas temprano a la mañana: la humedad la frena.\nPulverizá jabón blanco o potásico cada 5 a 7 días, 3 veces.\nSacá las hojas más secas y no las dejes en el huerto.\nNo apliques azufre en días de calor fuerte porque quema las hojas.",
                'prevencion' => "Cubrí el suelo con paja y regá parejo: la planta estresada por falta de agua se ataca más.\nEvitá el polvo en el huerto con cortinas o cercos.",
            ],
            [
                'clave' => 'mosca_blanca', 'nombre' => 'Mosca blanca', 'tipo' => 'plaga',
                'afecta' => 'Solanáceas, Cucurbitáceas, Fabáceas (legumbres)',
                'meses_riesgo' => '12,1,2,3,4',
                'sintomas' => "Nubecita de mosquitas blancas cuando movés la planta.\nHojas pegajosas y con un polvillo negro (fumagina).\nHojas amarillentas y planta débil.",
                'manejo' => "Colgá trampas amarillas con pegamento (o una botella amarilla untada con aceite) a la altura de las plantas.\nPulverizá jabón blanco o potásico sobre el envés de las hojas.\nSacá las hojas de abajo muy atacadas.",
                'prevencion' => "Revisá el envés de las hojas de los plantines antes de trasplantarlos.\nNo dejes restos de cultivos viejos al terminar el verano.",
            ],
            [
                'clave' => 'trips', 'nombre' => 'Trips', 'tipo' => 'plaga',
                'afecta' => 'Cebolla, Ajo, Solanáceas, Pepino, Poroto',
                'meses_riesgo' => '10,11,12,1,2',
                'sintomas' => "Manchas plateadas o blanquecinas en las hojas, con puntitos negros.\nEn cebolla y ajo, hojas blanquecinas y retorcidas.\nInsectos muy chicos y alargados, amarillos o marrones, que se esconden en los pliegues.",
                'manejo' => "Colgá trampas celestes o azules con pegamento.\nPulverizá jabón blanco o potásico al atardecer.\nUn riego por aspersión corto sobre las hojas de cebolla los desaloja.",
                'prevencion' => "Sacá las malezas de los bordes, donde se refugian.\nRotá: no repitas cebolla o ajo en el mismo lugar.",
            ],
            [
                'clave' => 'polilla_tomate', 'nombre' => 'Polilla del tomate', 'tipo' => 'plaga',
                'afecta' => 'Tomate, Papa, Berenjena',
                'meses_riesgo' => '11,12,1,2,3,4',
                'sintomas' => "Galerías transparentes (\"minas\") dentro de las hojas.\nFrutos con agujeritos y aserrín oscuro alrededor del cabito.\nBrotes de la punta comidos.",
                'manejo' => "Sacá las hojas y frutos dañados, embolsalos y tiralos fuera del huerto: no los dejes en el suelo ni en el compost.\nSi conseguís, usá trampas de feromona para capturar las polillas.\nAplicá Bacillus thuringiensis (Bt) sobre los brotes; es biológico y no afecta a otros insectos.",
                'prevencion' => "Al terminar la temporada, retirá todos los restos de tomate.\nArrancá las plantas de tomate o papa que nacen solas fuera de lugar.",
            ],
            [
                'clave' => 'isocas', 'nombre' => 'Orugas e isocas', 'tipo' => 'plaga',
                'afecta' => 'Maíz, Tomate, Brasicáceas, Asteráceas, Fabáceas (legumbres), Acelga',
                'meses_riesgo' => '11,12,1,2,3',
                'sintomas' => "Hojas comidas con agujeros grandes o comidas desde el borde.\nExcrementos verdes u oscuros sobre las hojas.\nOrugas a la vista; en el maíz, la punta del choclo comida.",
                'manejo' => "Juntá las orugas a mano al atardecer, cuando salen a comer.\nAplicá Bacillus thuringiensis (Bt) cuando las orugas todavía son chicas.\nEn repollo y brócoli, revisá el centro de la planta.",
                'prevencion' => "Asociá con plantas aromáticas (albahaca, romero, cebolla) que confunden a las mariposas.\nRevisá las plantas dos veces por semana en verano.",
            ],
            [
                'clave' => 'caracoles', 'nombre' => 'Caracoles y babosas', 'tipo' => 'animal',
                'afecta' => 'Asteráceas, Brasicáceas, Amarantáceas, Frutilla, Fabáceas (legumbres)',
                'meses_riesgo' => '3,4,5,8,9,10',
                'sintomas' => "Agujeros irregulares en las hojas, sobre todo en plantines.\nRastros brillantes de baba sobre las hojas y el suelo.\nEl daño aparece de un día para el otro, después de regar.",
                'manejo' => "Juntalos a mano al anochecer o temprano, después de regar.\nPoné trampas: tejas, tablas o cáscaras de naranja boca abajo, y revisalas a la mañana.\nHacé barreras de ceniza o cáscara de huevo molida alrededor de los plantines.",
                'prevencion' => "Regá a la mañana y no al atardecer.\nNo dejes maderas ni restos húmedos pegados a los canteros.",
            ],
            [
                'clave' => 'hormigas', 'nombre' => 'Hormiga cortadora', 'tipo' => 'animal',
                'afecta' => '*',
                'meses_riesgo' => '9,10,11,12,1,2,3',
                'sintomas' => "Hojas cortadas en forma de medialuna.\nPlantines pelados de la noche a la mañana.\nCaminos de hormigas llevando pedacitos de hojas.",
                'manejo' => "Seguí el camino de noche hasta encontrar el hormiguero.\nPoné barreras en el tallo: un cuello de botella con grasa o un anillo de cinta pegajosa.\nAvisá al técnico para tratar el hormiguero sin contaminar el huerto.",
                'prevencion' => "Revisá el huerto de noche en primavera, cuando más cortan.\nProtegé los plantines recién trasplantados con botellas cortadas.",
            ],
            [
                'clave' => 'oidio', 'nombre' => 'Oídio (polvillo blanco)', 'tipo' => 'enfermedad',
                'afecta' => 'Cucurbitáceas, Arveja, Frutilla, Pimiento',
                'meses_riesgo' => '9,10,11,12,1,2,3',
                'sintomas' => "Manchas de polvillo blanco, como harina, sobre las hojas.\nLas hojas se ponen amarillas y se secan desde abajo.\nAparece aunque el clima sea seco.",
                'manejo' => "Sacá las hojas más afectadas y no las pongas en el compost.\nPulverizá leche diluida (1 parte de leche en 9 de agua) cada 7 días.\nEl azufre también sirve, pero no lo apliques en días de calor fuerte.",
                'prevencion' => "Dejá espacio entre plantas para que circule el aire.\nRegá al pie y no mojes las hojas al atardecer.",
            ],
            [
                'clave' => 'manchas_hongos', 'nombre' => 'Tizón y mildiu (manchas por hongos)', 'tipo' => 'enfermedad',
                'afecta' => 'Tomate, Papa, Cebolla, Cucurbitáceas, Acelga',
                'meses_riesgo' => '1,2,3,4',
                'sintomas' => "Manchas marrones o negras en las hojas, a veces con anillos o borde amarillo.\nPelusa gris o violácea debajo de las hojas cuando hay humedad.\nAparece después de lluvias de verano o de regar mojando las hojas.",
                'manejo' => "Sacá enseguida las hojas con manchas y sacalas del huerto.\nAplicá caldo bordelés (cobre), que se permite en huertas agroecológicas.\nDejá de mojar las hojas: regá al pie.",
                'prevencion' => "Rotá los cultivos: no repitas tomate o papa en el mismo lugar.\nAtá las plantas para que las hojas no toquen el suelo.",
            ],
            [
                'clave' => 'podredumbre_apical', 'nombre' => 'Podredumbre apical (culo negro)', 'tipo' => 'fisiopatia',
                'afecta' => 'Tomate, Pimiento, Sandía, Zapallito, Berenjena',
                'meses_riesgo' => '11,12,1,2',
                'sintomas' => "Mancha negra o marrón, hundida y seca, en la punta del fruto.\nAparece en frutos todavía verdes.\nNo hay insectos ni pelusa: no es una plaga.",
                'manejo' => "Sacá los frutos manchados: no se recuperan.\nRegá parejo, sin dejar que el suelo se seque del todo entre riegos.\nSi regás por turno, usá el agua guardada entre turnos para no cortar el riego.",
                'prevencion' => "Cubrí el suelo con paja para que mantenga la humedad.\nAgregá compost al suelo antes de plantar.",
            ],
            [
                'clave' => 'golpe_sol', 'nombre' => 'Golpe de sol y calor', 'tipo' => 'clima',
                'afecta' => 'Tomate, Pimiento, Berenjena, Lechuga, Sandía, Melón, Zapallo, Espinaca',
                'meses_riesgo' => '12,1,2',
                'sintomas' => "Manchas blanquecinas o amarillas, secas, del lado del fruto que da al sol.\nHojas quemadas en los bordes.\nLechuga y espinaca que florecen antes de tiempo y se ponen amargas.",
                'manejo' => "Colocá malla media sombra (35 a 50 %) en las horas de más sol.\nNo saques hojas a las plantas: protegen a los frutos.\nRegá temprano a la mañana.",
                'prevencion' => "Sembrá las hortalizas de hoja en otoño e invierno, o a media sombra en verano.\nCubrí el suelo para bajar su temperatura.",
            ],
            [
                'clave' => 'helada', 'nombre' => 'Daño por helada', 'tipo' => 'clima',
                'afecta' => 'Solanáceas, Cucurbitáceas, Albahaca, Poroto, Maíz',
                'meses_riesgo' => '5,6,7,8,9',
                'sintomas' => "Hojas oscuras, como cocidas, que al día siguiente se secan.\nEl daño es mayor en la parte de arriba de la planta y en los lugares bajos del terreno.\nAparece después de una noche despejada y fría.",
                'manejo' => "No podes enseguida: esperá unos días para ver qué partes rebrotan.\nSi anuncian otra helada, cubrí las plantas de noche con tela, agrotextil o botellas cortadas.\nRegá el día anterior a la helada: el suelo húmedo guarda más calor.",
                'prevencion' => "Respetá las fechas de siembra de la guía para tu zona.\nPlantá las especies sensibles cerca de paredes que dan al norte.",
            ],
            [
                'clave' => 'zonda', 'nombre' => 'Daño por viento Zonda', 'tipo' => 'clima',
                'afecta' => '*',
                'meses_riesgo' => '7,8,9,10,11',
                'sintomas' => "Hojas secas y quemadas de golpe después de un día de viento caliente y seco.\nTallos quebrados y plantas volcadas.\nPlantines deshidratados aunque el suelo esté húmedo.",
                'manejo' => "Regá apenas calma el viento para recuperar las plantas.\nAtá los tallos quebrados con un tutor: muchos se recuperan.\nSacá las hojas totalmente secas recién cuando rebroten las nuevas.",
                'prevencion' => "Armá cortavientos con cañas, malla o cercos vivos del lado del viento.\nPoné tutores firmes a tomates, pimientos y porotos.",
            ],
            [
                'clave' => 'sales', 'nombre' => 'Sales en el suelo o el agua', 'tipo' => 'fisiopatia',
                'afecta' => '*',
                'meses_riesgo' => '12,1,2',
                'sintomas' => "Bordes de las hojas quemados, empezando por las hojas de abajo.\nCostra blanca sobre el suelo o en el borde de los surcos.\nPlantas que no crecen aunque se rieguen.",
                'manejo' => "Hacé de vez en cuando un riego abundante para lavar las sales hacia abajo, si el suelo drena bien.\nAgregá compost o materia orgánica.\nConsultá al técnico para analizar el suelo o el agua.",
                'prevencion' => "Cubrí el suelo para que no se evapore el agua y suban las sales.\nElegí especies tolerantes a la sal (la guía de cultivo lo indica).",
            ],
        ];

        // Vaciamos la tabla para que el seeder se pueda ejecutar varias veces sin duplicar
        $this->db->table('problemas')->truncate();
        $this->db->table('problemas')->insertBatch($problemas);
    }
}
