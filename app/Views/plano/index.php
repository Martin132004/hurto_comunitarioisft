<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .plano {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 1rem;
        align-items: start;
    }
    @media (max-width: 1199.98px) {
        .plano { grid-template-columns: minmax(0, 1fr); }
    }

    /* ---------- Escena ---------- */
    .escena {
        position: relative;
        border-radius: calc(var(--radio) + 4px);
        overflow: hidden;
        border: 1px solid var(--borde);
        box-shadow: var(--sombra-1);
        background: #cfe3ee;
        height: min(72vh, 680px);
        min-height: 420px;
        touch-action: none;
    }
    .escena canvas { display: block; outline: none; }
    .escena.moviendo, .escena.moviendo canvas { cursor: grabbing; }
    .escena-cargando {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: .6rem;
        color: var(--texto-2);
        font-size: .9rem;
    }

    .escena-barra {
        position: absolute;
        top: .75rem;
        left: .75rem;
        right: .75rem;
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        pointer-events: none;
        z-index: 2;
    }
    .escena-barra > * { pointer-events: auto; }
    .grupo-botones {
        display: inline-flex;
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(8px);
        border: 1px solid var(--borde);
        border-radius: var(--radio-sm);
        padding: 3px;
        gap: 2px;
        box-shadow: var(--sombra-1);
    }
    .grupo-botones button {
        border: 0;
        background: transparent;
        border-radius: 7px;
        padding: .35rem .6rem;
        font-size: .8rem;
        font-weight: 500;
        color: var(--texto-2);
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        white-space: nowrap;
    }
    .grupo-botones button:hover { background: var(--fondo); color: var(--texto); }
    .grupo-botones button.activo { background: var(--at-600); color: #fff; }
    .grupo-botones button:disabled { opacity: .4; }

    .escena-leyenda {
        position: absolute;
        left: .75rem;
        bottom: .75rem;
        z-index: 2;
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(8px);
        border: 1px solid var(--borde);
        border-radius: var(--radio-sm);
        padding: .5rem .7rem;
        font-size: .75rem;
        color: var(--texto-2);
        max-width: calc(100% - 1.5rem);
    }
    .escena-leyenda:empty { display: none; }
    .escena-leyenda .escala {
        height: 8px;
        width: 160px;
        border-radius: 4px;
        margin: .3rem 0 .15rem;
    }

    .escena-sol {
        position: absolute;
        right: .75rem;
        bottom: .75rem;
        z-index: 2;
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(8px);
        border: 1px solid var(--borde);
        border-radius: var(--radio-sm);
        padding: .4rem .7rem;
        font-size: .75rem;
        color: var(--texto-2);
        font-variant-numeric: tabular-nums;
    }

    /* Etiquetas sobre los canteros */
    .etiqueta-3d {
        font-family: 'Geist', sans-serif;
        font-size: 11px;
        font-weight: 600;
        color: var(--texto);
        background: rgba(255, 255, 255, .9);
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 6px;
        padding: 2px 6px;
        white-space: nowrap;
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
    }
    .etiqueta-3d .avisos { display: inline-flex; gap: 2px; }
    .etiqueta-3d.seleccionada { background: var(--at-600); color: #fff; border-color: var(--at-700); }
    .etiquetas-ocultas .etiqueta-3d { display: none; }

    /* ---------- Línea de tiempo ---------- */
    .tiempo {
        margin-top: 1rem;
        background: var(--superficie);
        border: 1px solid var(--borde);
        border-radius: var(--radio);
        padding: .85rem 1.1rem;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: .4rem 1rem;
        align-items: center;
        box-shadow: var(--sombra-1);
    }
    .tiempo label { font-size: .8rem; font-weight: 600; white-space: nowrap; margin: 0; }
    .tiempo output { font-size: .8rem; color: var(--texto-2); white-space: nowrap; font-variant-numeric: tabular-nums; min-width: 9.5rem; text-align: right; }
    .tiempo input[type=range] { accent-color: var(--at-600); width: 100%; }
    @media (max-width: 575.98px) {
        .tiempo { grid-template-columns: 1fr auto; }
        .tiempo input[type=range] { grid-column: 1 / -1; grid-row: auto; }
    }

    /* ---------- Panel lateral ---------- */
    .panel-plano {
        background: var(--superficie);
        border: 1px solid var(--borde);
        border-radius: calc(var(--radio) + 4px);
        box-shadow: var(--sombra-1);
        overflow: hidden;
    }
    @media (min-width: 1200px) {
        .panel-plano {
            position: sticky;
            top: 80px;
            max-height: calc(100vh - 96px);
            display: flex;
            flex-direction: column;
        }
        .panel-plano .pestanas-cuerpo { overflow-y: auto; }
    }
    .pestanas {
        display: flex;
        border-bottom: 1px solid var(--borde);
        background: var(--fondo);
        overflow-x: auto;
        scrollbar-width: none;
    }
    .pestanas button {
        flex: 1;
        border: 0;
        background: transparent;
        padding: .7rem .5rem;
        font-size: .78rem;
        font-weight: 600;
        color: var(--texto-2);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .2rem;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
    }
    .pestanas button i { font-size: 1.05rem; }
    .pestanas button.activa { color: var(--at-700); border-bottom-color: var(--at-500); background: var(--superficie); }
    .pestanas-cuerpo { padding: 1.1rem; }
    .pestana { display: none; }
    .pestana.activa { display: block; animation: subeAparece .35s var(--ease); }

    .subtitulo {
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--texto-3);
        margin: 1.1rem 0 .5rem;
    }
    .subtitulo:first-child { margin-top: 0; }

    .paleta {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: .4rem;
    }
    .paleta button {
        border: 1px solid var(--borde);
        background: var(--superficie);
        border-radius: var(--radio-sm);
        padding: .55rem .25rem;
        font-size: .72rem;
        font-weight: 500;
        color: var(--texto-2);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .25rem;
        line-height: 1.15;
        transition: border-color .2s, background .2s, transform .15s;
    }
    .paleta button i { font-size: 1.15rem; color: var(--at-600); }
    .paleta button:hover { border-color: var(--at-300); background: var(--at-50); color: var(--texto); }
    .paleta button:active { transform: scale(.96); }

    .tarjeta-sel {
        border: 1px solid var(--borde);
        border-radius: var(--radio);
        padding: .9rem;
        background: var(--fondo);
    }
    .tarjeta-sel .form-control, .tarjeta-sel .form-select { padding: .42rem .6rem; font-size: .85rem; }
    .tarjeta-sel .form-label { font-size: .75rem; margin-bottom: .2rem; }

    .lista-plano { list-style: none; padding: 0; margin: 0; }
    .lista-plano li {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .55rem 0;
        border-top: 1px dashed var(--borde);
        font-size: .85rem;
    }
    .lista-plano li:first-child { border-top: 0; }
    .lista-plano .emoji { font-size: 1.2rem; width: 1.6rem; text-align: center; flex-shrink: 0; }
    .lista-plano .detalle { font-size: .75rem; color: var(--texto-2); }
    .lista-plano .form-select { font-size: .8rem; padding: .3rem 1.8rem .3rem .5rem; max-width: 145px; }

    .aviso {
        display: flex;
        gap: .55rem;
        font-size: .82rem;
        padding: .55rem .7rem;
        border-radius: var(--radio-sm);
        margin-bottom: .4rem;
        line-height: 1.35;
    }
    .aviso i { flex-shrink: 0; margin-top: .1rem; }
    .aviso.ok     { background: var(--at-50); color: var(--at-900); }
    .aviso.info   { background: var(--agua-100); color: #0f4675; }
    .aviso.alerta { background: var(--cosecha-100); color: #7a3d06; }
    .aviso.grave  { background: var(--alerta-100); color: #7d2118; }

    /* Calendario del cantero */
    .gantt { font-size: .72rem; }
    .gantt-meses {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        color: var(--texto-3);
        text-align: center;
        margin-bottom: .3rem;
    }
    .gantt-fila {
        position: relative;
        height: 22px;
        background: repeating-linear-gradient(90deg, var(--fondo) 0, var(--fondo) calc(100% / 12 - 1px), var(--borde-suave) calc(100% / 12 - 1px), var(--borde-suave) calc(100% / 12));
        border-radius: 5px;
        margin-bottom: 4px;
    }
    .gantt-barra {
        position: absolute;
        top: 2px;
        bottom: 2px;
        border-radius: 4px;
        padding: 0 5px;
        color: #fff;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 18px;
        min-width: 4px;
    }
    .gantt-barra.actual { background: var(--at-600); }
    .gantt-barra.plan { background: repeating-linear-gradient(45deg, #5c8fc4, #5c8fc4 6px, #4c80b7 6px, #4c80b7 12px); }
    .gantt-hoy { position: absolute; top: -3px; bottom: -3px; width: 2px; background: var(--alerta-600); border-radius: 1px; }

    .medidor {
        height: 8px;
        border-radius: 4px;
        background: var(--borde-suave);
        overflow: hidden;
        margin: .35rem 0 .2rem;
    }
    .medidor > span { display: block; height: 100%; border-radius: 4px; background: var(--agua-600); transition: width .4s var(--ease); }

    .brujula {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 1px solid var(--borde);
        background: var(--fondo);
        position: relative;
        flex-shrink: 0;
    }
    .brujula span {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 3px;
        height: 26px;
        margin-left: -1.5px;
        margin-top: -26px;
        background: linear-gradient(var(--alerta-600) 50%, transparent 50%);
        transform-origin: 50% 100%;
        border-radius: 2px;
    }
    .brujula b { position: absolute; top: 2px; left: 50%; transform: translateX(-50%); font-size: .62rem; color: var(--texto-3); }

    .estado-guardado { font-size: .75rem; color: var(--texto-3); }
    .estado-guardado.pendiente { color: var(--cosecha-600); }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<section class="huerto-hero mb-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <a href="<?= base_url('huerto/configuracion') ?>" class="badge-fecha d-inline-flex align-items-center gap-2 text-decoration-none">
                    <i class="bi bi-geo-alt"></i> <?= $zona ? esc($zona['nombre']) : 'Elegí la zona de tu huerto' ?>
                </a>
                <span class="badge-fecha d-inline-flex align-items-center gap-2"><i class="bi bi-droplet"></i> <?= esc($riego['metodo']) ?></span>
            </div>
            <h1 class="mb-1">Huerto 3D</h1>
            <p class="mb-0 opacity-75">Dibujá tu huerto, ubicá los cultivos, planificá las próximas siembras y mirá cómo le pega el sol, el viento y cuánta agua necesita cada cantero.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-light" id="btnImagen"><i class="bi bi-image me-1"></i> Descargar imagen</button>
            <button type="button" class="btn btn-huerto" id="btnGuardar"><i class="bi bi-cloud-check me-1"></i> Guardar plano</button>
        </div>
    </div>
</section>

<div class="plano">
    <div>
        <div class="escena" id="escena">
            <div class="escena-cargando" id="cargando">
                <div class="spinner-border text-success" role="status"></div>
                Armando tu huerto…
            </div>

            <div class="escena-barra">
                <div class="grupo-botones" role="group" aria-label="Vista">
                    <button type="button" data-vista="3d" class="activo"><i class="bi bi-box"></i> 3D</button>
                    <button type="button" data-vista="planta"><i class="bi bi-grid-3x3"></i> Planta</button>
                </div>
                <div class="grupo-botones" role="group" aria-label="Capa">
                    <button type="button" data-capa="normal" class="activo"><i class="bi bi-flower1"></i> Cultivos</button>
                    <button type="button" data-capa="sol"><i class="bi bi-sun"></i> Sol</button>
                    <button type="button" data-capa="agua"><i class="bi bi-droplet"></i> Agua</button>
                    <button type="button" data-capa="viento"><i class="bi bi-wind"></i> Viento</button>
                </div>
                <div class="grupo-botones ms-auto" role="group" aria-label="Opciones">
                    <button type="button" id="btnGrilla" class="activo" title="Grilla de 1 m"><i class="bi bi-grid"></i></button>
                    <button type="button" id="btnEtiquetas" class="activo" title="Nombres"><i class="bi bi-tag"></i></button>
                    <button type="button" id="btnSombras" class="activo" title="Sombras"><i class="bi bi-brightness-alt-high"></i></button>
                    <button type="button" id="btnCentrar" title="Centrar vista"><i class="bi bi-fullscreen"></i></button>
                </div>
            </div>

            <div class="escena-leyenda" id="leyenda"></div>
            <div class="escena-sol" id="infoSol"></div>
        </div>

        <div class="tiempo">
            <label for="rangoFecha"><i class="bi bi-calendar3 me-1"></i> Fecha</label>
            <input type="range" id="rangoFecha" min="0" max="365" value="0" step="1">
            <output id="salidaFecha"></output>
            <label for="rangoHora"><i class="bi bi-clock me-1"></i> Hora</label>
            <input type="range" id="rangoHora" min="5" max="21" value="11" step="0.25">
            <output id="salidaHora"></output>
        </div>
        <p class="small text-muted mt-2 mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Mové la fecha para ver cómo van a estar los cultivos y las siembras planificadas. Clic en un elemento para elegirlo; arrastralo para moverlo. Con <kbd>R</kbd> lo girás y con <kbd>Supr</kbd> lo borrás.
        </p>
    </div>

    <aside class="panel-plano">
        <div class="pestanas" role="tablist">
            <button type="button" class="activa" data-pestana="diseno"><i class="bi bi-pencil-square"></i> Diseño</button>
            <button type="button" data-pestana="cultivos"><i class="bi bi-flower2"></i> Cultivos</button>
            <button type="button" data-pestana="planificar"><i class="bi bi-calendar2-week"></i> Planificar</button>
            <button type="button" data-pestana="analisis"><i class="bi bi-clipboard2-pulse"></i> Análisis</button>
            <button type="button" data-pestana="terreno"><i class="bi bi-compass"></i> Terreno</button>
        </div>

        <div class="pestanas-cuerpo">
            <!-- Diseño: agregar y editar elementos -->
            <div class="pestana activa" id="pestana-diseno">
                <div class="subtitulo">Agregar al huerto</div>
                <div class="paleta" id="paleta"></div>

                <div class="subtitulo">Elemento elegido</div>
                <div id="seleccion"></div>

                <div id="plantillas" class="mt-3"></div>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <span class="estado-guardado" id="estadoGuardado"></span>
                </div>
            </div>

            <!-- Cultivos: en qué cantero está cada uno -->
            <div class="pestana" id="pestana-cultivos">
                <p class="small text-muted">Ubicá cada cultivo en su cantero. Los que no ubiques no se ven en el plano.</p>
                <ul class="lista-plano" id="listaCultivos"></ul>
            </div>

            <!-- Planificar: próximas siembras por cantero -->
            <div class="pestana" id="pestana-planificar">
                <div id="planificar"></div>
            </div>

            <!-- Análisis: sol, viento, agua y avisos -->
            <div class="pestana" id="pestana-analisis">
                <div id="analisis"></div>
            </div>

            <!-- Terreno: medidas, orientación, ubicación y viento -->
            <div class="pestana" id="pestana-terreno">
                <form id="formTerreno" class="row g-2">
                    <div class="col-6">
                        <label class="form-label small">Ancho (m)</label>
                        <input type="number" class="form-control" name="ancho" min="2" max="200" step="0.5">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Largo (m)</label>
                        <input type="number" class="form-control" name="largo" min="2" max="200" step="0.5">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label small">¿Hacia dónde queda el norte?</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="brujula" aria-hidden="true"><b>N</b><span id="agujaNorte"></span></div>
                            <div class="flex-grow-1">
                                <input type="range" class="form-range" name="norte" min="0" max="359" step="1">
                                <div class="form-text" id="textoNorte"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label small">Viento fuerte de la zona</label>
                        <select class="form-select" name="viento">
                            <option value="">No lo tengo en cuenta</option>
                            <option value="0">Del norte</option>
                            <option value="45">Del noreste</option>
                            <option value="90">Del este</option>
                            <option value="135">Del sureste</option>
                            <option value="180">Del sur</option>
                            <option value="225">Del suroeste</option>
                            <option value="270">Del oeste</option>
                            <option value="315">Del noroeste</option>
                        </select>
                        <div class="form-text">Se usa para ver qué canteros quedan sin reparo.</div>
                    </div>
                    <div class="col-6 mt-3">
                        <label class="form-label small">Latitud</label>
                        <input type="number" class="form-control" name="latitud" min="-60" max="60" step="0.0001">
                    </div>
                    <div class="col-6 mt-3">
                        <label class="form-label small">Longitud</label>
                        <input type="number" class="form-control" name="longitud" min="-180" max="180" step="0.0001">
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-light btn-sm mt-1" id="btnUbicacion"><i class="bi bi-crosshair me-1"></i> Usar mi ubicación</button>
                        <div class="form-text">La ubicación sirve para calcular el recorrido del sol. Se guarda solo en tu huerto.</div>
                    </div>
                </form>
            </div>
        </div>
    </aside>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php $json = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE; ?>
<script type="application/json" id="datosPlano"><?= json_encode([
    'huerto'       => $huerto,
    'zona'         => $zona,
    'tipos'        => $tipos,
    'elementos'    => $elementos,
    'cultivos'     => $cultivos,
    'planificadas' => $planificadas,
    'especies'     => $especies,
    'riego'        => $riego,
    'hoy'          => date('Y-m-d'),
    'urls'         => [
        'guardar'    => base_url('huerto/plano/guardar'),
        'planificar' => base_url('huerto/plano/planificar'),
        'eliminar'   => base_url('huerto/plano/planificar/{id}/eliminar'),
        'sembrar'    => base_url('huerto/crear?plan={id}'),
        'nuevo'      => base_url('huerto/crear?cantero={id}'),
        'regar'      => base_url('huerto/riego/{id}'),
        'cosechar'   => base_url('huerto/cosechar/{id}'),
        'problema'   => base_url('huerto/problemas/nuevo'),
    ],
], $json) ?></script>
<script type="importmap">
{
    "imports": {
        "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
        "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
    }
}
</script>
<script type="module" src="<?= base_url('js/plano3d.js') ?>?v=<?= filemtime(FCPATH . 'js/plano3d.js') ?>"></script>
<?= $this->endSection() ?>
