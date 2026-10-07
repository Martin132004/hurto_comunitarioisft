<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .riesgo-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid rgba(15, 61, 46, .06);
        box-shadow: 0 4px 16px -8px rgba(15, 61, 46, .15);
        padding: 1.1rem 1.25rem;
        height: 100%;
    }
    .riesgo-card .nombre {
        font-weight: 700;
        color: var(--huerto-verde-oscuro);
    }
    .riesgo-card ul {
        padding-left: 1.1rem;
        margin-bottom: 0;
    }
    .reporte-item {
        display: flex;
        gap: 1rem;
        align-items: center;
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid rgba(15, 61, 46, .06);
        box-shadow: 0 4px 16px -8px rgba(15, 61, 46, .15);
        padding: .9rem 1.1rem;
        color: inherit;
        text-decoration: none;
        transition: transform .15s, box-shadow .15s;
    }
    .reporte-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px -12px rgba(15, 61, 46, .35);
    }
    .reporte-miniatura {
        width: 64px;
        height: 64px;
        border-radius: 1rem;
        object-fit: cover;
        flex-shrink: 0;
        background: var(--huerto-crema);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: var(--huerto-verde);
    }
    .catalogo .accordion-item {
        border: 0;
        border-radius: 1rem !important;
        overflow: hidden;
        margin-bottom: .6rem;
        box-shadow: 0 4px 16px -10px rgba(15, 61, 46, .2);
    }
    .catalogo .accordion-button {
        font-weight: 600;
        color: var(--huerto-verde-oscuro);
    }
    .catalogo .accordion-button:not(.collapsed) {
        background: var(--huerto-crema);
        box-shadow: none;
    }
    .catalogo h6 {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
        font-weight: 700;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $nombreProblema = fn ($clave) => $catalogo[$clave]['nombre'] ?? 'Sin identificar';
?>

<section class="huerto-hero mb-4">
    <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
        <div class="col-lg-8">
            <span class="badge-fecha d-inline-flex align-items-center gap-2 mb-3">
                <i class="bi bi-geo-alt"></i> <?= $zona ? esc($zona['nombre']) : 'Sin zona elegida' ?>
            </span>
            <h1 class="display-6 mb-2">Problemas y plagas</h1>
            <p class="mb-0 opacity-75">Reportá lo que ves en tus plantas, con foto, y el técnico te responde con qué hacer.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="<?= base_url('huerto/problemas/nuevo') ?>" class="btn btn-huerto">
                <i class="bi bi-camera me-1"></i> Reportar problema
            </a>
        </div>
    </div>
</section>

<?php foreach ($brotes as $clave => $huertas): ?>
    <div class="alert alert-danger rounded-4 d-flex gap-2 align-items-start">
        <i class="bi bi-exclamation-octagon-fill mt-1"></i>
        <div>
            <strong>Alerta en tu zona: <?= esc($nombreProblema($clave)) ?>.</strong>
            Lo reportaron <?= $huertas ?> huertas de <?= esc($zona['nombre'] ?? 'tu zona') ?> en los últimos <?= \App\Models\ReporteProblemaModel::BROTE_DIAS ?> días.
            Revisá tus plantas y aplicá la prevención. <a href="#problema-<?= esc($clave) ?>" class="alert-link">Ver qué hacer</a>
        </div>
    </div>
<?php endforeach; ?>

<?php foreach ($repetidos as $clave => $cultivos): ?>
    <div class="alert alert-warning rounded-4 d-flex gap-2 align-items-start">
        <i class="bi bi-arrows-angle-expand mt-1"></i>
        <div>
            <strong><?= esc($nombreProblema($clave)) ?> en varios cultivos:</strong> <?= esc($cultivos) ?>.
            Puede estar extendiéndose por el huerto. <a href="#problema-<?= esc($clave) ?>" class="alert-link">Ver qué hacer</a>
        </div>
    </div>
<?php endforeach; ?>

<?php if (! empty($vigilar)): ?>
    <h5 class="seccion-titulo mb-1 mt-4"><i class="bi bi-binoculars me-2"></i>Vigilá en <?= $meses[(int) date('n') - 1] ?></h5>
    <p class="text-muted small mb-3">Problemas frecuentes en esta época para los cultivos que tenés en el huerto.</p>
    <div class="row g-3 mb-5">
        <?php foreach ($vigilar as $clave => $afectados): ?>
            <?php $p = $catalogo[$clave]; $tipo = $tipos[$p['tipo']] ?? $tipos['plaga']; ?>
            <div class="col-md-6 col-lg-4">
                <div class="riesgo-card">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill text-bg-<?= $tipo['color'] ?>"><i class="bi <?= $tipo['icono'] ?>"></i></span>
                        <span class="nombre"><?= esc($p['nombre']) ?></span>
                    </div>
                    <div class="small text-muted mb-2">En: <?= esc(implode(', ', $afectados)) ?></div>
                    <div class="small fw-semibold">Qué buscar</div>
                    <ul class="small text-muted mb-2">
                        <?php foreach (array_slice($p['sintomas'], 0, 2) as $sintoma): ?>
                            <li><?= esc($sintoma) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="#problema-<?= esc($clave) ?>" class="small text-success fw-semibold text-decoration-none">Cómo prevenirlo <i class="bi bi-arrow-down-short"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 mt-4">
    <h5 class="seccion-titulo mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Mis reportes</h5>
    <div class="btn-group btn-group-sm" role="group">
        <a href="<?= base_url('huerto/problemas') ?>" class="btn <?= $filtro === 'pendientes' ? 'btn-success' : 'btn-outline-success' ?>">Sin resolver</a>
        <a href="<?= base_url('huerto/problemas?ver=resueltos') ?>" class="btn <?= $filtro === 'resueltos' ? 'btn-success' : 'btn-outline-success' ?>">Resueltos</a>
    </div>
</div>

<?php if (empty($reportes)): ?>
    <div class="estado-vacio mb-5">
        <i class="bi bi-emoji-smile"></i>
        <h5 class="fw-bold mt-3"><?= $filtro === 'resueltos' ? 'Todavía no hay problemas resueltos.' : 'No tenés problemas sin resolver.' ?></h5>
        <p class="text-muted mb-0">Si ves hojas comidas, manchas o bichos, reportalo con una foto.</p>
    </div>
<?php else: ?>
    <div class="d-grid gap-2 mb-5">
        <?php foreach ($reportes as $r): ?>
            <?php $estado = $estados[$r['estado']] ?? $estados['abierto']; $gravedad = $gravedades[$r['gravedad']] ?? $gravedades['pocas']; ?>
            <a href="<?= base_url('huerto/problemas/' . $r['id']) ?>" class="reporte-item">
                <?php if ($r['foto']): ?>
                    <img src="<?= base_url('huerto/problemas/' . $r['id'] . '/foto') ?>" alt="Foto del problema" class="reporte-miniatura" loading="lazy">
                <?php else: ?>
                    <span class="reporte-miniatura"><i class="bi bi-bug"></i></span>
                <?php endif; ?>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold" style="color: var(--huerto-verde-oscuro)">
                        <?= esc($nombreProblema($r['problema'])) ?>
                        <?php if ($r['cultivo_nombre']): ?><span class="fw-normal text-muted">· <?= esc($r['cultivo_nombre']) ?></span><?php endif; ?>
                    </div>
                    <div class="small text-muted text-truncate"><?= esc($r['descripcion'] ?? 'Sin descripción') ?></div>
                    <div class="small mt-1 d-flex flex-wrap gap-1">
                        <span class="badge rounded-pill text-bg-light border"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                        <span class="badge rounded-pill bg-<?= $gravedad['color'] ?>-subtle text-<?= $gravedad['color'] ?>-emphasis"><?= $gravedad['nombre'] ?></span>
                    </div>
                </div>
                <span class="badge rounded-pill text-bg-<?= $estado['color'] ?> flex-shrink-0"><i class="bi <?= $estado['icono'] ?> me-1"></i><span class="d-none d-sm-inline"><?= $estado['nombre'] ?></span></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h5 class="seccion-titulo mb-1"><i class="bi bi-journal-medical me-2"></i>Guía de problemas</h5>
<p class="text-muted small mb-3">Cómo reconocer cada problema y qué hacer, priorizando el manejo agroecológico.</p>

<?php if (empty($catalogo)): ?>
    <p class="text-muted">El catálogo de problemas todavía no está cargado.</p>
<?php else: ?>
    <div class="accordion catalogo" id="catalogo">
        <?php foreach ($catalogo as $clave => $p): ?>
            <?php $tipo = $tipos[$p['tipo']] ?? $tipos['plaga']; ?>
            <div class="accordion-item" id="problema-<?= esc($clave) ?>">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#detalle-<?= esc($clave) ?>">
                        <span class="badge rounded-pill text-bg-<?= $tipo['color'] ?>"><i class="bi <?= $tipo['icono'] ?>"></i></span>
                        <?= esc($p['nombre']) ?>
                        <span class="small text-muted fw-normal d-none d-md-inline">· <?= $tipo['nombre'] ?></span>
                    </button>
                </h2>
                <div id="detalle-<?= esc($clave) ?>" class="accordion-collapse collapse" data-bs-parent="#catalogo">
                    <div class="accordion-body">
                        <p class="small text-muted mb-3">
                            Afecta a: <?= in_array('*', $p['afecta'], true) ? 'todo el huerto' : esc(implode(', ', $p['afecta'])) ?>
                            <?php if ($p['meses']): ?>
                                · Más frecuente en <?= esc(implode(', ', array_map(fn ($m) => $meses[$m - 1], $p['meses']))) ?><?= ($zona['desfase_meses'] ?? 0) > 0 ? ' (en tu zona, un poco más tarde)' : '' ?>
                            <?php endif; ?>
                        </p>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <h6>Cómo reconocerlo</h6>
                                <ul class="small mb-0"><?php foreach ($p['sintomas'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                            </div>
                            <div class="col-md-4">
                                <h6>Qué hacer</h6>
                                <ul class="small mb-0"><?php foreach ($p['manejo'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                            </div>
                            <div class="col-md-4">
                                <h6>Cómo prevenirlo</h6>
                                <ul class="small mb-0"><?php foreach ($p['prevencion'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Al seguir un enlace "Ver qué hacer", abrimos ese problema de la guía
    function abrirProblema() {
        const item = location.hash.startsWith('#problema-') && document.querySelector(location.hash);
        if (item) {
            bootstrap.Collapse.getOrCreateInstance(item.querySelector('.accordion-collapse')).show();
        }
    }
    window.addEventListener('hashchange', abrirProblema);
    abrirProblema();
</script>
<?= $this->endSection() ?>
