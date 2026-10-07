<?php
    // Datos del huerto para la barra lateral (funciona aunque no estén corridas las migraciones)
    $huertoActual = (new \App\Models\HuertoModel())->actual();
    $zonaActual = null;
    if (! empty($huertoActual['zona']) && db_connect()->tableExists('zonas')) {
        $zonaActual = (new \App\Models\ZonaModel())->where('clave', $huertoActual['zona'])->first();
    }

    $diasSemana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $mesesAnio = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $fechaLarga = $diasSemana[(int) date('w')] . ' ' . date('j') . ' de ' . $mesesAnio[(int) date('n') - 1];

    // Menú lateral: [ruta, patrón para marcar activo, ícono, texto]
    $menu = [
        'Huerto' => [
            ['/', '/', 'bi-grid-1x2', 'Panel'],
            ['huerto/plano', 'huerto/plano', 'bi-box', 'Huerto 3D'],
            ['huerto/crear', 'huerto/crear', 'bi-plus-square', 'Nuevo cultivo'],
            ['huerto/problemas', 'huerto/problemas*', 'bi-bug', 'Problemas y plagas'],
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
    <style>
        :root {
            /* Colores de la marca (tomados del logo) */
            --at-900: #0a3a1f;
            --at-800: #0f4d29;
            --at-700: #17663a;
            --at-600: #1f7d43;
            --at-500: #39a33f;
            --at-300: #8cc63f;
            --at-100: #e8f4dc;
            --at-50:  #f3f9ec;

            --agua-600: #1769aa;
            --agua-100: #e3f0fb;
            --cosecha-600: #c2610c;
            --cosecha-100: #fdf0e1;
            --alerta-600: #c0392b;
            --alerta-100: #fbe9e7;

            --fondo: #f5f6f1;
            --superficie: #ffffff;
            --borde: #e2e6dd;
            --borde-suave: #edf0e9;
            --texto: #14211a;
            --texto-2: #5d6b62;
            --texto-3: #8b978f;

            --radio: 14px;
            --radio-sm: 10px;
            --sombra-1: 0 1px 2px rgba(20, 33, 26, .05), 0 1px 1px rgba(20, 33, 26, .03);
            --sombra-2: 0 10px 30px -14px rgba(20, 33, 26, .22), 0 2px 6px -2px rgba(20, 33, 26, .06);
            --ease: cubic-bezier(.22, .9, .3, 1);
            --ancho-lateral: 264px;

            /* Nombres que usan los estilos propios de cada vista */
            --huerto-verde: var(--at-600);
            --huerto-verde-oscuro: var(--at-900);
            --huerto-lima: var(--at-300);
            --huerto-crema: var(--fondo);

            /* Bootstrap con los colores del sistema */
            --bs-body-font-family: 'Geist', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --bs-body-color: var(--texto);
            --bs-body-bg: var(--fondo);
            --bs-border-color: var(--borde);
            --bs-secondary-color: var(--texto-2);
            --bs-success: #1f7d43;
            --bs-success-rgb: 31, 125, 67;
            --bs-primary: #1769aa;
            --bs-primary-rgb: 23, 105, 170;
            --bs-link-color: var(--at-700);
            --bs-link-color-rgb: 23, 102, 58;
            --bs-link-hover-color: var(--at-900);
            --bs-link-hover-color-rgb: 10, 58, 31;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--bs-body-font-family);
            background: var(--fondo);
            color: var(--texto);
            font-size: .9375rem;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        h1, h2, h3, h4, h5, h6, .display-6 {
            font-family: 'Outfit', var(--bs-body-font-family);
            letter-spacing: -.015em;
        }

        .text-muted { color: var(--texto-2) !important; }
        .text-success { color: var(--at-600) !important; }
        ::selection { background: var(--at-100); color: var(--at-900); }

        /* ---------- Estructura: barra lateral + contenido ---------- */
        .at-app { display: flex; min-height: 100vh; }

        .at-sidebar {
            width: var(--ancho-lateral);
            background: var(--superficie) !important;
            border-right: 1px solid var(--borde);
            display: flex;
            flex-direction: column;
        }
        @media (min-width: 992px) {
            .at-sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                flex-shrink: 0;
            }
        }
        .at-sidebar .offcanvas-body {
            display: flex;
            flex-direction: column;
            padding: 0;
            flex: 1;
            overflow-y: auto;
        }

        .at-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.25rem 1rem;
        }
        .at-brand a {
            display: flex;
            align-items: center;
            gap: .55rem;
            text-decoration: none;
        }
        .at-brand .marca {
            height: 40px;
            width: auto;
            transition: transform .5s var(--ease);
        }
        .at-brand a:hover .marca { transform: rotate(-8deg) scale(1.05); }
        .at-brand .texto { height: 22px; width: auto; }

        .at-nav { padding: .5rem .75rem; }
        .at-nav-grupo {
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--texto-3);
            padding: 1rem .75rem .45rem;
        }
        .at-nav-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .58rem .75rem;
            margin-bottom: 2px;
            border-radius: var(--radio-sm);
            color: var(--texto-2);
            font-weight: 500;
            text-decoration: none;
            transition: background .2s, color .2s;
        }
        .at-nav-link i {
            font-size: 1.05rem;
            width: 1.2rem;
            text-align: center;
            transition: transform .25s var(--ease);
        }
        .at-nav-link:hover { background: var(--fondo); color: var(--texto); }
        .at-nav-link:hover i { transform: translateX(2px); }
        .at-nav-link.active {
            background: var(--at-50);
            color: var(--at-800);
            font-weight: 600;
        }
        .at-nav-link.active i { color: var(--at-600); }
        .at-nav-link.active::before {
            content: "";
            position: absolute;
            left: -.75rem;
            top: 22%;
            bottom: 22%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--at-500);
            animation: barraEntra .4s var(--ease) both;
        }
        @keyframes barraEntra {
            from { transform: scaleY(0); }
            to   { transform: scaleY(1); }
        }

        .at-huerto-card {
            margin: auto .75rem .75rem;
            padding: .9rem 1rem;
            border-radius: var(--radio);
            background: var(--fondo);
            border: 1px solid var(--borde-suave);
            text-decoration: none;
            color: inherit;
            display: block;
            transition: border-color .2s, background .2s;
        }
        .at-huerto-card:hover { border-color: var(--at-300); background: var(--at-50); }
        .at-huerto-card .nombre {
            font-weight: 600;
            font-size: .875rem;
            color: var(--texto);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .at-huerto-card .zona { font-size: .78rem; color: var(--texto-2); }
        .at-estado-punto {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--at-500);
            box-shadow: 0 0 0 3px rgba(57, 163, 63, .18);
            flex-shrink: 0;
        }

        .at-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .at-topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            height: 64px;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 0 1.75rem;
            background: rgba(245, 246, 241, .82);
            backdrop-filter: saturate(1.4) blur(12px);
            -webkit-backdrop-filter: saturate(1.4) blur(12px);
            border-bottom: 1px solid transparent;
            transition: border-color .2s, background .2s;
        }
        .at-topbar.con-sombra { border-bottom-color: var(--borde); background: rgba(255, 255, 255, .88); }
        .at-migas {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .875rem;
            color: var(--texto-3);
            min-width: 0;
        }
        .at-migas strong { color: var(--texto); font-weight: 600; white-space: nowrap; }
        .at-fecha {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            font-size: .82rem;
            color: var(--texto-2);
            padding: .38rem .75rem;
            border-radius: var(--radio-sm);
            background: var(--superficie);
            border: 1px solid var(--borde);
        }
        .at-menu-btn {
            border: 1px solid var(--borde);
            background: var(--superficie);
            border-radius: var(--radio-sm);
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--texto);
        }
        .at-logo-movil { height: 28px; }

        .at-contenido {
            flex: 1;
            width: 100%;
            max-width: 1320px;
            margin: 0 auto;
            padding: 1.5rem 1.75rem 3rem;
        }
        @media (max-width: 575.98px) {
            .at-topbar { padding: 0 1rem; }
            .at-contenido { padding: 1rem 1rem 2.5rem; }
        }

        .at-footer {
            border-top: 1px solid var(--borde);
            padding: 1.1rem 1.75rem;
            font-size: .8rem;
            color: var(--texto-3);
        }
        .at-footer img { height: 18px; opacity: .85; }

        /* ---------- Encabezado de página ---------- */
        .huerto-hero {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            border-radius: calc(var(--radio) + 4px);
            padding: 2rem 2.25rem;
            color: #fff;
            background-color: var(--at-900);
            background-image:
                radial-gradient(120% 140% at 100% 0%, rgba(140, 198, 63, .28) 0%, transparent 55%),
                linear-gradient(160deg, var(--at-800) 0%, var(--at-900) 70%);
        }
        /* Trama de puntos tipo placa de circuito, como en el logo */
        .huerto-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background-image: radial-gradient(rgba(255, 255, 255, .13) 1px, transparent 1.2px);
            background-size: 18px 18px;
            -webkit-mask-image: linear-gradient(100deg, transparent 35%, #000 85%);
            mask-image: linear-gradient(100deg, transparent 35%, #000 85%);
        }
        .huerto-hero::after {
            content: "";
            position: absolute;
            z-index: -1;
            right: 2%;
            top: 50%;
            width: 210px;
            height: 270px;
            background: url('<?= base_url('img/agrotech-marca-blanca.png') ?>') center / contain no-repeat;
            opacity: .08;
            transform: translateY(-50%) rotate(8deg);
            animation: hojaFlota 9s ease-in-out infinite alternate;
        }
        @keyframes hojaFlota {
            from { transform: translateY(-52%) rotate(6deg); }
            to   { transform: translateY(-46%) rotate(11deg); }
        }
        .huerto-hero h1 {
            font-weight: 600;
            letter-spacing: -.025em;
            font-size: clamp(1.6rem, 2.6vw, 2.15rem);
        }
        .huerto-hero .badge-fecha {
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            padding: .3rem .7rem;
            font-size: .78rem;
            font-weight: 500;
            color: rgba(255, 255, 255, .9);
            transition: background .2s;
        }
        .huerto-hero a.badge-fecha:hover { background: rgba(255, 255, 255, .18); }
        .huerto-hero .btn-light {
            background: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .2);
            color: #fff;
        }
        .huerto-hero .btn-light:hover { background: rgba(255, 255, 255, .2); color: #fff; }

        /* Encabezado dentro de un panel: más sobrio, claro */
        .panel-form > .huerto-hero {
            background: var(--superficie);
            color: var(--texto);
            border-bottom: 1px solid var(--borde-suave);
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        .panel-form > .huerto-hero::before {
            background: linear-gradient(90deg, var(--at-500), var(--at-300));
            height: 3px;
            inset: 0 0 auto 0;
            -webkit-mask-image: none;
            mask-image: none;
            background-size: auto;
            transform-origin: left;
            animation: lineaEntra .9s var(--ease) both .15s;
        }
        @keyframes lineaEntra {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }
        .panel-form > .huerto-hero::after { display: none; }
        .panel-form > .huerto-hero h4 {
            display: flex;
            align-items: center;
            gap: .1rem;
            font-weight: 600 !important;
            font-size: 1.3rem;
            color: var(--texto);
        }
        .panel-form > .huerto-hero h4 > i {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--at-50);
            color: var(--at-600);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-right: .7rem !important;
        }
        .panel-form > .huerto-hero p { color: var(--texto-2); opacity: 1 !important; }

        /* ---------- Botones ---------- */
        .btn {
            font-weight: 500;
            border-radius: var(--radio-sm);
            transition: background .2s, border-color .2s, color .2s, transform .15s var(--ease), box-shadow .2s;
        }
        .btn.rounded-pill { border-radius: var(--radio-sm) !important; }
        .btn:not(.btn-sm):not(.btn-lg):not(.rounded-circle) { padding-top: .58rem; padding-bottom: .58rem; }
        .btn:active { transform: scale(.97); }
        .btn-huerto {
            background: var(--at-600);
            color: #fff;
            font-weight: 600;
            border: 1px solid var(--at-700);
            padding: .6rem 1.2rem;
            box-shadow: 0 1px 0 rgba(255, 255, 255, .15) inset, 0 6px 16px -8px rgba(31, 125, 67, .7);
        }
        .btn-huerto:hover, .btn-huerto:focus-visible {
            background: var(--at-700);
            border-color: var(--at-800);
            color: #fff;
            box-shadow: 0 1px 0 rgba(255, 255, 255, .15) inset, 0 10px 22px -10px rgba(31, 125, 67, .9);
        }
        .huerto-hero:not(.rounded-0) .btn-huerto {
            background: var(--at-300);
            border-color: var(--at-300);
            color: var(--at-900);
            box-shadow: 0 10px 24px -12px rgba(140, 198, 63, .9);
        }
        .huerto-hero:not(.rounded-0) .btn-huerto:hover { background: #9fd456; border-color: #9fd456; }
        .btn-light {
            background: var(--superficie);
            border: 1px solid var(--borde);
            color: var(--texto);
        }
        .btn-light:hover { background: var(--fondo); border-color: #d3d9cd; }
        .btn-outline-success { --bs-btn-color: var(--at-700); --bs-btn-border-color: var(--borde); --bs-btn-hover-bg: var(--at-600); --bs-btn-hover-border-color: var(--at-600); --bs-btn-active-bg: var(--at-600); --bs-btn-active-border-color: var(--at-600); }

        /* ---------- Tarjetas y paneles ---------- */
        .stat-card {
            position: relative;
            background: var(--superficie);
            border-radius: var(--radio);
            padding: 1.1rem 1.2rem;
            border: 1px solid var(--borde);
            box-shadow: var(--sombra-1);
            display: flex;
            align-items: center;
            gap: .95rem;
            transition: transform .25s var(--ease), box-shadow .25s, border-color .25s;
        }
        /* Misma altura solo entre tarjetas de una fila (fuera de una columna, el 100% estira la tarjeta) */
        [class*="col"] > .stat-card { height: 100%; }
        a.stat-card, .stat-card[data-filtro] { cursor: pointer; text-decoration: none; color: inherit; }
        a.stat-card:hover, .stat-card[data-filtro]:hover {
            transform: translateY(-2px);
            box-shadow: var(--sombra-2);
            border-color: #d3d9cd;
        }
        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .stat-valor {
            font-family: 'Outfit', sans-serif;
            font-size: 1.65rem;
            font-weight: 600;
            line-height: 1.05;
            color: var(--texto);
            font-variant-numeric: tabular-nums;
            letter-spacing: -.02em;
        }
        .stat-label {
            font-size: .8rem;
            color: var(--texto-2);
            font-weight: 500;
            margin-top: .15rem;
        }

        .seccion-titulo {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: var(--texto);
            letter-spacing: -.01em;
        }
        .seccion-titulo > i { color: var(--at-600); }

        .estado-vacio {
            background: var(--superficie);
            border: 1.5px dashed #cfd8c9;
            border-radius: calc(var(--radio) + 4px);
            padding: 3.5rem 1.5rem;
            text-align: center;
        }
        .estado-vacio > i {
            font-size: 2.6rem;
            color: var(--at-500);
            display: inline-block;
            animation: brota 2.8s ease-in-out infinite;
        }
        @keyframes brota {
            0%, 100% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-6px) rotate(-4deg); }
        }

        .panel-form {
            background: var(--superficie);
            border-radius: calc(var(--radio) + 4px);
            border: 1px solid var(--borde);
            box-shadow: var(--sombra-1);
            overflow: hidden;
        }

        /* ---------- Formularios ---------- */
        .form-control, .form-select {
            border-radius: var(--radio-sm);
            padding: .62rem .9rem;
            background-color: var(--superficie);
            border: 1px solid var(--borde);
            color: var(--texto);
            transition: border-color .2s, box-shadow .2s, background-color .2s;
        }
        .form-control:hover, .form-select:hover { border-color: #cdd5c7; }
        .form-control:focus, .form-select:focus {
            background-color: #fff;
            border-color: var(--at-500);
            box-shadow: 0 0 0 4px rgba(57, 163, 63, .14);
        }
        .form-control::placeholder { color: var(--texto-3); }
        select.form-control {
            appearance: none;
            padding-right: 2.4rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%235d6b62' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right .9rem center;
            background-size: 12px 10px;
        }
        .form-label {
            font-weight: 600;
            font-size: .85rem;
            color: var(--texto);
            margin-bottom: .4rem;
        }
        .form-label > i { color: var(--texto-3); }
        .form-text { color: var(--texto-3); font-size: .78rem; }
        .input-group-text {
            background: var(--fondo);
            border-color: var(--borde);
            color: var(--texto-2);
            border-radius: var(--radio-sm);
        }
        .form-check-input:checked { background-color: var(--at-600); border-color: var(--at-600); }
        .form-check-input:focus { box-shadow: 0 0 0 4px rgba(57, 163, 63, .14); border-color: var(--at-500); }

        /* ---------- Alertas, insignias, listas ---------- */
        .alert {
            border: 1px solid transparent;
            border-radius: var(--radio) !important;
            font-size: .9rem;
        }
        .alert-success { background: var(--at-50); border-color: #cfe6bd; color: var(--at-900); }
        .alert-danger { background: var(--alerta-100); border-color: #f2c9c3; color: #7d2118; }
        .alert-warning { background: var(--cosecha-100); border-color: #f5d9b5; color: #7a3d06; }
        .alert-info { background: var(--agua-100); border-color: #c7dff3; color: #0f4675; }
        .alert-light { background: var(--fondo); border-color: var(--borde) !important; }
        .badge { font-weight: 500; letter-spacing: .01em; }
        .text-bg-light { background: var(--fondo) !important; color: var(--texto-2) !important; }
        .list-group-item { border-color: var(--borde-suave); }
        .accordion { --bs-accordion-btn-focus-box-shadow: 0 0 0 4px rgba(57, 163, 63, .14); --bs-accordion-active-color: var(--at-900); }
        hr { border-color: var(--borde); opacity: 1; }
        .dropdown-menu {
            border: 1px solid var(--borde);
            border-radius: var(--radio);
            box-shadow: var(--sombra-2);
            padding: .35rem;
            font-size: .875rem;
            animation: menuAbre .18s var(--ease);
        }
        .dropdown-item { border-radius: 8px; padding: .5rem .7rem; display: flex; align-items: center; gap: .55rem; }
        .dropdown-item:active { background: var(--at-600); }
        @keyframes menuAbre {
            from { opacity: 0; transform: translateY(-4px) scale(.98); }
            to   { opacity: 1; transform: none; }
        }

        /* Enlace "Volver" de las vistas internas */
        a.text-success.fw-semibold > .bi-arrow-left { transition: transform .2s var(--ease); }
        a.text-success.fw-semibold:hover > .bi-arrow-left { transform: translateX(-3px); }

        /* ---------- Animaciones de entrada ---------- */
        .at-contenido > *:not(.riego-overlay),
        .at-contenido > .row > [class*="col"] > *,
        .at-contenido .row.g-3 > [class*="col"] {
            animation: subeAparece .6s var(--ease) backwards;
        }
        .at-contenido > *:nth-child(2) { animation-delay: .05s; }
        .at-contenido > *:nth-child(3) { animation-delay: .1s; }
        .at-contenido > *:nth-child(4) { animation-delay: .15s; }
        .at-contenido > *:nth-child(5) { animation-delay: .2s; }
        .at-contenido > *:nth-child(n+6) { animation-delay: .25s; }
        .at-contenido .row.g-3 > [class*="col"]:nth-child(2) { animation-delay: .08s; }
        .at-contenido .row.g-3 > [class*="col"]:nth-child(3) { animation-delay: .14s; }
        .at-contenido .row.g-3 > [class*="col"]:nth-child(4) { animation-delay: .2s; }
        .at-contenido .row.g-3 > [class*="col"]:nth-child(n+5) { animation-delay: .26s; }
        @keyframes subeAparece {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
    <?= $this->renderSection('estilos') ?>
</head>
<body>

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
                <?php if (! url_is('huerto/crear')): ?>
                    <a href="<?= base_url('huerto/crear') ?>" class="btn btn-huerto btn-sm px-3 py-2"><i class="bi bi-plus-lg"></i><span class="d-none d-sm-inline ms-1">Nuevo cultivo</span></a>
                <?php endif; ?>
            </div>
        </header>

        <main class="at-contenido">
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

        // Borde de la barra superior al hacer scroll
        const barra = document.getElementById('barraSuperior');
        const marcarBarra = () => barra.classList.toggle('con-sombra', window.scrollY > 4);
        window.addEventListener('scroll', marcarBarra, { passive: true });
        marcarBarra();

        // Los números de las tarjetas cuentan desde cero al cargar ("12,5 kg", "Cada 3", "80 %")
        const formato = (valor, decimales) => valor.toLocaleString('es-AR', { minimumFractionDigits: decimales, maximumFractionDigits: decimales });
        document.querySelectorAll('.stat-valor, .plan-dato .valor, [data-contar]').forEach(el => {
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
    })();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
