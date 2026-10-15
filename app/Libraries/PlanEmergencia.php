<?php

namespace App\Libraries;

use App\Models\EmergenciaModel;
use App\Models\EspecieModel;
use App\Models\HuertoModel;
use App\Models\PlanoElementoModel;

// Planes de contingencia ante una emergencia climática: qué hacer ya, mientras dura y cómo reparar los daños
class PlanEmergencia
{
    // Especies que crecen altas o van con tutor: las primeras que vuelca el viento
    private const ALTAS = ['Tomate', 'Maíz', 'Poroto', 'Arveja'];

    // Días desde la siembra en que un cultivo todavía se considera plantín
    private const DIAS_PLANTIN = 21;

    // 'requiere' muestra el paso solo si el plano tiene ese elemento (tunel o media_sombra)
    public const TIPOS = [
        'zonda' => [
            'nombre'    => 'Viento Zonda',
            'boton'     => 'Viene Zonda',
            'icono'     => 'bi-wind',
            'resumen'   => 'Viento caliente y muy seco, con ráfagas fuertes y polvo. En pocas horas puede deshidratar y quebrar plantas.',
            'seguridad' => 'Con ráfagas fuertes no te quedes en la huerta: pueden volar chapas, ramas y polvo. Si tenés que salir, usá anteojos y barbijo.',
            'problema'  => 'zonda',
            'fases'     => [
                'antes'   => 'Ahora, antes de que llegue',
                'durante' => 'Mientras sopla',
            ],
            'pasos' => [
                'antes' => [
                    'z_regar'     => ['texto' => 'Si el suelo está seco, regá ahora y en profundidad', 'detalle' => 'Con el suelo húmedo las plantas aguantan mejor el aire seco. Si la tierra ya está húmeda, no hace falta.'],
                    'z_tutores'   => ['texto' => 'Atá las plantas altas a sus tutores', 'detalle' => 'Tomate, maíz, poroto y arveja: atalos en dos o tres puntos con tiras blandas, sin apretar el tallo.'],
                    'z_cobertura' => ['texto' => 'Cubrí el suelo con paja o rastrojo y sujetala', 'detalle' => 'Reduce la evaporación. Mojala o poné peso encima para que no se vuele.'],
                    'z_plantines' => ['texto' => 'Protegé plantines y almácigos', 'detalle' => 'Entralos a reparo o tapalos con cajones o tela bien sujeta.'],
                    'z_sueltos'   => ['texto' => 'Guardá o atá todo lo que se pueda volar', 'detalle' => 'Herramientas, baldes, bolsas, chapas y carteles.'],
                    'z_sombra'    => ['texto' => 'Revisá la media sombra', 'detalle' => 'Si está floja, reforzá las ataduras o enrollala: con viento fuerte se rasga y arrastra la estructura.', 'requiere' => 'media_sombra'],
                    'z_tunel'     => ['texto' => 'Cerrá bien el túnel', 'detalle' => 'Bajá y sujetá los laterales: si el viento entra, levanta el plástico.', 'requiere' => 'tunel'],
                ],
                'durante' => [
                    'z_resguardo' => ['texto' => 'Quedate a resguardo', 'detalle' => 'No hace falta estar en la huerta: lo importante ya está hecho.'],
                    'z_aspersion' => ['texto' => 'No riegues por aspersión mientras sopla', 'detalle' => 'El viento se lleva el agua antes de que llegue al suelo.'],
                    'z_podar'     => ['texto' => 'No cortes ni podes todavía', 'detalle' => 'Esperá a que pase para ver qué se dañó de verdad.'],
                ],
            ],
            'danos' => ['quebradas', 'hojas_quemadas', 'marchitas', 'cobertura', 'estructuras', 'riego', 'perdidas'],
        ],
        'granizo' => [
            'nombre'    => 'Granizo',
            'boton'     => 'Viene granizo',
            'icono'     => 'bi-cloud-hail',
            'resumen'   => 'Piedra que rompe hojas, golpea frutos y quiebra tallos en pocos minutos. Suele venir con tormenta y viento.',
            'seguridad' => 'No salgas a tapar plantas en plena tormenta: el granizo lastima y puede haber rayos. Hacé todo antes o esperá a que pase.',
            'problema'  => null,
            'fases'     => [
                'antes'   => 'Ahora, antes de que empiece',
                'durante' => 'Mientras cae',
            ],
            'pasos' => [
                'antes' => [
                    'g_cosechar'  => ['texto' => 'Cosechá lo que esté listo o casi listo', 'detalle' => 'Frutos y hojas que ya se pueden comer: mejor cosecharlos hoy que perderlos.'],
                    'g_cubrir'    => ['texto' => 'Cubrí los canteros con malla o lo que tengas', 'detalle' => 'Malla antigranizo, media sombra tensa, cajones o chapas sujetas. Dejala separada de las plantas con soportes: si se apoya, el peso del hielo las aplasta.'],
                    'g_plantines' => ['texto' => 'Entrá plantines y macetas bajo techo', 'detalle' => 'Son lo primero que se pierde con la piedra.'],
                    'g_guardar'   => ['texto' => 'Guardá herramientas y lo que se pueda romper', 'detalle' => 'También mangueras y goteros sueltos.'],
                    'g_desagues'  => ['texto' => 'Despejá desagües y acequias', 'detalle' => 'Si se tapan con hielo y hojas, el agua inunda los canteros.'],
                    'g_tunel'     => ['texto' => 'Cerrá el túnel y tené repuesto a mano', 'detalle' => 'Si el plástico es fino puede romperse: tené cinta o un plástico de repuesto.', 'requiere' => 'tunel'],
                ],
                'durante' => [
                    'g_techo'  => ['texto' => 'Quedate bajo techo', 'detalle' => 'No salgas a tapar plantas mientras cae: el granizo lastima.'],
                    'g_fotos'  => ['texto' => 'Cuando pare, sacá fotos antes de tocar nada', 'detalle' => 'Sirven para mostrarle el daño al técnico.'],
                ],
            ],
            'danos' => ['hojas_rotas', 'frutos_golpeados', 'quebradas', 'encharcado', 'estructuras', 'riego', 'perdidas'],
        ],
        'helada' => [
            'nombre'    => 'Helada',
            'boton'     => 'Viene helada',
            'icono'     => 'bi-snow',
            'resumen'   => 'Noche muy fría, en general con cielo despejado y sin viento. Quema hojas, brotes y frutos de los cultivos sensibles.',
            'seguridad' => 'Hacé todo antes de que oscurezca. De noche y a la madrugada la escarcha hace resbaladizos los caminos.',
            'problema'  => 'helada',
            'fases'     => [
                'antes'   => 'Esta tarde, antes de que baje el sol',
                'durante' => 'Durante la noche y a la mañana',
            ],
            'pasos' => [
                'antes' => [
                    'h_regar'     => ['texto' => 'Si el suelo está seco, regá a la tarde', 'detalle' => 'El suelo húmedo guarda más calor y lo va soltando durante la noche.'],
                    'h_tapar'     => ['texto' => 'Tapá los cultivos sensibles al atardecer', 'detalle' => 'Con manta antihelada, tela o cartón que llegue hasta el suelo. Que el plástico no toque las hojas.'],
                    'h_macetas'   => ['texto' => 'Entrá macetas y plantines a reparo', 'detalle' => 'Contra una pared o bajo techo, lejos del frío del suelo abierto.'],
                    'h_cosechar'  => ['texto' => 'Cosechá lo sensible que ya esté listo', 'detalle' => 'Tomates, zapallitos o porotos maduros no se recuperan si se hielan.'],
                    'h_tunel'     => ['texto' => 'Cerrá el túnel antes de que baje el sol', 'detalle' => 'Adentro también puede helar en noches muy frías: sumá manta sobre los cultivos sensibles.', 'requiere' => 'tunel'],
                ],
                'durante' => [
                    'h_destapar' => ['texto' => 'A la mañana destapá cuando suba la temperatura', 'detalle' => 'Si las dejás tapadas con sol fuerte se cocinan.'],
                    'h_esperar'  => ['texto' => 'No podes las plantas heladas enseguida', 'detalle' => 'Esperá 2 o 3 días para ver qué parte se secó de verdad.'],
                ],
            ],
            'danos' => ['helada_hojas', 'frutos_helados', 'estructuras', 'perdidas'],
        ],
    ];

    // Daños posibles y cómo repararlos
    public const DANOS = [
        'quebradas' => [
            'nombre'  => 'Plantas quebradas o volcadas',
            'icono'   => 'bi-slash-circle',
            'reparar' => [
                'Si el tallo está doblado pero no partido, enderezalo con cuidado y atalo a un tutor.',
                'Si se partió, cortá limpio por debajo de la rotura con una tijera limpia.',
                'Arrimá tierra al pie de las plantas volcadas y regá para que asienten las raíces.',
                'Si la planta se partió en la base, sacala: no se va a recuperar.',
            ],
        ],
        'hojas_quemadas' => [
            'nombre'  => 'Hojas secas o quemadas por el viento',
            'icono'   => 'bi-fire',
            'reparar' => [
                'Si el suelo quedó seco, regá al pie temprano o al atardecer.',
                'Esperá unos días antes de sacar hojas: muchas plantas rebrotan.',
                'Después cortá solo lo que esté completamente seco.',
            ],
        ],
        'marchitas' => [
            'nombre'  => 'Plantas marchitas',
            'icono'   => 'bi-droplet-half',
            'reparar' => [
                'Revisá la humedad metiendo un dedo en la tierra: si está seca, regá al pie.',
                'Si con el suelo húmedo no se recuperan en uno o dos días, revisá las raíces y avisale al técnico.',
            ],
        ],
        'cobertura' => [
            'nombre'  => 'Cobertura volada o suelo seco',
            'icono'   => 'bi-layers',
            'reparar' => [
                'Volvé a cubrir el suelo con paja o rastrojo y sujetalo.',
                'Si el suelo quedó seco, regá antes de cubrir.',
            ],
        ],
        'hojas_rotas' => [
            'nombre'  => 'Hojas rotas o agujereadas',
            'icono'   => 'bi-scissors',
            'reparar' => [
                'Sacá solo las hojas muy destrozadas: las que tienen agujeros chicos siguen sirviendo.',
                'Vigilá las heridas los próximos días: por ahí entran hongos.',
                'Regá al pie, sin mojar las hojas, hasta que cicatricen.',
            ],
        ],
        'frutos_golpeados' => [
            'nombre'  => 'Frutos golpeados',
            'icono'   => 'bi-basket',
            'reparar' => [
                'Cosechá y consumí pronto los que se puedan aprovechar: golpeados no se conservan.',
                'Sacá del huerto los frutos partidos o podridos para que no atraigan plagas ni hongos.',
            ],
        ],
        'encharcado' => [
            'nombre'  => 'Suelo encharcado o con costra',
            'icono'   => 'bi-water',
            'reparar' => [
                'Abrí los desagües y despejá las acequias para que salga el agua.',
                'Cuando el suelo se oree, rompé con cuidado la costra de la superficie alrededor de las plantas.',
            ],
        ],
        'helada_hojas' => [
            'nombre'  => 'Hojas o brotes helados',
            'icono'   => 'bi-snow2',
            'reparar' => [
                'Se reconocen porque se ponen oscuros y blandos. No los cortes enseguida.',
                'Esperá 2 o 3 días hasta que se vea bien qué parte se secó, y cortá solo eso.',
                'Seguí regando normal: no hace falta regar de más.',
            ],
        ],
        'frutos_helados' => [
            'nombre'  => 'Frutos helados',
            'icono'   => 'bi-thermometer-snow',
            'reparar' => [
                'Los frutos helados se ablandan y se pudren: cosechá los que se puedan aprovechar y usalos pronto.',
                'Sacá del huerto los que estén blandos o con manchas.',
            ],
        ],
        'estructuras' => [
            'nombre'  => 'Túnel, media sombra o tutores rotos',
            'icono'   => 'bi-tools',
            'reparar' => [
                'Retirá plásticos y alambres rotos para que no lastimen plantas ni personas.',
                'Reparalos con cinta o cambiá el paño antes de la próxima emergencia.',
                'Reemplazá los tutores quebrados y volvé a atar las plantas.',
            ],
        ],
        'riego' => [
            'nombre'  => 'Acequia, mangueras o goteros dañados',
            'icono'   => 'bi-moisture',
            'reparar' => [
                'Sacá ramas, tierra y hojas de la acequia antes del próximo turno de agua.',
                'Revisá mangueras y goteros: destapalos o cambiá los que estén rotos.',
            ],
        ],
        'perdidas' => [
            'nombre'  => 'Plantas que se perdieron',
            'icono'   => 'bi-x-octagon',
            'reparar' => [
                'Sacalas del cantero y anotá qué se perdió.',
                'Fijate qué se puede volver a sembrar en esta época antes de dejar el cantero vacío.',
            ],
        ],
    ];

    // Pasos del plan de un tipo, sin los que piden elementos que el huerto no tiene
    public static function pasos(string $tipo, array $elementosPlano): array
    {
        $tiene = [
            'tunel'        => in_array('tunel', array_column($elementosPlano, 'tipo'), true),
            'media_sombra' => (bool) array_filter(array_column($elementosPlano, 'media_sombra')),
        ];

        return array_map(
            fn ($pasos) => array_filter($pasos, fn ($p) => ! isset($p['requiere']) || $tiene[$p['requiere']]),
            self::TIPOS[$tipo]['pasos']
        );
    }

    // Pasos del plan según el plano actual del huerto
    public static function pasosDelHuerto(string $tipo): array
    {
        $huerto = (new HuertoModel())->actual();
        $elementos = $huerto['id'] ? (new PlanoElementoModel())->delHuerto($huerto['id']) : [];

        return self::pasos($tipo, $elementos);
    }

    // Pasos que se pueden marcar ahora: los del plan mientras está activa, los de reparación cuando terminó
    public static function clavesValidas(array $emergencia): array
    {
        $claves = [];
        if ($emergencia['estado'] === 'activa') {
            foreach (self::pasosDelHuerto($emergencia['tipo']) as $pasos) {
                $claves += $pasos;
            }
        } elseif ($emergencia['estado'] === 'terminada') {
            foreach (EmergenciaModel::lista($emergencia['danos']) as $dano) {
                if (isset(self::DANOS[$dano])) {
                    $claves += self::pasosReparacion($dano);
                }
            }
        }

        return $claves;
    }

    // Claves de los pasos de reparación de un daño: "r_<daño>_<n>"
    public static function pasosReparacion(string $dano): array
    {
        $claves = [];
        foreach (self::DANOS[$dano]['reparar'] as $i => $texto) {
            $claves['r_' . $dano . '_' . $i] = $texto;
        }

        return $claves;
    }

    // Cultivos en curso más expuestos a este tipo de emergencia, con el motivo
    public static function enRiesgo(string $tipo, array $cultivos): array
    {
        $especies = new EspecieModel();
        $hoy = new \DateTime('today');
        $riesgo = [];

        foreach ($cultivos as $cultivo) {
            if ($cultivo['estado'] === 'Cosechado') {
                continue;
            }

            $especie = $especies->identificar($cultivo['nombre_planta']);
            $dias = (int) (new \DateTime($cultivo['fecha_siembra']))->diff($hoy)->format('%r%a');
            $plantin = $dias >= 0 && $dias < self::DIAS_PLANTIN;
            $lista = $dias >= (int) $cultivo['dias_cosecha_estimados'] - 7;

            $motivo = match ($tipo) {
                'zonda' => $especie && in_array($especie['nombre'], self::ALTAS, true) ? 'Planta alta: atala bien al tutor'
                    : ($plantin ? 'Recién sembrado: protegelo del viento' : null),
                'granizo' => $lista ? 'Está para cosechar: cosechalo hoy si podés'
                    : ($plantin ? 'Plantín: tapalo o entralo' : null),
                'helada' => $especie && $especie['sensible_helada'] ? 'No soporta heladas: tapalo al atardecer'
                    : ($plantin ? 'Recién sembrado: conviene taparlo' : null),
                default => null,
            };

            if ($motivo) {
                $riesgo[] = ['cultivo' => $cultivo, 'motivo' => $motivo];
            }
        }

        return $riesgo;
    }
}
