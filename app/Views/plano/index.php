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

    /* ---------- Plano ---------- */
    .escena {
        border-radius: calc(var(--radio) + 4px);
        border: 1px solid var(--borde);
        box-shadow: var(--sombra-1);
        background: #eef2e6;
        padding: .75rem;
    }
    .escena svg {
        display: block;
        width: 100%;
        height: auto;
        max-height: min(70vh, 680px);
        touch-action: none;
        user-select: none;
        font-family: 'Geist', sans-serif;
        /* Un árbol o un cantero contra el borde puede sobresalir del terreno: se ve entero */
        overflow: visible;
    }
    .escena svg.moviendo, .escena svg.moviendo * { cursor: grabbing !important; }
    .leyenda { font-size: .75rem; color: var(--texto-2); margin-top: .5rem; }
    .leyenda i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }

    /* ---------- Panel lateral ---------- */
    .panel-plano {
        background: var(--superficie);
        border: 1px solid var(--borde);
        border-radius: calc(var(--radio) + 4px);
        box-shadow: var(--sombra-1);
        overflow: hidden;
    }
    @media (min-width: 1200px) {
        .panel-plano { position: sticky; top: 80px; max-height: calc(100vh - 96px); display: flex; flex-direction: column; }
        .panel-plano .pestanas-cuerpo { overflow-y: auto; }
    }
    .pestanas { display: flex; border-bottom: 1px solid var(--borde); background: var(--fondo); }
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
    }
    .pestanas button i { font-size: 1.05rem; }
    .pestanas button.activa { color: var(--at-700); border-bottom-color: var(--at-500); background: var(--superficie); }
    .pestanas-cuerpo { padding: 1.1rem; }
    .pestana { display: none; }
    .pestana.activa { display: block; }

    .subtitulo {
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--texto-3);
        margin: 1.1rem 0 .5rem;
    }
    .subtitulo:first-child { margin-top: 0; }

    .paleta { display: grid; grid-template-columns: repeat(3, 1fr); gap: .4rem; }
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
    }
    .paleta button i { font-size: 1.15rem; color: var(--at-600); }
    .paleta button:hover { border-color: var(--at-300); background: var(--at-50); color: var(--texto); }

    .tarjeta-sel { border: 1px solid var(--borde); border-radius: var(--radio); padding: .9rem; background: var(--fondo); }
    .tarjeta-sel .form-control { padding: .42rem .6rem; font-size: .85rem; }
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
    /* Casilla para tildar en la lista de tareas */
    .lista-plano .check { width: 14px; height: 14px; border: 1.5px solid var(--texto-3); border-radius: 3px; flex-shrink: 0; }

    .aviso { display: flex; gap: .55rem; font-size: .82rem; padding: .55rem .7rem; border-radius: var(--radio-sm); margin-top: .4rem; line-height: 1.35; }
    .aviso.info   { background: var(--agua-100); color: #0f4675; }
    .aviso.alerta { background: var(--cosecha-100); color: #7a3d06; }
    .aviso.grave  { background: var(--alerta-100); color: #7d2118; }

    .estado-guardado { font-size: .75rem; color: var(--texto-3); }
    .estado-guardado.pendiente { color: var(--cosecha-600); }

    /* Hoja para imprimir: solo se ve al imprimir */
    #hojaImpresion { display: none; }
    @media print {
        body * { visibility: hidden; }
        #hojaImpresion, #hojaImpresion * { visibility: visible; }
        /* Sin animación: al pasar de oculto a visible arrancaría transparente y saldría en blanco */
        #hojaImpresion { display: block; position: absolute; left: 0; top: 0; width: 100%; font-size: 12px; animation: none !important; opacity: 1 !important; }
        #hojaImpresion h2 { font-size: 18px; margin: 0 0 8px; }
        #hojaImpresion h3 { font-size: 15px; margin: 14px 0 4px; }
        #hojaImpresion svg { width: 100%; height: auto; max-height: 60vh; overflow: visible; }
        #hojaImpresion .lista-plano li { padding: 3px 0; break-inside: avoid; }
    }
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
            <h1 class="mb-1">Plano del huerto</h1>
            <p class="mb-0 opacity-75">Dibujá tu huerto visto desde arriba, ubicá cada cultivo en su cantero y mirá qué hay que hacer hoy.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-light" id="btnImprimir"><i class="bi bi-printer me-1"></i> Imprimir con tareas</button>
            <button type="button" class="btn btn-huerto" id="btnGuardar"><i class="bi bi-cloud-check me-1"></i> Guardar plano</button>
        </div>
    </div>
</section>

<div class="plano">
    <div>
        <div class="escena">
            <svg id="plano" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Plano del huerto visto desde arriba"></svg>
            <div class="leyenda" id="leyenda">
                Borde del cantero:
                <i style="background:#3fa34d"></i> bien
                <i style="background:#f0a020"></i> atención
                <i style="background:#d64545"></i> urgente
                · 💧 regar · 🧺 cosechar · 🐛 problema
            </div>
        </div>
        <p class="small text-muted mt-2 mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Clic en un elemento para elegirlo y arrastralo para moverlo. Cada cultivo se dibuja a su distancia de plantación.
        </p>
    </div>

    <aside class="panel-plano">
        <div class="pestanas" role="tablist">
            <button type="button" class="activa" data-pestana="hoy"><i class="bi bi-check2-square"></i> Hoy</button>
            <button type="button" data-pestana="diseno"><i class="bi bi-pencil-square"></i> Diseño</button>
            <button type="button" data-pestana="cultivos"><i class="bi bi-flower2"></i> Cultivos</button>
            <button type="button" data-pestana="terreno"><i class="bi bi-rulers"></i> Terreno</button>
        </div>

        <div class="pestanas-cuerpo">
            <!-- Hoy: tareas del día, también van en la hoja impresa -->
            <div class="pestana activa" id="pestana-hoy"><div id="hoy"></div></div>

            <!-- Diseño: agregar y editar elementos -->
            <div class="pestana" id="pestana-diseno">
                <div class="subtitulo">Agregar al huerto</div>
                <div class="paleta" id="paleta"></div>
                <div class="subtitulo">Elemento elegido</div>
                <div id="seleccion"></div>
                <div class="estado-guardado mt-3" id="estadoGuardado"></div>
            </div>

            <!-- Cultivos: en qué cantero está cada uno -->
            <div class="pestana" id="pestana-cultivos">
                <p class="small text-muted">Ubicá cada cultivo en su cantero. Los que no ubiques no se ven en el plano.</p>
                <ul class="lista-plano" id="listaCultivos"></ul>
            </div>

            <!-- Terreno: medidas -->
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
                </form>
            </div>
        </div>
    </aside>
</div>

<div id="hojaImpresion"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="application/json" id="datosPlano"><?= json_encode([
    'huerto'    => $huerto,
    'zona'      => $zona,
    'tipos'     => $tipos,
    'elementos' => $elementos,
    'cultivos'  => $cultivos,
    'especies'  => $especies,
    'riego'     => $riego,
    'hoy'       => date('Y-m-d'),
    'urls'      => [
        'guardar'  => base_url('huerto/plano/guardar'),
        // {id} va fuera de base_url(): adentro se codifica como %7Bid%7D y el JS no lo reemplaza
        'nuevo'    => base_url('huerto/crear') . '?cantero={id}',
        'regar'    => base_url('huerto/riego') . '/{id}',
        'cosechar' => base_url('huerto/cosechar') . '/{id}',
    ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
<script type="module" src="<?= base_url('js/plano2d.js') ?>?v=<?= filemtime(FCPATH . 'js/plano2d.js') ?>"></script>
<?= $this->endSection() ?>
