<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    /* ==========================================================================
       Panel principal
       Sin ornamentos de plantilla SaaS/IA: estructura de ficha técnica,
       tipografía editorial humanista + sans operativa, alto contraste al sol.
       ========================================================================== */

    .hc-panel {
        --hc-papel: #ffffff;
        --hc-lienzo: #f4f6f2;
        --hc-salvia: #ebf0e8;
        --hc-salvia-2: #dfe7da;
        --hc-tinta: #16241b;
        --hc-tinta-2: #46574c;
        --hc-tinta-3: #637368;
        --hc-borde: #cdd8c9;
        --hc-borde-fino: #e2e9df;

        --hc-hoja: #1b6636;
        --hc-hoja-claro: #e7f3eb;
        --hc-acequia: #0d588a;
        --hc-acequia-claro: #e4f0f8;
        --hc-cosecha: #9c5106;
        --hc-cosecha-claro: #fcf0e1;
        --hc-alerta: #b3261e;
        --hc-alerta-claro: #fbeae8;

        --hc-serif: 'Source Serif 4', Georgia, serif;
        --hc-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;

        font-family: var(--hc-sans);
        color: var(--hc-tinta);
        font-variant-numeric: tabular-nums;
    }

    /* Desactivamos las animaciones en cascada genéricas del layout global dentro del panel */
    .at-contenido > *:not(.riego-overlay),
    .at-contenido > .row > [class*="col"] > *,
    .at-contenido .row.g-3 > [class*="col"] {
        animation: none !important;
    }

    /* ---------- 1. Cabecera del huerto y semana de riego ---------- */
    .hc-cabecera {
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        border-top: 4px solid var(--hc-hoja);
        border-radius: 8px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .hc-cabecera-cuerpo {
        display: grid;
        grid-template-columns: 1fr;
    }
    @media (min-width: 992px) {
        .hc-cabecera-cuerpo {
            grid-template-columns: 1.15fr 1fr;
        }
    }

    .hc-identidad {
        padding: 1.35rem 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
    }

    .hc-fecha-linea {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        font-size: .84rem;
        color: var(--hc-tinta-2);
    }
    .hc-fecha-actual {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-weight: 600;
        color: var(--hc-hoja);
    }
    .hc-link-config {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .82rem;
        font-weight: 600;
        color: var(--hc-tinta-2);
        text-decoration: none;
        padding: .25rem .6rem;
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: var(--hc-lienzo);
        transition: background .15s, color .15s, border-color .15s;
    }
    .hc-link-config:hover {
        background: var(--hc-salvia);
        color: var(--hc-tinta);
        border-color: #b5c4ae;
    }

    .hc-titulo-huerto {
        font-family: var(--hc-serif);
        font-size: clamp(1.5rem, 2.3vw, 1.95rem);
        font-weight: 700;
        color: var(--hc-tinta);
        letter-spacing: -.015em;
        margin: .15rem 0 0;
        line-height: 1.15;
    }

    /* Ficha de propiedades del huerto (reemplaza las cadenas con puntos medios) */
    .hc-ficha-tecnica {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
        gap: 0;
        margin: 0;
        border: 1px solid var(--hc-borde-fino);
        border-radius: 6px;
        background: var(--hc-lienzo);
        overflow: hidden;
    }
    .hc-ficha-item {
        padding: .55rem .75rem;
        border-right: 1px solid var(--hc-borde-fino);
        border-bottom: 1px solid var(--hc-borde-fino);
    }
    .hc-ficha-item:last-child { border-right: 0; }
    .hc-ficha-item dt {
        font-size: .74rem;
        font-weight: 500;
        color: var(--hc-tinta-2);
        margin-bottom: .1rem;
    }
    .hc-ficha-item dd {
        margin: 0;
        font-size: .86rem;
        font-weight: 600;
        color: var(--hc-tinta);
        line-height: 1.3;
    }
    .hc-ficha-item dd a {
        color: var(--hc-hoja);
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .hc-ficha-item dd.turno-activo {
        color: var(--hc-acequia);
    }

    /* Riegos de los próximos 7 días */
    .hc-almanaque {
        padding: 1.25rem 1.4rem;
        background: var(--hc-salvia);
        border-top: 1px solid var(--hc-borde);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    @media (min-width: 992px) {
        .hc-almanaque {
            border-top: 0;
            border-left: 1px solid var(--hc-borde);
        }
    }

    .hc-almanaque-cabecera {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .75rem;
    }
    .hc-almanaque-titulo {
        font-size: .88rem;
        font-weight: 700;
        color: var(--hc-tinta);
        margin: 0;
    }
    .hc-almanaque-sub {
        font-size: .78rem;
        color: var(--hc-tinta-2);
    }

    .hc-semana-grilla {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: .35rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .hc-dia {
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        padding: .5rem .25rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        min-height: 82px;
    }
    .hc-dia.es-hoy {
        border-color: var(--hc-hoja);
        border-top-width: 3px;
        background: #ffffff;
    }
    .hc-dia.con-turno {
        border-color: #90bddb;
        background: #f5faff;
    }
    .hc-dia.es-hoy.con-turno {
        border-color: var(--hc-acequia);
        border-top-width: 3px;
    }
    .hc-dia-nombre {
        font-size: .74rem;
        font-weight: 600;
        color: var(--hc-tinta-2);
        text-transform: capitalize;
    }
    .hc-dia.es-hoy .hc-dia-nombre {
        color: var(--hc-hoja);
        font-weight: 700;
    }
    .hc-dia-num {
        font-family: var(--hc-serif);
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--hc-tinta);
        line-height: 1.1;
        margin: .15rem 0;
    }
    .hc-dia-carga {
        font-size: .74rem;
        font-weight: 600;
        color: var(--hc-acequia);
        display: inline-flex;
        align-items: center;
        gap: .2rem;
    }
    .hc-dia-carga.vacio {
        color: var(--hc-tinta-3);
        font-weight: 400;
    }
    .hc-dia-turno {
        margin-top: .2rem;
        font-size: .68rem;
        font-weight: 700;
        color: #fff;
        background: var(--hc-acequia);
        border-radius: 4px;
        padding: .05rem .35rem;
        line-height: 1.3;
    }
    @media (max-width: 575.98px) {
        .hc-identidad { padding: 1.1rem; }
        .hc-almanaque { padding: 1rem 1.1rem; }
        .hc-semana-grilla { gap: .2rem; }
        .hc-dia { min-height: 72px; padding: .4rem .15rem; }
        .hc-dia-palabra { display: none; }
    }

    /* ---------- 2. Módulos Operativos: Trabajo del Día + Estado Hídrico y Sanidad ---------- */
    .hc-caja {
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        border-radius: 8px;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .hc-caja-cabecera {
        padding: .95rem 1.25rem;
        background: var(--hc-lienzo);
        border-bottom: 1px solid var(--hc-borde);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }
    .hc-caja-titulo {
        font-family: var(--hc-serif);
        font-size: 1.12rem;
        font-weight: 700;
        color: var(--hc-tinta);
        margin: 0;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .hc-caja-titulo i {
        color: var(--hc-hoja);
        font-size: 1rem;
    }

    .hc-resumen-pill {
        font-size: .78rem;
        font-weight: 600;
        padding: .22rem .65rem;
        border-radius: 6px;
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        color: var(--hc-tinta-2);
    }
    .hc-resumen-pill.agua {
        background: var(--hc-acequia-claro);
        border-color: #b6d4ea;
        color: var(--hc-acequia);
    }

    /* Lista de tareas del día */
    .hc-tareas-lista {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .hc-tarea {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .85rem 1.25rem;
        border-bottom: 1px solid var(--hc-borde-fino);
    }
    .hc-tarea:last-child { border-bottom: 0; }
    .hc-tarea-izq {
        display: flex;
        align-items: flex-start;
        gap: .8rem;
        min-width: 0;
        flex: 1;
    }
    .hc-tarea-marca {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: .95rem;
        margin-top: .1rem;
        border: 1px solid transparent;
    }
    .hc-tarea-marca.agua {
        background: var(--hc-acequia-claro);
        color: var(--hc-acequia);
        border-color: #c2dcf0;
    }
    .hc-tarea-marca.cosecha {
        background: var(--hc-cosecha-claro);
        color: var(--hc-cosecha);
        border-color: #edd0b0;
    }
    .hc-tarea-marca.hecho {
        background: var(--hc-hoja-claro);
        color: var(--hc-hoja);
        border-color: #c3e0cc;
    }

    .hc-tarea-titulo {
        font-weight: 600;
        font-size: .92rem;
        color: var(--hc-tinta);
        line-height: 1.3;
    }
    .hc-tarea-titulo strong {
        font-family: var(--hc-serif);
        font-weight: 700;
        font-size: .98rem;
    }
    .hc-tarea-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem .75rem;
        font-size: .8rem;
        color: var(--hc-tinta-2);
        margin-top: .2rem;
    }
    .hc-tarea-meta span {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }
    .hc-dosis {
        font-weight: 700;
        color: var(--hc-acequia);
        background: var(--hc-acequia-claro);
        padding: .05rem .45rem;
        border-radius: 4px;
    }

    .hc-tareas-vacio {
        padding: 1.5rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .9rem;
        color: var(--hc-tinta-2);
        font-size: .9rem;
    }

    /* Botones sobrios de herramienta profesional */
    .hc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        font-family: var(--hc-sans);
        font-size: .84rem;
        font-weight: 600;
        padding: .45rem .9rem;
        border-radius: 6px;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s, border-color .15s, color .15s;
    }
    .hc-btn-agua {
        background: var(--hc-acequia);
        border-color: #09436b;
        color: #fff;
    }
    .hc-btn-agua:hover, .hc-btn-agua:focus-visible {
        background: #09436b;
        color: #fff;
    }
    .hc-btn-cosecha {
        background: var(--hc-cosecha-claro);
        border-color: #dfba92;
        color: var(--hc-cosecha);
    }
    .hc-btn-cosecha:hover, .hc-btn-cosecha:focus-visible {
        background: #f6e1c5;
        border-color: #cfa06d;
        color: #783d03;
    }
    .hc-btn-secundario {
        background: var(--hc-papel);
        border-color: var(--hc-borde);
        color: var(--hc-tinta);
    }
    .hc-btn-secundario:hover, .hc-btn-secundario:focus-visible {
        background: var(--hc-salvia);
        border-color: #b4c3ad;
        color: var(--hc-tinta);
    }

    /* Columna derecha: Balance de Agua y Sanidad */
    .hc-bloque-lateral {
        padding: 1.1rem 1.25rem;
    }
    .hc-bloque-lateral + .hc-bloque-lateral {
        border-top: 1px solid var(--hc-borde);
    }
    .hc-subtitulo-lateral {
        font-size: .86rem;
        font-weight: 700;
        color: var(--hc-tinta);
        margin: 0 0 .65rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }
    .hc-metrica-principal {
        display: flex;
        align-items: baseline;
        gap: .45rem;
        margin-bottom: .5rem;
    }
    .hc-metrica-numero {
        font-family: var(--hc-serif);
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--hc-tinta);
        line-height: 1;
    }
    .hc-metrica-unidad {
        font-size: .84rem;
        color: var(--hc-tinta-2);
        font-weight: 500;
    }

    .hc-barra-tanque {
        background: var(--hc-lienzo);
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        padding: .65rem .8rem;
        margin-top: .6rem;
    }
    .hc-barra-tanque-top {
        display: flex;
        justify-content: space-between;
        font-size: .78rem;
        margin-bottom: .35rem;
        color: var(--hc-tinta-2);
    }
    .hc-barra-pista {
        height: 7px;
        background: #dde4d9;
        border-radius: 4px;
        overflow: hidden;
    }
    .hc-barra-relleno {
        height: 100%;
        background: var(--hc-acequia);
        border-radius: inherit;
    }
    .hc-barra-relleno.deficit {
        background: var(--hc-alerta);
    }

    .hc-lista-notas {
        list-style: none;
        padding: 0;
        margin: .5rem 0 0;
        font-size: .83rem;
        color: var(--hc-tinta-2);
    }
    .hc-lista-notas li {
        display: flex;
        align-items: flex-start;
        gap: .45rem;
        padding: .3rem 0;
    }
    .hc-lista-notas li i {
        margin-top: .12rem;
        flex-shrink: 0;
    }

    /* ---------- 3. Inventario de Cultivos (Controles + Fichas + Tabla) ---------- */
    .hc-seccion-encabezado {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin: 2rem 0 1rem;
        padding-bottom: .75rem;
        border-bottom: 2px solid var(--hc-tinta);
    }
    .hc-seccion-titulo {
        font-family: var(--hc-serif);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--hc-tinta);
        margin: 0;
    }
    .hc-seccion-sub {
        font-size: .85rem;
        color: var(--hc-tinta-2);
        margin: .15rem 0 0;
    }

    .hc-controles {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .65rem;
        margin-bottom: 1.25rem;
    }
    .hc-filtros {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 3px;
        padding: 3px;
        background: var(--hc-salvia);
        border: 1px solid var(--hc-borde);
        border-radius: 8px;
    }
    .hc-filtro-btn {
        border: 1px solid transparent;
        background: transparent;
        color: var(--hc-tinta-2);
        font-size: .83rem;
        font-weight: 600;
        padding: .38rem .75rem;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        cursor: pointer;
        transition: background .15s, color .15s, border-color .15s;
    }
    .hc-filtro-btn:hover { color: var(--hc-tinta); }
    .hc-filtro-btn.activo {
        background: var(--hc-papel);
        color: var(--hc-tinta);
        border-color: var(--hc-borde);
    }
    .hc-filtro-btn .cuenta {
        font-size: .74rem;
        font-weight: 700;
        padding: .02rem .38rem;
        border-radius: 4px;
        background: var(--hc-lienzo);
        color: var(--hc-tinta-2);
    }
    .hc-filtro-btn.activo .cuenta {
        background: var(--hc-hoja-claro);
        color: var(--hc-hoja);
    }

    .hc-buscador {
        position: relative;
        margin-left: auto;
        min-width: 230px;
    }
    .hc-buscador i {
        position: absolute;
        left: .75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--hc-tinta-3);
        pointer-events: none;
    }
    .hc-buscador input {
        width: 100%;
        padding: .42rem .8rem .42rem 2.15rem;
        font-size: .85rem;
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: var(--hc-papel);
        color: var(--hc-tinta);
    }
    .hc-buscador input:focus {
        outline: 2px solid var(--hc-hoja);
        outline-offset: 0;
        border-color: var(--hc-hoja);
    }

    .hc-vista-switch {
        display: inline-flex;
        padding: 3px;
        background: var(--hc-salvia);
        border: 1px solid var(--hc-borde);
        border-radius: 8px;
    }
    .hc-vista-btn {
        border: 1px solid transparent;
        background: transparent;
        color: var(--hc-tinta-2);
        font-size: .82rem;
        font-weight: 600;
        padding: .35rem .65rem;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .hc-vista-btn.activo {
        background: var(--hc-papel);
        color: var(--hc-tinta);
        border-color: var(--hc-borde);
    }
    @media (max-width: 767.98px) {
        .hc-buscador { margin-left: 0; flex: 1 1 200px; }
    }

    /* ---------- Ficha de Cultivo (Vista Cuadrícula) ---------- */
    .hc-ficha {
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        border-left: 4px solid var(--hc-hoja);
        border-radius: 8px;
        padding: 1.15rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: border-color .15s;
    }
    .hc-ficha:hover {
        border-color: #9eb098;
    }
    .hc-ficha[data-estado="riego"] { border-left-color: var(--hc-acequia); }
    .hc-ficha[data-estado="cosecha"] { border-left-color: var(--hc-cosecha); }
    .hc-ficha[data-estado="cosechado"] {
        border-left-color: #95a399;
        background: #fafbf9;
    }

    .hc-ficha-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .6rem;
        margin-bottom: .75rem;
    }
    .hc-cultivo-titulo {
        font-family: var(--hc-serif);
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--hc-tinta);
        margin: 0;
        line-height: 1.2;
        text-transform: capitalize;
    }
    .hc-cultivo-variedad {
        font-family: var(--hc-sans);
        font-size: .88rem;
        font-weight: 500;
        color: var(--hc-tinta-2);
    }
    .hc-cultivo-siembra {
        font-size: .78rem;
        color: var(--hc-tinta-2);
        margin-top: .2rem;
    }

    .hc-ficha-top-der {
        display: flex;
        align-items: center;
        gap: .35rem;
        flex-shrink: 0;
    }
    .hc-estado-tag {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .75rem;
        font-weight: 700;
        padding: .22rem .55rem;
        border-radius: 5px;
        border: 1px solid transparent;
    }
    .hc-estado-riego {
        background: var(--hc-acequia-claro);
        color: var(--hc-acequia);
        border-color: #b8d8ee;
    }
    .hc-estado-cosecha {
        background: var(--hc-cosecha-claro);
        color: var(--hc-cosecha);
        border-color: #ebd0b0;
    }
    .hc-estado-crecimiento {
        background: var(--hc-hoja-claro);
        color: var(--hc-hoja);
        border-color: #c5e2ce;
    }
    .hc-estado-cosechado {
        background: var(--hc-lienzo);
        color: var(--hc-tinta-2);
        border-color: var(--hc-borde);
    }

    .hc-menu-btn {
        border: 1px solid transparent;
        background: transparent;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        color: var(--hc-tinta-2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .hc-menu-btn:hover, .hc-menu-btn[aria-expanded="true"] {
        background: var(--hc-lienzo);
        border-color: var(--hc-borde);
        color: var(--hc-tinta);
    }

    /* Ciclo en tierra */
    .hc-ciclo {
        background: var(--hc-lienzo);
        border: 1px solid var(--hc-borde-fino);
        border-radius: 6px;
        padding: .65rem .75rem;
        margin-bottom: .75rem;
    }
    .hc-ciclo-cabecera {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        font-size: .79rem;
        color: var(--hc-tinta-2);
        margin-bottom: .35rem;
    }
    .hc-ciclo-cabecera strong {
        color: var(--hc-tinta);
        font-weight: 700;
    }
    .hc-ciclo-pista {
        height: 6px;
        border-radius: 3px;
        background: #dce3d8;
        overflow: hidden;
    }
    .hc-ciclo-barra {
        display: block;
        height: 100%;
        width: var(--p);
        background: var(--hc-hoja);
        border-radius: inherit;
    }
    .hc-ficha[data-estado="cosecha"] .hc-ciclo-barra { background: var(--hc-cosecha); }
    .hc-ficha[data-estado="cosechado"] .hc-ciclo-barra { background: #8c9a90; }

    .hc-ciclo-pie {
        font-size: .76rem;
        color: var(--hc-tinta-2);
        margin-top: .38rem;
        display: flex;
        justify-content: space-between;
        gap: .5rem;
    }

    /* Tabla interna de 3 celdas (Sentence case, sin UPPERCASE) */
    .hc-datos-tabla {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        margin: 0 0 .75rem;
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: var(--hc-papel);
        overflow: hidden;
    }
    .hc-datos-tabla > div {
        padding: .5rem .6rem;
        min-width: 0;
    }
    .hc-datos-tabla > div + div {
        border-left: 1px solid var(--hc-borde);
    }
    .hc-datos-tabla dt {
        font-size: .72rem;
        font-weight: 500;
        color: var(--hc-tinta-2);
        margin-bottom: .1rem;
    }
    .hc-datos-tabla dd {
        margin: 0;
        font-size: .85rem;
        font-weight: 700;
        color: var(--hc-tinta);
    }
    .hc-datos-tabla dd.urgente {
        color: var(--hc-acequia);
    }

    .hc-notas-cultivo {
        display: flex;
        flex-direction: column;
        gap: .3rem;
        margin-bottom: .75rem;
        font-size: .78rem;
    }
    .hc-notas-cultivo span, .hc-notas-cultivo a {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        text-decoration: none;
    }

    .hc-ficha-acciones {
        display: flex;
        gap: .5rem;
        margin-top: auto;
        padding-top: .25rem;
    }
    .hc-ficha-acciones form, .hc-ficha-acciones > a {
        flex: 1;
        display: flex;
    }
    .hc-ficha-acciones .hc-btn {
        width: 100%;
    }

    /* ---------- Vista de Tabla de Campo Compacta ---------- */
    /* En pantallas chicas la tabla se desplaza de costado en vez de apretar las columnas */
    @media (max-width: 991.98px) {
        .hc-tabla th, .hc-tabla td { white-space: nowrap; }
    }
    .hc-tabla-contenedor {
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        border-radius: 8px;
        overflow-x: auto;
    }
    .hc-tabla {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }
    .hc-tabla thead th {
        background: var(--hc-salvia);
        color: var(--hc-tinta);
        font-weight: 700;
        font-size: .8rem;
        padding: .75rem 1rem;
        border-bottom: 1.5px solid var(--hc-borde);
        text-align: left;
        white-space: nowrap;
    }
    .hc-tabla tbody td {
        padding: .75rem 1rem;
        border-bottom: 1px solid var(--hc-borde-fino);
        vertical-align: middle;
    }
    .hc-tabla tbody tr:last-child td { border-bottom: 0; }
    .hc-tabla tbody tr:hover { background: var(--hc-lienzo); }
    .hc-tabla-nombre {
        font-family: var(--hc-serif);
        font-weight: 700;
        font-size: 1rem;
        color: var(--hc-tinta);
        text-transform: capitalize;
    }
    .hc-tabla-sub {
        font-size: .78rem;
        color: var(--hc-tinta-2);
    }

    .sin-resultados { display: none; }
    .sin-resultados.visible { display: block; }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $litros = fn ($valor) => number_format((float) $valor, 1, ',', '.');
    $estados = [
        'riego'       => ['texto' => 'Regar hoy',            'clase' => 'hc-estado-riego',       'icono' => 'bi-droplet-fill'],
        'cosecha'     => ['texto' => 'Lista para cosechar',  'clase' => 'hc-estado-cosecha',     'icono' => 'bi-basket2-fill'],
        'crecimiento' => ['texto' => 'En crecimiento',       'clase' => 'hc-estado-crecimiento', 'icono' => 'bi-sprout'],
        'cosechado'   => ['texto' => 'Cosechado',            'clase' => 'hc-estado-cosechado',   'icono' => 'bi-check2-circle'],
    ];

    $mesesAnio = \App\Libraries\Fechas::MESES;
    $horarios = \App\Models\CultivoModel::HORARIOS_RIEGO;

    // Total de litros que requieren las plantas que piden agua hoy
    $litrosHoyTotal = array_sum(array_map(fn ($p) => (float) $p['litros_hoy'], $paraRegar));
    $activosCount = $cuentas['todos'] - $cuentas['cosechado'];
?>

<div class="hc-panel">

    <?= $this->include('layout/avisos') ?>

    <!-- 1. CABECERA DEL HUERTO Y SEMANA DE RIEGO -->
    <section class="hc-cabecera" aria-labelledby="nombreHuerto">
        <div class="hc-cabecera-cuerpo">
            <div class="hc-identidad">
                <div>
                    <div class="hc-fecha-linea">
                        <time class="hc-fecha-actual" datetime="<?= $hoyFecha->format('Y-m-d') ?>">
                            <i class="bi bi-calendar3"></i>
                            <?= ucfirst($hoyTexto) ?> de <?= $hoyFecha->format('Y') ?>
                        </time>
                        <a href="<?= base_url('huerto/configuracion') ?>" class="hc-link-config">
                            <i class="bi bi-sliders"></i> Configurar huerto
                        </a>
                    </div>
                    <h1 id="nombreHuerto" class="hc-titulo-huerto"><?= esc($huerto['nombre']) ?></h1>
                </div>

                <dl class="hc-ficha-tecnica">
                    <div class="hc-ficha-item">
                        <dt>Zona</dt>
                        <dd>
                            <?php if ($zona): ?>
                                <?= esc($zona['nombre']) ?>
                            <?php else: ?>
                                <a href="<?= base_url('huerto/configuracion') ?>">Elegí la zona de tu huerto</a>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="hc-ficha-item">
                        <dt>Método de riego</dt>
                        <dd><?= esc($riego['metodo']['nombre']) ?> (<?= round($riego['metodo']['eficiencia'] * 100) ?>%)</dd>
                    </div>
                    <div class="hc-ficha-item">
                        <dt>Turno de agua</dt>
                        <?php if ($riego['usa_turno']): ?>
                            <dd class="<?= $hoyHayTurno ? 'turno-activo' : '' ?>">
                                <?php if ($hoyHayTurno): ?>
                                    <i class="bi bi-water me-1"></i>Hoy hay turno de agua
                                <?php elseif (! empty($riego['proximos_turnos'])): ?>
                                    Próximo turno: <?= $riego['proximos_turnos'][0]->format('d/m') ?>
                                <?php else: ?>
                                    Turno de agua sin fechas
                                <?php endif; ?>
                            </dd>
                        <?php else: ?>
                            <dd>Agua de red (sin turno)</dd>
                        <?php endif; ?>
                    </div>
                </dl>
            </div>

            <div class="hc-almanaque">
                <div class="hc-almanaque-cabecera">
                    <h2 class="hc-almanaque-titulo">Riegos de los próximos 7 días</h2>
                    <span class="hc-almanaque-sub"><?= ucfirst($mesesAnio[(int) $hoyFecha->format('n') - 1]) ?></span>
                </div>

                <?php if (! empty($cultivos)): ?>
                    <ol class="hc-semana-grilla" aria-label="Riegos de los próximos 7 días">
                        <?php foreach ($semana as $i => $dia): ?>
                            <li class="hc-dia <?= $i === 0 ? 'es-hoy' : '' ?> <?= $dia['turno'] ? 'con-turno' : '' ?>">
                                <span class="visually-hidden">
                                    <?= $i === 0 ? 'Hoy, ' : '' ?><?= $dia['largo'] ?>: <?= $dia['riegos'] ?: 'sin' ?> riego<?= $dia['riegos'] == 1 ? '' : 's' ?><?= $dia['turno'] ? ', turno de agua' : '' ?>
                                </span>
                                <span class="hc-dia-nombre" aria-hidden="true"><?= $dia['nombre'] ?></span>
                                <span class="hc-dia-num" aria-hidden="true"><?= $dia['numero'] ?></span>
                                <?php if ($dia['riegos']): ?>
                                    <span class="hc-dia-carga" aria-hidden="true">
                                        <i class="bi bi-droplet-fill"></i><?= $dia['riegos'] ?><span class="hc-dia-palabra"> riego<?= $dia['riegos'] == 1 ? '' : 's' ?></span>
                                    </span>
                                <?php else: ?>
                                    <span class="hc-dia-carga vacio" aria-hidden="true">Sin riego</span>
                                <?php endif; ?>
                                <?php if ($dia['turno']): ?>
                                    <span class="hc-dia-turno" aria-hidden="true">Turno</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <div class="small text-muted py-3">
                        Cuando agregues cultivos, acá vas a ver qué días te toca regar y cuándo hay turno de agua.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 2. TAREAS DEL DÍA + RIEGO Y PROBLEMAS -->
    <div class="row g-3 mb-4 align-items-lg-start">
        <div class="col-lg-7">
            <section class="hc-caja" aria-labelledby="tituloTareas">
                <div class="hc-caja-cabecera">
                    <h2 id="tituloTareas" class="hc-caja-titulo">
                        <i class="bi bi-clipboard2-check"></i> Para hacer hoy
                    </h2>
                    <?php if ($paraRegar): ?>
                        <span class="hc-resumen-pill agua">
                            <i class="bi bi-droplet-half me-1"></i><?= count($paraRegar) ?> riego<?= count($paraRegar) === 1 ? '' : 's' ?> hoy · <?= $litros($litrosHoyTotal) ?> L
                        </span>
                    <?php elseif ($paraCosechar): ?>
                        <span class="hc-resumen-pill">
                            <?= count($paraCosechar) ?> cultivo<?= count($paraCosechar) === 1 ? '' : 's' ?> para cosechar
                        </span>
                    <?php else: ?>
                        <span class="hc-resumen-pill">Todo al día</span>
                    <?php endif; ?>
                </div>

                <?php if (empty($cultivos)): ?>
                    <div class="hc-tareas-vacio">
                        <span class="hc-tarea-marca hecho"><i class="bi bi-plus-lg"></i></span>
                        <div>
                            <div class="fw-semibold text-dark">Todavía no hay tareas</div>
                            <div class="small">Agregá tus cultivos y acá te avisamos qué regar y qué cosechar cada día.</div>
                        </div>
                    </div>
                <?php elseif ($hayTareas): ?>
                    <ul class="hc-tareas-lista">
                        <?php if ($riego['usa_turno'] && $hoyHayTurno && $riego['reservorio'] > 0): ?>
                            <li class="hc-tarea">
                                <div class="hc-tarea-izq">
                                    <span class="hc-tarea-marca agua"><i class="bi bi-water"></i></span>
                                    <div>
                                        <div class="hc-tarea-titulo">Llenar el reservorio</div>
                                        <div class="hc-tarea-meta">
                                            <span>Hoy hay turno de agua: guardá hasta <?= $riego['reservorio'] ?> L para regar entre turnos.</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php foreach ($paraRegar as $planta): ?>
                            <?php $horario = $horarios[$planta['horario_riego']] ?? $horarios['manana']; ?>
                            <li class="hc-tarea">
                                <div class="hc-tarea-izq">
                                    <span class="hc-tarea-marca agua"><i class="bi bi-droplet-fill"></i></span>
                                    <div>
                                        <div class="hc-tarea-titulo">
                                            Regar <strong><?= esc(mb_strtolower($planta['nombre_planta'])) ?></strong><?= $planta['variedad'] ? ' (' . esc($planta['variedad']) . ')' : '' ?>
                                        </div>
                                        <div class="hc-tarea-meta">
                                            <span class="hc-dosis"><?= $litros($planta['litros_hoy']) ?> L</span>
                                            <span><i class="bi bi-clock"></i><?= esc($horario['nombre']) ?> (<?= esc(implode(' y ', $horario['franjas'])) ?>)</span>
                                            <?php if ($riego['usa_turno'] && ! $hoyHayTurno): ?>
                                                <span><i class="bi bi-bucket"></i>Con agua guardada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <form action="<?= base_url('huerto/riego/' . $planta['id']) ?>" method="POST" class="hc-form-riego">
                                    <button type="submit" class="hc-btn hc-btn-agua" aria-label="Regar <?= esc($planta['nombre_planta'], 'attr') ?>">
                                        <i class="bi bi-check2"></i> Regar
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>

                        <?php foreach ($paraCosechar as $planta): ?>
                            <li class="hc-tarea">
                                <div class="hc-tarea-izq">
                                    <span class="hc-tarea-marca cosecha"><i class="bi bi-basket2-fill"></i></span>
                                    <div>
                                        <div class="hc-tarea-titulo">
                                            Cosechar <strong><?= esc(mb_strtolower($planta['nombre_planta'])) ?></strong><?= $planta['variedad'] ? ' (' . esc($planta['variedad']) . ')' : '' ?>
                                        </div>
                                        <div class="hc-tarea-meta">
                                            <span>Lista desde el <?= $planta['fecha_cosecha']->format('d/m') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= base_url('huerto/cosechar/' . $planta['id']) ?>" class="hc-btn hc-btn-cosecha" aria-label="Registrar cosecha de <?= esc($planta['nombre_planta'], 'attr') ?>">
                                    Registrar
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <?php if ($regadosHoy): ?>
                            <li class="hc-tarea">
                                <div class="hc-tarea-izq">
                                    <span class="hc-tarea-marca hecho"><i class="bi bi-check2-all"></i></span>
                                    <div>
                                        <div class="hc-tarea-titulo">Ya regaste hoy</div>
                                        <div class="hc-tarea-meta">
                                            <span><?= ucfirst(esc($listaRegados)) ?>. No hace falta volver a regarlos hoy.</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <div class="hc-tareas-vacio">
                        <span class="hc-tarea-marca hecho"><i class="bi bi-check2-circle"></i></span>
                        <div>
                            <div class="fw-semibold text-dark">
                                <?php if ($regadosHoy): ?>
                                    Todo al día: ya regaste <?= esc($listaRegados) ?>.
                                <?php else: ?>
                                    No hay tareas para hoy.
                                <?php endif; ?>
                            </div>
                            <?php if ($proximoRiego): ?>
                                <div class="small mt-1">
                                    Próximo riego: <strong><?= esc(mb_strtolower($proximoRiego['nombre_planta'])) ?></strong>,
                                    <?= $proximoRiego['dias_proximo_riego'] === 1 ? 'mañana' : 'en ' . $proximoRiego['dias_proximo_riego'] . ' días' ?>.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="hc-caja" aria-label="Riego y problemas">
                <div class="hc-caja-cabecera">
                    <h2 class="hc-caja-titulo">
                        <i class="bi bi-moisture"></i> Riego y problemas
                    </h2>
                    <a href="<?= base_url('huerto/reportes') ?>" class="hc-resumen-pill text-decoration-none">
                        Ver reporte del mes
                    </a>
                </div>

                <div class="hc-bloque-lateral">
                    <div class="hc-subtitulo-lateral">
                        <span>Riego de este mes</span>
                        <span class="small fw-normal text-muted"><?= $activosCount ?> cultivo<?= $activosCount === 1 ? '' : 's' ?> en curso</span>
                    </div>
                    <div class="hc-metrica-principal">
                        <span class="hc-metrica-numero"><?= $litros($riego['litros_semana']) ?> L</span>
                        <span class="hc-metrica-unidad">por semana</span>
                    </div>

                    <?php if ($riego['usa_turno'] && $riego['agua_entre_turnos'] > 0): ?>
                        <?php
                            $porcentajeReserva = $riego['reservorio'] > 0
                                ? min(100, round($riego['agua_entre_turnos'] / $riego['reservorio'] * 100))
                                : 100;
                        ?>
                        <div class="hc-barra-tanque">
                            <div class="hc-barra-tanque-top">
                                <span>Entre turnos necesitás: <strong><?= $litros($riego['agua_entre_turnos']) ?> L</strong></span>
                                <span>Podés guardar: <strong><?= $riego['reservorio'] ?> L</strong></span>
                            </div>
                            <div class="hc-barra-pista" role="progressbar" aria-label="Agua guardada que se usa entre turnos" aria-valuenow="<?= $porcentajeReserva ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="hc-barra-relleno <?= $riego['reservorio_alcanza'] ? '' : 'deficit' ?>" style="width: <?= max(6, $porcentajeReserva) ?>%"></div>
                            </div>
                            <div class="small mt-1 <?= $riego['reservorio_alcanza'] ? 'text-success' : 'text-danger fw-semibold' ?>">
                                <?php if ($riego['reservorio_alcanza']): ?>
                                    Con el agua que guardás alcanza entre turnos.
                                <?php else: ?>
                                    Te faltan unos <?= $litros($riego['agua_entre_turnos'] - $riego['reservorio']) ?> L guardados para regar entre turnos.
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="small text-muted mb-0">
                            Los litros de cada cultivo ya están ajustados al mes y a tu método de riego.
                        </p>
                    <?php endif; ?>
                </div>

                <div class="hc-bloque-lateral mt-auto">
                    <div class="hc-subtitulo-lateral">
                        <span>Problemas y plagas</span>
                        <a href="<?= base_url('huerto/problemas') ?>" class="small fw-semibold text-success text-decoration-none">Ver problemas</a>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between gap-2">
                        <div class="hc-metrica-principal mb-1">
                            <span class="hc-metrica-numero"><?= $problemas['sin_resolver'] ?></span>
                            <span class="hc-metrica-unidad">sin resolver</span>
                        </div>
                        <a href="<?= base_url('huerto/problemas/nuevo') ?>" class="hc-btn hc-btn-secundario py-1 px-2">
                            <i class="bi bi-camera"></i> Reportar problema
                        </a>
                    </div>
                    <ul class="hc-lista-notas">
                        <?php if ($problemas['respondidos']): ?>
                            <li class="text-primary fw-semibold"><i class="bi bi-chat-left-text"></i>El técnico respondió <?= $problemas['respondidos'] ?> reporte(s).</li>
                        <?php endif; ?>
                        <?php if ($problemas['repetidos']): ?>
                            <li class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle"></i><?= esc(implode(', ', $problemas['repetidos'])) ?> en varios cultivos.</li>
                        <?php elseif (! $problemas['respondidos']): ?>
                            <li><i class="bi bi-camera"></i>Si ves hojas comidas, manchas o bichos, reportalo con una foto.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <!-- 3. MIS CULTIVOS -->
    <div id="cultivos" class="hc-seccion-encabezado">
        <div>
            <h2 class="hc-seccion-titulo">Mis cultivos</h2>
            <p class="hc-seccion-sub">Ordenados por lo que necesita atención primero.</p>
        </div>
        <a href="<?= base_url('huerto/crear') ?>" class="hc-btn hc-btn-secundario">
            <i class="bi bi-plus-lg"></i> Agregar cultivo
        </a>
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

        <div class="hc-controles">
            <div class="hc-filtros" role="group" aria-label="Filtrar cultivos">
                <?php foreach ([
                    'todos'       => 'Todos',
                    'riego'       => 'Para regar',
                    'cosecha'     => 'Para cosechar',
                    'crecimiento' => 'En crecimiento',
                    'cosechado'   => 'Cosechados',
                ] as $clave => $texto): ?>
                    <button type="button" class="hc-filtro-btn filtro-btn <?= $clave === 'todos' ? 'activo' : '' ?>" data-filtro="<?= $clave ?>" aria-pressed="<?= $clave === 'todos' ? 'true' : 'false' ?>">
                        <?= $texto ?> <span class="cuenta"><?= $cuentas[$clave] ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="hc-buscador">
                <i class="bi bi-search"></i>
                <input type="search" id="buscarCultivo" placeholder="Buscar cultivo o variedad" aria-label="Buscar cultivo">
            </div>

            <div class="hc-vista-switch" role="group" aria-label="Cómo ver los cultivos">
                <button type="button" class="hc-vista-btn activo" data-vista="fichas" aria-pressed="true">
                    <i class="bi bi-grid-3x2-gap"></i> Tarjetas
                </button>
                <button type="button" class="hc-vista-btn" data-vista="tabla" aria-pressed="false">
                    <i class="bi bi-table"></i> Tabla
                </button>
            </div>
        </div>

        <!-- VISTA A: TARJETAS -->
        <div class="row g-3" id="grillaCultivos">
            <?php foreach ($cultivos as $planta): ?>
                <?php
                    $estado = $planta['estado_panel'];
                    $cosechado = $estado === 'cosechado';
                    $diasCosecha = $planta['dias_a_cosecha'];
                    $proximo = $planta['proximo_riego_texto'];
                    $horarioNombre = \App\Models\CultivoModel::HORARIOS_RIEGO[$planta['horario_riego']]['nombre'] ?? 'Mañana';
                ?>
                <div class="col-sm-6 col-xl-4 cultivo-col" data-estado="<?= $estado ?>" data-riego="<?= $planta['necesita_riego'] ? '1' : '0' ?>" data-texto="<?= esc(mb_strtolower($planta['nombre_planta'] . ' ' . $planta['variedad'])) ?>">
                    <article class="hc-ficha" data-estado="<?= $estado ?>">
                        <div class="hc-ficha-top">
                            <div class="min-w-0">
                                <h3 class="hc-cultivo-titulo">
                                    <?= esc($planta['nombre_planta']) ?>
                                    <?php if ($planta['variedad']): ?>
                                        <span class="hc-cultivo-variedad">(<?= esc($planta['variedad']) ?>)</span>
                                    <?php endif; ?>
                                </h3>
                                <div class="hc-cultivo-siembra">
                                    Sembrado el <?= date('d/m/Y', strtotime($planta['fecha_siembra'])) ?>
                                </div>
                            </div>

                            <div class="hc-ficha-top-der">
                                <span class="hc-estado-tag <?= $estados[$estado]['clase'] ?>">
                                    <i class="bi <?= $estados[$estado]['icono'] ?>"></i>
                                    <?= $estados[$estado]['texto'] ?>
                                </span>
                                <div class="dropdown">
                                    <button class="hc-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Más acciones: <?= esc($planta['nombre_planta'], 'attr') ?>">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php if (! $cosechado): ?>
                                            <li><a class="dropdown-item" href="<?= base_url('huerto/cosechar/' . $planta['id']) ?>"><i class="bi bi-basket2"></i>Registrar cosecha</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('huerto/problemas/nuevo?cultivo=' . $planta['id']) ?>"><i class="bi bi-bug"></i>Reportar un problema</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                        <?php endif; ?>
                                        <li>
                                            <form action="<?= base_url('huerto/eliminar/' . $planta['id']) ?>" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar este cultivo?')">
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash3"></i>Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="hc-ciclo">
                            <div class="hc-ciclo-cabecera">
                                <span>Día <strong><?= min($planta['dias_desde_siembra'], 999) ?></strong> de <?= (int) $planta['dias_cosecha_estimados'] ?></span>
                                <strong><?= $cosechado ? 100 : $planta['progreso'] ?>%</strong>
                            </div>
                            <div class="hc-ciclo-pista" role="progressbar" aria-valuenow="<?= $cosechado ? 100 : $planta['progreso'] ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Avance del ciclo">
                                <span class="hc-ciclo-barra" style="--p: <?= $cosechado ? 100 : max(3, $planta['progreso']) ?>%"></span>
                            </div>
                            <div class="hc-ciclo-pie">
                                <?php if ($cosechado): ?>
                                    <span>Ciclo terminado</span>
                                    <?php if (isset($kgCosechados[$planta['id']])): ?>
                                        <strong><?= number_format($kgCosechados[$planta['id']], 2, ',', '.') ?> kg cosechados</strong>
                                    <?php endif; ?>
                                <?php elseif ($planta['alerta_cosecha']): ?>
                                    <span class="fw-semibold" style="color: var(--hc-cosecha)">Lista desde el <?= $planta['fecha_cosecha']->format('d/m') ?></span>
                                <?php else: ?>
                                    <span>Cosecha estimada el <?= $planta['fecha_cosecha']->format('d/m') ?></span>
                                    <span>Faltan <?= $diasCosecha ?> día<?= $diasCosecha == 1 ? '' : 's' ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (! $cosechado): ?>
                            <dl class="hc-datos-tabla">
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
                                    <dd title="<?= esc($horarioNombre) ?>"><?= (int) $planta['frecuencia_riego_dias'] ?> día<?= $planta['frecuencia_riego_dias'] == 1 ? '' : 's' ?> · <?= esc(mb_strtolower($horarioNombre)) ?></dd>
                                </div>
                            </dl>
                        <?php endif; ?>

                        <?php $conTurno = $planta['necesita_riego'] && $riego['usa_turno'] && ! $hoyHayTurno; ?>
                        <?php if ($conTurno || $planta['regado_hoy'] || ! empty($problemas['por_cultivo'][$planta['id']]) || (! $cosechado && isset($kgCosechados[$planta['id']]))): ?>
                            <div class="hc-notas-cultivo">
                                <?php if ($planta['regado_hoy']): ?>
                                    <span class="text-success fw-semibold"><i class="bi bi-check2-circle"></i>Regado hoy a las <?= date('H:i', strtotime($planta['ultimo_riego'])) ?></span>
                                <?php endif; ?>
                                <?php if ($conTurno): ?>
                                    <span style="color: var(--hc-acequia)"><i class="bi bi-info-circle"></i>Hoy no hay turno: regá con agua guardada.</span>
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
                            <div class="hc-ficha-acciones">
                                <form action="<?= base_url('huerto/riego/' . $planta['id']) ?>" method="POST" class="hc-form-riego">
                                    <button type="submit" class="hc-btn <?= $planta['necesita_riego'] ? 'hc-btn-agua' : 'hc-btn-secundario' ?>" aria-label="Regar <?= esc($planta['nombre_planta'], 'attr') ?>">
                                        <i class="bi bi-droplet-fill"></i> <?= $planta['necesita_riego'] ? 'Regar (' . $litros($planta['litros_hoy']) . ' L)' : 'Regar' ?>
                                    </button>
                                </form>
                                <?php if ($planta['alerta_cosecha'] || isset($kgCosechados[$planta['id']])): ?>
                                    <a href="<?= base_url('huerto/cosechar/' . $planta['id']) ?>" class="hc-btn hc-btn-cosecha" aria-label="Cosechar <?= esc($planta['nombre_planta'], 'attr') ?>">
                                        <i class="bi bi-basket2"></i> Cosechar
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- VISTA B: TABLA -->
        <div class="hc-tabla-contenedor" id="tablaCultivos" hidden>
            <table class="hc-tabla">
                <thead>
                    <tr>
                        <th>Cultivo</th>
                        <th>Estado</th>
                        <th>Ciclo</th>
                        <th>Próximo riego</th>
                        <th>Riego</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cultivos as $planta): ?>
                        <?php
                            $estado = $planta['estado_panel'];
                            $cosechado = $estado === 'cosechado';
                            $horarioNombre = \App\Models\CultivoModel::HORARIOS_RIEGO[$planta['horario_riego']]['nombre'] ?? 'Mañana';
                        ?>
                        <tr class="cultivo-fila" data-estado="<?= $estado ?>" data-riego="<?= $planta['necesita_riego'] ? '1' : '0' ?>" data-texto="<?= esc(mb_strtolower($planta['nombre_planta'] . ' ' . $planta['variedad'])) ?>">
                            <td>
                                <div class="hc-tabla-nombre">
                                    <?= esc($planta['nombre_planta']) ?>
                                    <?php if ($planta['variedad']): ?>
                                        <span class="fw-normal text-muted">(<?= esc($planta['variedad']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="hc-tabla-sub">Sembrado el <?= date('d/m/Y', strtotime($planta['fecha_siembra'])) ?></div>
                            </td>
                            <td>
                                <span class="hc-estado-tag <?= $estados[$estado]['clase'] ?>">
                                    <i class="bi <?= $estados[$estado]['icono'] ?>"></i>
                                    <?= $estados[$estado]['texto'] ?>
                                </span>
                                <?php if (! empty($problemas['por_cultivo'][$planta['id']])): ?>
                                    <div class="small mt-1">
                                        <a href="<?= base_url('huerto/problemas') ?>" class="text-danger fw-semibold text-decoration-none">
                                            <i class="bi bi-bug-fill"></i> <?= $problemas['por_cultivo'][$planta['id']] ?> problema(s)
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold">Día <?= min($planta['dias_desde_siembra'], 999) ?> de <?= (int) $planta['dias_cosecha_estimados'] ?> (<?= $cosechado ? 100 : $planta['progreso'] ?>%)</div>
                                <div class="hc-tabla-sub">
                                    <?php if ($cosechado): ?>
                                        Cosechado<?= isset($kgCosechados[$planta['id']]) ? ' · ' . number_format($kgCosechados[$planta['id']], 2, ',', '.') . ' kg' : '' ?>
                                    <?php else: ?>
                                        Cosecha estimada el <?= $planta['fecha_cosecha']->format('d/m') ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong class="<?= $planta['dias_proximo_riego'] === 0 && ! $cosechado ? 'text-primary' : '' ?>">
                                    <?= $planta['proximo_riego_texto'] ?>
                                </strong>
                                <?php if ($planta['regado_hoy']): ?>
                                    <div class="small text-success fw-semibold">Regado hoy a las <?= date('H:i', strtotime($planta['ultimo_riego'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (! $cosechado): ?>
                                    <div class="fw-semibold"><?= $litros($planta['litros_hoy']) ?> L por riego</div>
                                    <div class="hc-tabla-sub">Cada <?= (int) $planta['frecuencia_riego_dias'] ?> día<?= $planta['frecuencia_riego_dias'] == 1 ? '' : 's' ?> · <?= esc(mb_strtolower($horarioNombre)) ?></div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <?php if (! $cosechado): ?>
                                        <form action="<?= base_url('huerto/riego/' . $planta['id']) ?>" method="POST" class="d-inline hc-form-riego">
                                            <button type="submit" class="hc-btn <?= $planta['necesita_riego'] ? 'hc-btn-agua' : 'hc-btn-secundario' ?> py-1 px-2">
                                                <i class="bi bi-droplet-fill"></i> Regar
                                            </button>
                                        </form>
                                        <?php if ($planta['alerta_cosecha'] || isset($kgCosechados[$planta['id']])): ?>
                                            <a href="<?= base_url('huerto/cosechar/' . $planta['id']) ?>" class="hc-btn hc-btn-cosecha py-1 px-2">
                                                Cosechar
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <form action="<?= base_url('huerto/eliminar/' . $planta['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que querés eliminar este cultivo?')">
                                        <button type="submit" class="hc-btn hc-btn-secundario py-1 px-2 text-danger" aria-label="Eliminar <?= esc($planta['nombre_planta'], 'attr') ?>" title="Eliminar">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="estado-vacio sin-resultados mt-2" id="sinResultados">
            <i class="bi bi-search"></i>
            <h6 class="fw-semibold mt-3 mb-1">No hay cultivos que coincidan</h6>
            <p class="text-muted small mb-0">Probá con otro filtro u otra búsqueda.</p>
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.addEventListener('pageshow', e => { if (e.persisted) location.reload(); });

    (function () {
        // Evita doble envío accidental al registrar un riego
        document.querySelectorAll('.hc-form-riego').forEach(form => {
            form.addEventListener('submit', e => {
                const btn = form.querySelector('button[type="submit"]');
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }
                btn.disabled = true;
            });
        });

        const grilla = document.getElementById('grillaCultivos');
        const tabla = document.getElementById('tablaCultivos');
        if (!grilla) return;

        const columnas = [...grilla.querySelectorAll('.cultivo-col')];
        const filas = tabla ? [...tabla.querySelectorAll('.cultivo-fila')] : [];
        const botonesFiltro = [...document.querySelectorAll('.filtro-btn')];
        const botonesVista = [...document.querySelectorAll('[data-vista]')];
        const buscador = document.getElementById('buscarCultivo');
        const sinResultados = document.getElementById('sinResultados');
        let filtro = 'todos';

        const coincide = el => {
            const texto = buscador.value.trim().toLowerCase();
            const enFiltro = filtro === 'todos' || (filtro === 'riego' ? el.dataset.riego === '1' : el.dataset.estado === filtro);
            return enFiltro && (!texto || el.dataset.texto.includes(texto));
        };

        const aplicar = () => {
            let visibles = 0;
            columnas.forEach(col => {
                const mostrar = coincide(col);
                col.hidden = !mostrar;
                if (mostrar) visibles++;
            });
            filas.forEach(fila => {
                fila.hidden = !coincide(fila);
            });
            sinResultados.classList.toggle('visible', visibles === 0);
        };

        botonesFiltro.forEach(b => b.addEventListener('click', () => {
            filtro = b.dataset.filtro;
            botonesFiltro.forEach(item => {
                const activo = item.dataset.filtro === filtro;
                item.classList.toggle('activo', activo);
                item.setAttribute('aria-pressed', activo);
            });
            aplicar();
        }));

        buscador.addEventListener('input', aplicar);

        // Cambia entre la vista de tarjetas y la de tabla
        const cambiarVista = modo => {
            const esTabla = modo === 'tabla';
            grilla.hidden = esTabla;
            if (tabla) tabla.hidden = !esTabla;
            botonesVista.forEach(b => {
                const activo = b.dataset.vista === modo;
                b.classList.toggle('activo', activo);
                b.setAttribute('aria-pressed', activo);
            });
            try { localStorage.setItem('hc_vista_cultivos', modo); } catch (e) {}
        };

        botonesVista.forEach(b => b.addEventListener('click', () => cambiarVista(b.dataset.vista)));
        try {
            const guardada = localStorage.getItem('hc_vista_cultivos');
            if (guardada === 'tabla') cambiarVista('tabla');
        } catch (e) {}
    })();
</script>
<?= $this->endSection() ?>
