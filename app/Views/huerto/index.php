<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    /* Resumen de riego y problemas */
    .resumen {
        background: var(--superficie);
        border: 1px solid var(--borde);
        border-radius: calc(var(--radio) + 2px);
        box-shadow: var(--sombra-1);
        padding: 1.25rem 1.35rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .resumen-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .9rem;
    }
    .resumen-cabecera h2 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: .55rem;
    }
    .resumen-cabecera h2 i {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .95rem;
    }
    .resumen-cifra {
        font-family: 'Outfit', sans-serif;
        font-size: 2rem;
        font-weight: 600;
        line-height: 1;
        letter-spacing: -.03em;
    }
    .resumen-cifra small { font-size: .9rem; font-weight: 500; color: var(--texto-2); letter-spacing: 0; }
    .resumen-lista { list-style: none; padding: 0; margin: .9rem 0 0; font-size: .85rem; color: var(--texto-2); }
    .resumen-lista li { display: flex; gap: .55rem; padding: .45rem 0; border-top: 1px dashed var(--borde); }
    .resumen-lista li:first-child { border-top: 0; }
    .resumen-lista li i { flex-shrink: 0; margin-top: .05rem; }
    .chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .75rem;
        font-weight: 500;
        padding: .22rem .6rem;
        border-radius: 6px;
        background: var(--fondo);
        border: 1px solid var(--borde);
        color: var(--texto-2);
        white-space: nowrap;
    }
    .resumen .acciones { margin-top: auto; padding-top: 1rem; }

    /* Barra de filtros */
    .filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.1rem;
    }
    .filtro-grupo {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 2px;
        padding: 3px;
        background: #ebede6;
        border-radius: 11px;
    }
    .filtro-btn {
        border: 0;
        background: transparent;
        color: var(--texto-2);
        font-size: .82rem;
        font-weight: 500;
        padding: .4rem .75rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        transition: background .2s, color .2s, box-shadow .2s;
    }
    .filtro-btn:hover { color: var(--texto); }
    .filtro-btn.activo {
        background: var(--superficie);
        color: var(--texto);
        box-shadow: 0 1px 3px rgba(20, 33, 26, .12);
    }
    .filtro-btn .cuenta {
        font-size: .7rem;
        font-weight: 600;
        min-width: 1.3rem;
        padding: .05rem .35rem;
        border-radius: 5px;
        background: rgba(20, 33, 26, .07);
        font-variant-numeric: tabular-nums;
    }
    .buscador { position: relative; margin-left: auto; min-width: 220px; }
    .buscador i { position: absolute; left: .8rem; top: 50%; transform: translateY(-50%); color: var(--texto-3); pointer-events: none; }
    .buscador .form-control { padding-left: 2.2rem; padding-top: .5rem; padding-bottom: .5rem; font-size: .875rem; }
    @media (max-width: 767.98px) { .buscador { margin-left: 0; width: 100%; } }

    /* Tarjeta de cultivo */
    .cultivo {
        position: relative;
        background: var(--superficie);
        border: 1px solid var(--borde);
        border-radius: calc(var(--radio) + 2px);
        box-shadow: var(--sombra-1);
        padding: 1.15rem 1.15rem 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform .3s var(--ease), box-shadow .3s, border-color .3s;
    }
    .cultivo:hover {
        transform: translateY(-3px);
        box-shadow: var(--sombra-2);
        border-color: #d3d9cd;
    }
    .cultivo::before {
        content: "";
        position: absolute;
        left: 1.15rem;
        right: 1.15rem;
        top: -1px;
        height: 3px;
        border-radius: 0 0 3px 3px;
        background: var(--tono, transparent);
    }
    .cultivo[data-estado="riego"] { --tono: var(--agua-600); }
    .cultivo[data-estado="cosecha"] { --tono: var(--cosecha-600); }
    .cultivo[data-estado="cosechado"] { background: #fbfbf9; }
    .cultivo[data-estado="cosechado"] .cultivo-avatar { filter: grayscale(.6); opacity: .8; }

    .cultivo-cabecera { display: flex; align-items: flex-start; gap: .8rem; }
    .cultivo-avatar {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        background: var(--tinte, var(--at-50));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
        transition: transform .4s var(--ease);
    }
    .cultivo:hover .cultivo-avatar { transform: rotate(-6deg) scale(1.06); }
    .cultivo-nombre {
        font-size: 1.08rem;
        font-weight: 600;
        margin: 0;
        text-transform: capitalize;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cultivo-sub { font-size: .8rem; color: var(--texto-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cultivo-menu {
        border: 0;
        background: transparent;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        color: var(--texto-3);
        margin: -.2rem -.3rem 0 auto;
        flex-shrink: 0;
        transition: background .2s, color .2s;
    }
    .cultivo-menu:hover, .cultivo-menu[aria-expanded="true"] { background: var(--fondo); color: var(--texto); }

    .estado {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        align-self: flex-start;
        font-size: .76rem;
        font-weight: 600;
        padding: .25rem .6rem .25rem .5rem;
        border-radius: 6px;
        margin-top: .9rem;
    }
    .estado .punto { width: 7px; height: 7px; border-radius: 50%; background: currentColor; position: relative; }
    .estado-riego { background: var(--agua-100); color: var(--agua-600); }
    .estado-cosecha { background: var(--cosecha-100); color: var(--cosecha-600); }
    .estado-crecimiento { background: var(--at-50); color: var(--at-700); }
    .estado-cosechado { background: #eef0eb; color: var(--texto-2); }
    .estado-riego .punto::after, .estado-cosecha .punto::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: currentColor;
        animation: latido 1.8s ease-out infinite;
    }
    @keyframes latido {
        from { transform: scale(1); opacity: .6; }
        to   { transform: scale(3); opacity: 0; }
    }

    .ciclo { margin-top: 1rem; }
    .ciclo-linea { display: flex; justify-content: space-between; font-size: .78rem; color: var(--texto-2); margin-bottom: .4rem; }
    .ciclo-linea strong { color: var(--texto); font-weight: 600; font-variant-numeric: tabular-nums; }
    .ciclo-barra {
        height: 6px;
        border-radius: 6px;
        background: #edf0e9;
        overflow: hidden;
    }
    .ciclo-barra span {
        display: block;
        height: 100%;
        width: var(--p);
        border-radius: inherit;
        background: linear-gradient(90deg, var(--at-500), var(--at-300));
        transform-origin: left;
        animation: crece 1.2s var(--ease) both .35s;
    }
    .cultivo[data-estado="cosecha"] .ciclo-barra span { background: linear-gradient(90deg, #e8890c, #f2b13c); }
    .cultivo[data-estado="cosechado"] .ciclo-barra span { background: #b9c2b5; }
    @keyframes crece {
        from { transform: scaleX(0); }
        to   { transform: scaleX(1); }
    }
    .ciclo-nota { font-size: .76rem; color: var(--texto-3); margin-top: .4rem; }

    .cultivo-datos {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        margin: 1rem 0 0;
        border: 1px solid var(--borde-suave);
        border-radius: var(--radio-sm);
        background: #fafbf8;
    }
    .cultivo-datos > div { padding: .55rem .65rem; min-width: 0; }
    .cultivo-datos > div + div { border-left: 1px solid var(--borde-suave); }
    .cultivo-datos dt { font-size: .68rem; font-weight: 500; color: var(--texto-3); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .1rem; }
    .cultivo-datos dd { margin: 0; font-size: .86rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cultivo-datos dd.urgente { color: var(--agua-600); }

    .cultivo-avisos { display: flex; flex-direction: column; gap: .35rem; margin-top: .75rem; font-size: .78rem; }
    .cultivo-avisos a, .cultivo-avisos span { display: inline-flex; align-items: center; gap: .4rem; text-decoration: none; }

    .cultivo-acciones { display: flex; gap: .5rem; margin-top: auto; padding-top: 1rem; }
    .cultivo-acciones .btn { flex: 1; font-size: .85rem; padding: .5rem .75rem; }
    .btn-agua {
        background: var(--agua-600);
        border: 1px solid #125a93;
        color: #fff;
        box-shadow: 0 6px 16px -8px rgba(23, 105, 170, .8);
    }
    .btn-agua:hover { background: #125a93; color: #fff; }
    .btn-agua i { display: inline-block; }
    .btn-agua:hover i { animation: gota .7s var(--ease); }
    @keyframes gota {
        0% { transform: translateY(0); }
        40% { transform: translateY(3px) scaleY(1.15); }
        100% { transform: translateY(0); }
    }
    .btn-cosecha {
        background: var(--cosecha-100);
        border: 1px solid #f3d3ad;
        color: var(--cosecha-600);
    }
    .btn-cosecha:hover { background: #fbe2c6; color: #9a4c08; border-color: #ecc292; }

    /* Filtrado animado */
    .cultivo-col { transition: opacity .25s, transform .25s var(--ease); }
    .cultivo-col.saliendo { opacity: 0; transform: scale(.97); }
    .sin-resultados { display: none; }
    .sin-resultados.visible { display: block; animation: subeAparece .4s var(--ease) both; }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $litros = fn ($valor) => number_format((float) $valor, 1, ',', '.');
    $hoyFecha = new \DateTime('today');

    // Estado de cada cultivo, en orden de prioridad
    $estadoDe = function (array $c): string {
        if ($c['estado'] == 'Cosechado') { return 'cosechado'; }
        if ($c['alerta_cosecha']) { return 'cosecha'; }
        if ($c['alerta_riego']) { return 'riego'; }
        return 'crecimiento';
    };

    $cuentas = ['todos' => 0, 'riego' => 0, 'cosecha' => 0, 'crecimiento' => 0, 'cosechado' => 0];
    foreach ($cultivos ?? [] as $c) {
        $cuentas['todos']++;
        $cuentas[$estadoDe($c)]++;
    }

    // Lo urgente primero: regar, cosechar, en crecimiento y al final lo cosechado
    $orden = ['riego' => 0, 'cosecha' => 1, 'crecimiento' => 2, 'cosechado' => 3];
    usort($cultivos, fn ($a, $b) => $orden[$estadoDe($a)] <=> $orden[$estadoDe($b)]);

    $estados = [
        'riego'       => ['texto' => 'Regar hoy', 'clase' => 'estado-riego'],
        'cosecha'     => ['texto' => 'Lista para cosechar', 'clase' => 'estado-cosecha'],
        'crecimiento' => ['texto' => 'En crecimiento', 'clase' => 'estado-crecimiento'],
        'cosechado'   => ['texto' => 'Cosechado', 'clase' => 'estado-cosechado'],
    ];

    // Ícono y color de fondo según la especie (por el comienzo del nombre, así sirven los plurales)
    $tintes = ['rojo' => '#fdeceb', 'naranja' => '#fff0df', 'verde' => '#eaf5e0', 'violeta' => '#f1eafa', 'amarillo' => '#fdf5d8', 'marron' => '#f3ece2'];
    $iconos = [
        'tomate' => ['🍅', 'rojo'], 'frutilla' => ['🍓', 'rojo'], 'sandia' => ['🍉', 'rojo'], 'pimiento' => ['🫑', 'verde'],
        'morron' => ['🫑', 'rojo'], 'aji' => ['🌶️', 'rojo'], 'zanahoria' => ['🥕', 'naranja'], 'zapallito' => ['🥒', 'verde'],
        'zapallo' => ['🎃', 'naranja'], 'calabaza' => ['🎃', 'naranja'], 'melon' => ['🍈', 'amarillo'], 'pepino' => ['🥒', 'verde'],
        'berenjena' => ['🍆', 'violeta'], 'cebolla' => ['🧅', 'marron'], 'ajo' => ['🧄', 'marron'], 'papa' => ['🥔', 'marron'],
        'batata' => ['🍠', 'marron'], 'lechuga' => ['🥬', 'verde'], 'acelga' => ['🥬', 'verde'], 'espinaca' => ['🥬', 'verde'],
        'repollo' => ['🥬', 'verde'], 'brocoli' => ['🥦', 'verde'], 'coliflor' => ['🥦', 'verde'], 'albahaca' => ['🌿', 'verde'],
        'perejil' => ['🌿', 'verde'], 'oregano' => ['🌿', 'verde'], 'arveja' => ['🫛', 'verde'], 'haba' => ['🫛', 'verde'],
        'chaucha' => ['🫛', 'verde'], 'poroto' => ['🫘', 'marron'], 'maiz' => ['🌽', 'amarillo'], 'choclo' => ['🌽', 'amarillo'],
        'rabanito' => ['🌱', 'rojo'], 'remolacha' => ['🌱', 'violeta'], 'girasol' => ['🌻', 'amarillo'], 'uva' => ['🍇', 'violeta'],
    ];
    $iconoDe = function (string $nombre) use ($iconos, $tintes): array {
        $clave = strtr(mb_strtolower(trim($nombre)), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        foreach ($iconos as $prefijo => [$icono, $tinte]) {
            if (str_starts_with($clave, $prefijo)) {
                return [$icono, $tintes[$tinte]];
            }
        }
        return ['🌱', $tintes['verde']];
    };

    $hora = (int) date('G');
    $saludo = $hora < 13 ? 'Buen día' : ($hora < 20 ? 'Buenas tardes' : 'Buenas noches');
?>

<section class="huerto-hero mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="<?= base_url('huerto/configuracion') ?>" class="badge-fecha d-inline-flex align-items-center gap-2 text-decoration-none">
                    <i class="bi bi-geo-alt"></i> <?= $zona ? esc($zona['nombre']) : 'Elegí la zona de tu huerto' ?>
                </a>
                <?php if ($riego['usa_turno']): ?>
                    <span class="badge-fecha d-inline-flex align-items-center gap-2">
                        <i class="bi bi-water"></i>
                        <?php if ($hoyHayTurno): ?>
                            Hoy hay turno de agua
                        <?php elseif (! empty($riego['proximos_turnos'])): ?>
                            Próximo turno: <?= $riego['proximos_turnos'][0]->format('d/m') ?>
                        <?php else: ?>
                            Turno de agua sin fechas
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="mb-2"><?= $saludo ?>. Así está <?= esc($huerto['nombre']) ?> hoy.</h1>
            <p class="mb-0" style="color: rgba(255,255,255,.72)">
                <?php
                    $tareas = array_filter([
                        $cuentas['riego'] ? $cuentas['riego'] . ' cultivo' . ($cuentas['riego'] > 1 ? 's' : '') . ' para regar' : '',
                        $cuentas['cosecha'] ? $cuentas['cosecha'] . ' listo' . ($cuentas['cosecha'] > 1 ? 's' : '') . ' para cosechar' : '',
                    ]);
                ?>
                <?= $tareas ? 'Tenés ' . implode(' y ', $tareas) . '.' : 'No hay tareas pendientes: todos los cultivos están al día.' ?>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="<?= base_url('huerto/crear') ?>" class="btn btn-huerto">
                <i class="bi bi-plus-lg me-1"></i> Nuevo cultivo
            </a>
        </div>
    </div>
</section>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card" data-filtro="todos" role="button" tabindex="0">
            <div class="stat-icon" style="background: var(--at-50); color: var(--at-600)"><i class="bi bi-flower2"></i></div>
            <div>
                <div class="stat-valor"><?= $cuentas['todos'] ?></div>
                <div class="stat-label">Cultivos registrados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" data-filtro="riego" role="button" tabindex="0">
            <div class="stat-icon" style="background: var(--agua-100); color: var(--agua-600)"><i class="bi bi-droplet-half"></i></div>
            <div>
                <div class="stat-valor"><?= $cuentas['riego'] ?></div>
                <div class="stat-label">Requieren riego</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" data-filtro="cosecha" role="button" tabindex="0">
            <div class="stat-icon" style="background: var(--cosecha-100); color: var(--cosecha-600)"><i class="bi bi-basket2"></i></div>
            <div>
                <div class="stat-valor"><?= $cuentas['cosecha'] ?></div>
                <div class="stat-label">Listos para cosechar</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" data-filtro="cosechado" role="button" tabindex="0">
            <div class="stat-icon" style="background: #eef0eb; color: var(--texto-2)"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-valor"><?= $cuentas['cosechado'] ?></div>
                <div class="stat-label">Cosechados</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="<?= $problemas ? 'col-lg-7' : 'col-12' ?>">
        <div class="resumen">
            <div class="resumen-cabecera">
                <h2><i class="bi bi-droplet-half" style="background: var(--agua-100); color: var(--agua-600)"></i>Riego de este mes</h2>
                <span class="chip"><i class="bi bi-moisture"></i><?= esc($riego['metodo']['nombre']) ?></span>
            </div>
            <div class="resumen-cifra"><span data-contar><?= $litros($riego['litros_semana']) ?></span> <small>litros por semana</small></div>
            <ul class="resumen-lista">
                <?php if ($riego['usa_turno']): ?>
                    <?php if ($hoyHayTurno): ?>
                        <li class="text-success fw-semibold"><i class="bi bi-water"></i>Hoy hay turno de agua.</li>
                    <?php elseif (! empty($riego['proximos_turnos'])): ?>
                        <li><i class="bi bi-calendar-check"></i>Próximo turno de agua: <?= $riego['proximos_turnos'][0]->format('d/m') ?>.</li>
                    <?php endif; ?>
                    <?php if ($riego['agua_entre_turnos'] > 0): ?>
                        <?php if ($riego['reservorio_alcanza']): ?>
                            <li><i class="bi bi-check-circle text-success"></i>Entre turnos usás unos <?= $litros($riego['agua_entre_turnos']) ?> L guardados (tenés <?= $riego['reservorio'] ?> L).</li>
                        <?php else: ?>
                            <li class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle"></i>Entre turnos necesitás unos <?= $litros($riego['agua_entre_turnos']) ?> L guardados y tenés <?= $riego['reservorio'] ?> L.</li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <li><i class="bi bi-info-circle"></i>Los litros de cada cultivo ya están ajustados al mes y a tu método de riego.</li>
                <?php endif; ?>
            </ul>
            <div class="acciones">
                <a href="<?= base_url('huerto/configuracion') ?>" class="btn btn-light btn-sm px-3"><i class="bi bi-sliders me-1"></i>Configurar riego</a>
            </div>
        </div>
    </div>

    <?php if ($problemas): ?>
        <div class="col-lg-5">
            <div class="resumen">
                <div class="resumen-cabecera">
                    <h2><i class="bi bi-bug" style="background: var(--alerta-100); color: var(--alerta-600)"></i>Problemas y plagas</h2>
                    <?php if ($problemas['brotes']): ?>
                        <span class="chip" style="background: var(--alerta-100); border-color: #f2c9c3; color: var(--alerta-600)"><i class="bi bi-exclamation-octagon"></i>Alerta en tu zona</span>
                    <?php endif; ?>
                </div>
                <div class="resumen-cifra"><?= $problemas['sin_resolver'] ?> <small>sin resolver</small></div>
                <ul class="resumen-lista">
                    <?php if ($problemas['brotes']): ?>
                        <li class="text-danger"><i class="bi bi-exclamation-octagon-fill"></i><span>Varias huertas de la zona reportaron <?= esc(mb_strtolower(implode(', ', $problemas['brotes']))) ?>.</span></li>
                    <?php endif; ?>
                    <?php if ($problemas['respondidos']): ?>
                        <li class="text-primary"><i class="bi bi-chat-left-text"></i>El técnico respondió <?= $problemas['respondidos'] ?> reporte(s).</li>
                    <?php endif; ?>
                    <?php if ($problemas['repetidos']): ?>
                        <li class="text-danger"><i class="bi bi-arrows-angle-expand"></i><?= esc(implode(', ', $problemas['repetidos'])) ?> en varios cultivos.</li>
                    <?php elseif (! $problemas['respondidos'] && ! $problemas['brotes']): ?>
                        <li><i class="bi bi-camera"></i>Si ves hojas comidas, manchas o bichos, reportalo con una foto.</li>
                    <?php endif; ?>
                </ul>
                <div class="acciones">
                    <a href="<?= base_url('huerto/problemas') ?>" class="btn btn-light btn-sm px-3">Ver problemas<i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="cultivos" class="d-flex align-items-end justify-content-between mb-3" style="scroll-margin-top: 80px">
    <div>
        <h2 class="seccion-titulo h5 mb-1">Mis cultivos</h2>
        <p class="text-muted small mb-0">Ordenados por lo que necesita atención primero.</p>
    </div>
</div>

<?php if (empty($cultivos)): ?>
    <div class="estado-vacio">
        <i class="bi bi-flower1"></i>
        <h5 class="fw-semibold mt-3">Todavía no hay cultivos en tu huerto</h5>
        <p class="text-muted mb-4">Agregá tu primera planta y el sistema te avisa cuándo regarla y cuándo cosecharla.</p>
        <a href="<?= base_url('huerto/crear') ?>" class="btn btn-huerto">
            <i class="bi bi-plus-lg me-1"></i> Agregar cultivo
        </a>
    </div>
<?php else: ?>

    <div class="filtros">
        <div class="filtro-grupo" role="tablist" aria-label="Filtrar cultivos">
            <?php foreach (['todos' => 'Todos', 'riego' => 'Para regar', 'cosecha' => 'Para cosechar', 'crecimiento' => 'En crecimiento', 'cosechado' => 'Cosechados'] as $clave => $texto): ?>
                <button type="button" class="filtro-btn <?= $clave === 'todos' ? 'activo' : '' ?>" data-filtro="<?= $clave ?>" role="tab" aria-selected="<?= $clave === 'todos' ? 'true' : 'false' ?>">
                    <?= $texto ?> <span class="cuenta"><?= $cuentas[$clave] ?></span>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="buscador">
            <i class="bi bi-search"></i>
            <input type="search" id="buscarCultivo" class="form-control" placeholder="Buscar cultivo o variedad" aria-label="Buscar cultivo">
        </div>
    </div>

    <div class="row g-3" id="grillaCultivos">
        <?php foreach ($cultivos as $planta): ?>
            <?php
                $estado = $estadoDe($planta);
                [$icono, $tinte] = $iconoDe($planta['nombre_planta']);
                $cosechado = $estado === 'cosechado';
                $diasCosecha = (int) $hoyFecha->diff($planta['fecha_cosecha'])->days;

                if ($cosechado) {
                    $proximo = '—';
                } elseif ($planta['dias_proximo_riego'] === 0) {
                    $proximo = 'Hoy';
                } elseif ($planta['dias_proximo_riego'] === 1) {
                    $proximo = 'Mañana';
                } else {
                    $proximo = 'En ' . $planta['dias_proximo_riego'] . ' días';
                }
            ?>
            <div class="col-sm-6 col-xl-4 cultivo-col" data-estado="<?= $estado ?>" data-texto="<?= esc(mb_strtolower($planta['nombre_planta'] . ' ' . $planta['variedad'])) ?>">
                <article class="cultivo" data-estado="<?= $estado ?>" style="--tinte: <?= $tinte ?>">
                    <div class="cultivo-cabecera">
                        <div class="cultivo-avatar" aria-hidden="true"><?= $icono ?></div>
                        <div class="min-w-0 flex-grow-1" style="min-width: 0">
                            <h3 class="cultivo-nombre"><?= esc($planta['nombre_planta']) ?></h3>
                            <div class="cultivo-sub">
                                <?= $planta['variedad'] ? esc($planta['variedad']) . ' · ' : '' ?>Sembrado el <?= date('d/m/Y', strtotime($planta['fecha_siembra'])) ?>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="cultivo-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Más acciones">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (! $cosechado): ?>
                                    <li><a class="dropdown-item" href="<?= base_url('huerto/cosechar/' . $planta['id']) ?>"><i class="bi bi-basket2"></i>Registrar cosecha</a></li>
                                    <?php if ($problemas !== null): ?>
                                        <li><a class="dropdown-item" href="<?= base_url('huerto/problemas/nuevo?cultivo=' . $planta['id']) ?>"><i class="bi bi-bug"></i>Reportar un problema</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= base_url('huerto/eliminar/' . $planta['id']) ?>" onclick="return confirm('¿Seguro que querés eliminar este cultivo?')">
                                        <i class="bi bi-trash3"></i>Eliminar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <span class="estado <?= $estados[$estado]['clase'] ?>"><span class="punto"></span><?= $estados[$estado]['texto'] ?></span>

                    <div class="ciclo">
                        <div class="ciclo-linea">
                            <span>Día <strong><?= min($planta['dias_desde_siembra'], 999) ?></strong> de <?= (int) $planta['dias_cosecha_estimados'] ?></span>
                            <strong><?= $cosechado ? 100 : $planta['progreso'] ?>%</strong>
                        </div>
                        <div class="ciclo-barra" role="progressbar" aria-valuenow="<?= $planta['progreso'] ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Avance del ciclo">
                            <span style="--p: <?= $cosechado ? 100 : max(2, $planta['progreso']) ?>%"></span>
                        </div>
                        <div class="ciclo-nota">
                            <?php if ($cosechado): ?>
                                <i class="bi bi-check2 me-1"></i>Ciclo terminado<?= isset($kgCosechados[$planta['id']]) ? ' · ' . number_format($kgCosechados[$planta['id']], 2, ',', '.') . ' kg cosechados' : '' ?>
                            <?php elseif ($planta['alerta_cosecha']): ?>
                                <i class="bi bi-basket2 me-1"></i>Lista desde el <?= $planta['fecha_cosecha']->format('d/m') ?>
                            <?php else: ?>
                                <i class="bi bi-calendar-event me-1"></i>Cosecha estimada el <?= $planta['fecha_cosecha']->format('d/m') ?> · faltan <?= $diasCosecha ?> día<?= $diasCosecha == 1 ? '' : 's' ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (! $cosechado): ?>
                        <dl class="cultivo-datos">
                            <div>
                                <dt>Próx. riego</dt>
                                <dd class="<?= $planta['dias_proximo_riego'] === 0 ? 'urgente' : '' ?>"><?= $proximo ?></dd>
                            </div>
                            <div>
                                <dt>Por riego</dt>
                                <dd><?= $litros($planta['litros_hoy']) ?> L</dd>
                            </div>
                            <div>
                                <dt>Cada</dt>
                                <dd title="<?= esc(\App\Models\CultivoModel::HORARIOS_RIEGO[$planta['horario_riego']]['nombre'] ?? 'Mañana') ?>"><?= (int) $planta['frecuencia_riego_dias'] ?> día<?= $planta['frecuencia_riego_dias'] == 1 ? '' : 's' ?> · <?= esc(mb_strtolower(\App\Models\CultivoModel::HORARIOS_RIEGO[$planta['horario_riego']]['nombre'] ?? 'mañana')) ?></dd>
                            </div>
                        </dl>
                    <?php endif; ?>

                    <?php $conTurno = $planta['alerta_riego'] && ! $cosechado && $riego['usa_turno'] && ! $hoyHayTurno; ?>
                    <?php if ($conTurno || ! empty($problemas['por_cultivo'][$planta['id']]) || (! $cosechado && isset($kgCosechados[$planta['id']]))): ?>
                        <div class="cultivo-avisos">
                            <?php if ($conTurno): ?>
                                <span class="text-primary"><i class="bi bi-info-circle"></i>Hoy no hay turno: regá con agua guardada.</span>
                            <?php endif; ?>
                            <?php if (! $cosechado && isset($kgCosechados[$planta['id']])): ?>
                                <span class="text-muted"><i class="bi bi-basket2"></i>Lleva <?= number_format($kgCosechados[$planta['id']], 2, ',', '.') ?> kg cosechados</span>
                            <?php endif; ?>
                            <?php if (! empty($problemas['por_cultivo'][$planta['id']])): ?>
                                <a href="<?= base_url('huerto/problemas') ?>" class="text-danger fw-semibold"><i class="bi bi-bug-fill"></i><?= $problemas['por_cultivo'][$planta['id']] ?> problema(s) sin resolver</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (! $cosechado): ?>
                        <div class="cultivo-acciones">
                            <a href="<?= base_url('huerto/riego/' . $planta['id']) ?>" class="btn <?= $planta['alerta_riego'] ? 'btn-agua' : 'btn-light' ?>"><i class="bi bi-droplet-fill me-1"></i>Regar</a>
                            <?php if ($planta['alerta_cosecha'] || isset($kgCosechados[$planta['id']])): ?>
                                <a href="<?= base_url('huerto/cosechar/' . $planta['id']) ?>" class="btn btn-cosecha"><i class="bi bi-basket2 me-1"></i>Cosechar</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="estado-vacio sin-resultados mt-1" id="sinResultados">
        <i class="bi bi-search"></i>
        <h6 class="fw-semibold mt-3 mb-1">No hay cultivos que coincidan</h6>
        <p class="text-muted small mb-0">Probá con otro filtro u otra búsqueda.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        const grilla = document.getElementById('grillaCultivos');
        if (!grilla) return;

        const columnas = [...grilla.querySelectorAll('.cultivo-col')];
        const botones = [...document.querySelectorAll('.filtro-btn')];
        const buscador = document.getElementById('buscarCultivo');
        const sinResultados = document.getElementById('sinResultados');
        let filtro = 'todos';

        const aplicar = () => {
            const texto = buscador.value.trim().toLowerCase();
            let visibles = 0;
            columnas.forEach(col => {
                const pasa = (filtro === 'todos' || col.dataset.estado === filtro) && (!texto || col.dataset.texto.includes(texto));
                if (pasa) {
                    visibles++;
                    if (col.hidden) {
                        col.hidden = false;
                        col.classList.add('saliendo');
                        requestAnimationFrame(() => requestAnimationFrame(() => col.classList.remove('saliendo')));
                    }
                } else if (!col.hidden) {
                    col.classList.add('saliendo');
                    setTimeout(() => { if (col.classList.contains('saliendo')) col.hidden = true; }, 200);
                }
            });
            sinResultados.classList.toggle('visible', visibles === 0);
        };

        const elegir = (clave) => {
            filtro = clave;
            botones.forEach(b => {
                const activo = b.dataset.filtro === clave;
                b.classList.toggle('activo', activo);
                b.setAttribute('aria-selected', activo);
            });
            aplicar();
        };

        botones.forEach(b => b.addEventListener('click', () => elegir(b.dataset.filtro)));
        buscador.addEventListener('input', aplicar);

        // Las tarjetas de resumen de arriba filtran la grilla
        document.querySelectorAll('.stat-card[data-filtro]').forEach(card => {
            const ir = () => {
                elegir(card.dataset.filtro);
                document.getElementById('cultivos').scrollIntoView({ behavior: 'smooth' });
            };
            card.addEventListener('click', ir);
            card.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); ir(); } });
        });
    })();
</script>
<?= $this->endSection() ?>
