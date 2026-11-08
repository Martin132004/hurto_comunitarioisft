<?php
    // Datos del huerto para la barra lateral
    $huertoActual = (new \App\Models\HuertoModel())->actual();
    $zonaActual = (new \App\Models\ZonaModel())->porClave($huertoActual['zona']);

    $fechaLarga = \App\Libraries\Fechas::larga(new \DateTime());

    // Emergencia climática en curso: se avisa en todas las pantallas
    $emergenciaActiva = (new \App\Models\EmergenciaModel())->activa();
    $emergenciaTipo = $emergenciaActiva ? (\App\Libraries\PlanEmergencia::TIPOS[$emergenciaActiva['tipo']] ?? null) : null;

    // Menú lateral: [ruta, patrón para marcar activo, ícono, texto]
    $menu = [
        'Huerto' => [
            ['/', '/', 'bi-grid-1x2', 'Panel'],
            ['huerto/plano', 'huerto/plano', 'bi-map', 'Plano del huerto'],
            ['huerto/crear', 'huerto/crear', 'bi-plus-square', 'Nuevo cultivo'],
            ['huerto/problemas', 'huerto/problemas*', 'bi-bug', 'Problemas y plagas'],
            ['huerto/emergencia', 'huerto/emergencia*', 'bi-exclamation-octagon', 'Emergencia climática'],
        ],
        'Gestión' => [
            ['huerto/reportes', 'huerto/reportes*', 'bi-file-earmark-bar-graph', 'Reportes'],
            ['huerto/configuracion', 'huerto/configuracion', 'bi-sliders', 'Mi huerto'],
        ],
    ];

    $paginaActual = 'Panel';
    foreach ($menu as $items) {
        foreach ($items as $item) {
            if (url_is($item[1])) {
                $paginaActual = $item[3];
            }
        }
    }
    if (url_is('huerto/regando*')) { $paginaActual = 'Riego'; }
    if (url_is('huerto/cosechar*') || url_is('huerto/estado*')) { $paginaActual = 'Cosecha'; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($paginaActual) ?> · AgroTech</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('img/agrotech-icono.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/agrotech-tokens.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/emergencia.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/agrotech.css') ?>" rel="stylesheet">
    <?= $this->renderSection('estilos') ?>
</head>
<body>

<a href="#contenido" class="at-saltar visually-hidden-focusable">Saltar al contenido</a>

<?php if (session()->getFlashdata('bienvenida')): ?>
    <div class="bienvenida" id="bienvenida" data-cubierta>
        <img src="<?= base_url('img/agrotech-marca-blanca.png') ?>" alt="" class="marca">
        <p class="saludo">Hola, <?= esc(session('usuario_nombre')) ?></p>
        <p class="detalle">Preparando <?= esc($huertoActual['nombre']) ?></p>
        <div class="progreso">
            <div class="progreso-cabecera">
                <span class="paso" id="bienvenidaPaso" role="status">Iniciando sesión…</span>
                <span class="porcentaje" id="bienvenidaPorcentaje" aria-hidden="true">0 %</span>
            </div>
            <div class="carga" role="progressbar" aria-label="Cargando el sistema" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="bienvenidaBarra"><span></span></div>
        </div>
    </div>
<?php endif; ?>

<div class="at-app">

    <aside class="at-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="menuPrincipal" aria-label="Menú principal">
        <div class="at-brand">
            <a href="<?= base_url('/') ?>" aria-label="AgroTech, ir al panel">
                <img src="<?= base_url('img/agrotech-marca.png') ?>" alt="" class="marca">
                <img src="<?= base_url('img/agrotech-texto.png') ?>" alt="AgroTech" class="texto">
            </a>
            <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#menuPrincipal" aria-label="Cerrar menú"></button>
        </div>

        <div class="offcanvas-body">
            <nav class="at-nav">
                <?php foreach ($menu as $grupo => $items): ?>
                    <div class="at-nav-grupo"><?= $grupo ?></div>
                    <?php foreach ($items as [$ruta, $patron, $icono, $texto]): ?>
                        <a href="<?= base_url($ruta) ?>" class="at-nav-link <?= url_is($patron) ? 'active' : '' ?>" <?= url_is($patron) ? 'aria-current="page"' : '' ?>>
                            <i class="bi <?= $icono ?>"></i> <?= $texto ?>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </nav>

            <a href="<?= base_url('huerto/configuracion') ?>" class="at-huerto-card">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="at-estado-punto"></span>
                    <span class="nombre"><?= esc($huertoActual['nombre']) ?></span>
                </div>
                <div class="zona"><i class="bi bi-geo-alt me-1"></i><?= $zonaActual ? esc($zonaActual['nombre']) : 'Elegí la zona del huerto' ?></div>
            </a>

            <?php if (session('usuario_id')): ?>
                <div class="at-usuario">
                    <span class="at-avatar" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr(session('usuario_nombre'), 0, 1))) ?></span>
                    <div style="min-width: 0">
                        <div class="nombre"><?= esc(session('usuario_nombre')) ?></div>
                        <div class="rol">Sesión iniciada</div>
                    </div>
                    <form action="<?= base_url('salir') ?>" method="POST" class="ms-auto">
                        <button type="submit" class="at-salir" aria-label="Cerrar sesión" title="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </aside>

    <div class="at-main">
        <header class="at-topbar" id="barraSuperior">
            <button class="at-menu-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-label="Abrir menú">
                <i class="bi bi-list"></i>
            </button>
            <img src="<?= base_url('img/agrotech-marca.png') ?>" alt="" class="at-logo-movil d-lg-none">
            <div class="at-migas">
                <span class="d-none d-sm-inline">AgroTech</span>
                <i class="bi bi-chevron-right small d-none d-sm-inline"></i>
                <strong><?= esc($paginaActual) ?></strong>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="at-fecha d-none d-md-inline-flex"><i class="bi bi-calendar3"></i><?= ucfirst($fechaLarga) ?></span>
                <?php if ($emergenciaTipo): ?>
                    <a href="<?= base_url('huerto/emergencia/' . $emergenciaActiva['id']) ?>" class="em-boton-top activa" aria-label="Emergencia en curso: <?= esc($emergenciaTipo['nombre'], 'attr') ?>">
                        <span class="em-punto" aria-hidden="true"></span><span class="d-none d-sm-inline"><?= esc($emergenciaTipo['nombre']) ?></span>
                    </a>
                <?php elseif (! url_is('huerto/emergencia*')): ?>
                    <a href="<?= base_url('huerto/emergencia') ?>" class="em-boton-top" aria-label="Alerta de clima: Zonda, granizo o helada">
                        <i class="bi bi-exclamation-triangle-fill"></i><span class="d-none d-sm-inline">Alerta de clima</span>
                    </a>
                <?php endif; ?>
                <?php if (! url_is('huerto/crear')): ?>
                    <a href="<?= base_url('huerto/crear') ?>" class="btn btn-huerto btn-sm px-3 py-2" aria-label="Nuevo cultivo"><i class="bi bi-plus-lg"></i><span class="d-none d-sm-inline ms-1">Nuevo cultivo</span></a>
                <?php endif; ?>
            </div>
        </header>

        <main class="at-contenido" id="contenido" tabindex="-1">
            <?php if ($emergenciaTipo && ! url_is('huerto/emergencia*')): ?>
                <?php
                    // Mismo criterio que el plan: solo cuentan los pasos que el plan muestra hoy
                    $pasosValidos = array_keys(\App\Libraries\PlanEmergencia::clavesValidas($emergenciaActiva));
                    $pasosHechos = count(array_intersect(\App\Models\EmergenciaModel::lista($emergenciaActiva['pasos']), $pasosValidos));
                ?>
                <div class="em-franja" role="alert">
                    <span class="em-punto" aria-hidden="true"></span>
                    <div class="texto">
                        <strong><i class="bi <?= $emergenciaTipo['icono'] ?> me-1" aria-hidden="true"></i>Emergencia en curso: <?= esc($emergenciaTipo['nombre']) ?></strong>
                        <div><?= $pasosHechos ? $pasosHechos . ' paso' . ($pasosHechos > 1 ? 's' : '') . ' del plan hecho' . ($pasosHechos > 1 ? 's' : '') . '.' : 'Todavía no marcaste ningún paso del plan.' ?></div>
                    </div>
                    <a href="<?= base_url('huerto/emergencia/' . $emergenciaActiva['id']) ?>" class="btn btn-light btn-sm">Ver el plan</a>
                    <a href="<?= base_url('huerto/emergencia/' . $emergenciaActiva['id'] . '/terminar') ?>" class="btn btn-outline-light btn-sm">Ya terminó</a>
                </div>
            <?php endif; ?>
            <?= $this->renderSection('contenido') ?>
        </main>

        <footer class="at-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span class="d-flex align-items-center gap-2">
                <img src="<?= base_url('img/agrotech-texto.png') ?>" alt="AgroTech">
                <span>Gestión de huertas</span>
            </span>
            <span>&copy; <?= date('Y') ?> · Trabajo Práctico PP III · rodriguez &amp; carrizo</span>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        const reducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Los íconos de Bootstrap Icons acompañan texto: los lectores de pantalla los saltean
        document.querySelectorAll('i.bi:not([aria-label]):not([role])').forEach(i => i.setAttribute('aria-hidden', 'true'));

        // Bienvenida: la barra avanza por etapas, como una carga real, y al completarse se va.
        // Se puede saltear con un clic o Escape.
        const bienvenida = document.getElementById('bienvenida');
        if (bienvenida) {
            const barra = document.getElementById('bienvenidaBarra');
            const relleno = barra.querySelector('span');
            const textoPaso = document.getElementById('bienvenidaPaso');
            const textoPorcentaje = document.getElementById('bienvenidaPorcentaje');

            // [milisegundos, porcentaje]: tramos rápidos y pausas cortas
            const tramos = [[0, 0], [600, 12], [1100, 19], [1900, 41], [2400, 47], [3200, 68], [3700, 74], [4400, 92], [4900, 100]];
            const pasos = [
                [0,   'Iniciando sesión…'],
                [15,  'Cargando cultivos…'],
                [42,  'Calculando el riego del mes…'],
                [70,  'Revisando turnos de agua…'],
                [90,  'Buscando alertas y plagas…'],
                [100, 'Listo'],
            ];
            const duracion = tramos[tramos.length - 1][0];
            const suave = t => 1 - Math.pow(1 - t, 2);
            let inicio = null;
            let terminado = false;

            const porcentajeEn = ms => {
                for (let i = 1; i < tramos.length; i++) {
                    const [t0, p0] = tramos[i - 1];
                    const [t1, p1] = tramos[i];
                    if (ms <= t1) return p0 + (p1 - p0) * suave((ms - t0) / (t1 - t0));
                }
                return 100;
            };

            const quitar = () => {
                if (terminado) return;
                terminado = true;
                bienvenida.classList.add('saliendo');
                document.dispatchEvent(new Event('cubierta:sale'));
            };
            bienvenida.addEventListener('animationend', e => { if (e.animationName === 'bienvenidaSale') bienvenida.remove(); });
            bienvenida.addEventListener('click', quitar);
            document.addEventListener('keydown', e => { if (e.key === 'Escape') quitar(); });

            const avanzar = ahora => {
                if (terminado) return;
                if (inicio === null) inicio = ahora;
                const ms = ahora - inicio;
                const valor = Math.min(100, Math.round(porcentajeEn(ms)));

                relleno.style.transform = `scaleX(${valor / 100})`;
                textoPorcentaje.textContent = valor + ' %';
                barra.setAttribute('aria-valuenow', valor);
                const paso = pasos.filter(([desde]) => valor >= desde).pop()[1];
                if (textoPaso.textContent !== paso) textoPaso.textContent = paso;

                if (ms < duracion) {
                    requestAnimationFrame(avanzar);
                } else {
                    bienvenida.classList.add('completa');
                    setTimeout(quitar, 600);
                }
            };
            // Empieza cuando ya apareció el logo
            setTimeout(() => requestAnimationFrame(avanzar), 500);
        }

        // Borde de la barra superior al hacer scroll
        const barra = document.getElementById('barraSuperior');
        const marcarBarra = () => barra.classList.toggle('con-sombra', window.scrollY > 4);
        window.addEventListener('scroll', marcarBarra, { passive: true });
        marcarBarra();

        // Los números de las tarjetas cuentan desde cero al cargar ("12,5 kg", "Cada 3", "80 %").
        // Si una pantalla cubre la página (bienvenida, riego), esperan a que se vaya.
        const formato = (valor, decimales) => valor.toLocaleString('es-AR', { minimumFractionDigits: decimales, maximumFractionDigits: decimales });
        const contarNumeros = () => document.querySelectorAll('.stat-valor, .plan-dato .valor, [data-contar]').forEach(el => {
            if (reducido || el.children.length) return;
            const partes = el.textContent.trim().match(/^(\D*?)(\d[\d.]*(?:,\d+)?)(.*)$/);
            if (!partes) return;
            const [, antes, numero, despues] = partes;
            const decimales = numero.includes(',') ? numero.split(',')[1].length : 0;
            const final = parseFloat(numero.replace(/\./g, '').replace(',', '.'));
            if (!final) return;
            const inicio = performance.now();
            const duracion = 900;
            const paso = (ahora) => {
                const t = Math.min(1, (ahora - inicio) / duracion);
                const suave = 1 - Math.pow(1 - t, 3);
                el.textContent = antes + formato(final * suave, decimales) + despues;
                if (t < 1) requestAnimationFrame(paso);
            };
            requestAnimationFrame(paso);
        });
        if (document.querySelector('[data-cubierta]')) {
            document.addEventListener('cubierta:sale', contarNumeros, { once: true });
        } else {
            contarNumeros();
        }
    })();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
