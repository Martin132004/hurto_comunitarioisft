<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .riesgo-card {
        background: var(--hc-papel);
        border-radius: 8px;
        border: 1px solid var(--hc-borde);
        border-top: 3px solid var(--hc-cosecha);
        padding: 1rem 1.15rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .riesgo-card .nombre {
        font-family: var(--hc-serif);
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--hc-tinta);
    }
    .riesgo-card ul {
        padding-left: 1.1rem;
        margin-bottom: .75rem;
    }
    .reporte-item {
        display: flex;
        gap: 1rem;
        align-items: center;
        background: var(--hc-papel);
        border-radius: 6px;
        border: 1px solid var(--hc-borde);
        border-left: 4px solid var(--hc-hoja);
        padding: .85rem 1.05rem;
        color: inherit;
        text-decoration: none;
        transition: background .15s, border-color .15s;
    }
    .reporte-item:hover {
        background: var(--hc-lienzo);
        border-color: var(--hc-borde-fuerte);
        border-left-color: var(--hc-hoja);
    }
    .reporte-miniatura {
        width: 58px;
        height: 58px;
        border-radius: 6px;
        border: 1px solid var(--hc-borde);
        object-fit: cover;
        flex-shrink: 0;
        background: var(--hc-salvia);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: var(--hc-hoja);
    }
    .catalogo .accordion-item {
        border: 1px solid var(--hc-borde);
        border-radius: 6px !important;
        overflow: hidden;
        margin-bottom: .5rem;
        background: var(--hc-papel);
    }
    .catalogo .accordion-button {
        font-family: var(--hc-serif);
        font-weight: 700;
        font-size: 1rem;
        color: var(--hc-tinta);
        padding: .85rem 1.1rem;
        background: var(--hc-papel);
    }
    .catalogo .accordion-button:not(.collapsed) {
        background: var(--hc-salvia);
        color: var(--hc-tinta);
        box-shadow: none;
        border-bottom: 1px solid var(--hc-borde);
    }
    .catalogo-columna {
        background: var(--hc-lienzo);
        border: 1px solid var(--hc-borde-fino);
        border-radius: 6px;
        padding: .75rem .9rem;
        height: 100%;
    }
    .catalogo-subtitulo {
        font-family: var(--hc-serif);
        font-size: .88rem;
        font-weight: 700;
        color: var(--hc-tinta);
        padding-bottom: .3rem;
        margin-bottom: .45rem;
        border-bottom: 1px solid var(--hc-borde);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $meses = \App\Libraries\Fechas::MESES;
    $nombreProblema = fn ($clave) => $catalogo[$clave]['nombre'] ?? 'Sin identificar';
?>

<section class="huerto-hero mb-4">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <span class="badge-fecha mb-2">
                <i class="bi bi-geo-alt text-success"></i> <?= $zona ? esc($zona['nombre']) : 'Sin zona configurada' ?>
            </span>
            <h1 class="mb-1">Problemas y plagas</h1>
            <p class="mb-0">Reportá lo que ves en tus plantas, con foto, y el técnico te responde con qué hacer.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="<?= base_url('huerto/problemas/nuevo') ?>" class="btn btn-huerto">
                <i class="bi bi-camera me-1"></i> Reportar problema
            </a>
        </div>
    </div>
</section>

<?php foreach ($repetidos as $clave => $cultivos): ?>
    <div class="alert alert-warning d-flex gap-2 align-items-start mb-3">
        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
        <div>
            <strong><?= esc($nombreProblema($clave)) ?> en varios cultivos:</strong> <?= esc($cultivos) ?>.
            Puede estar extendiéndose por el huerto. <a href="#problema-<?= esc($clave) ?>" class="alert-link">Ver qué hacer</a>
        </div>
    </div>
<?php endforeach; ?>

<?php if (! empty($vigilar)): ?>
    <div class="mb-4 mt-4">
        <h5 class="seccion-titulo mb-1"><i class="bi bi-binoculars me-2"></i>Vigilá en <?= $meses[(int) date('n') - 1] ?></h5>
        <p class="text-muted small mb-3">Problemas frecuentes en esta época para los cultivos que tenés en el huerto.</p>
        <div class="row g-3">
            <?php foreach ($vigilar as $clave => $afectados): ?>
                <?php $p = $catalogo[$clave]; $tipo = $tipos[$p['tipo']] ?? $tipos['plaga']; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="riesgo-card">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                            <span class="nombre" style="flex: 1 1 11rem;"><?= esc($p['nombre']) ?></span>
                            <span class="badge text-bg-<?= $tipo['color'] ?>"><i class="bi <?= $tipo['icono'] ?> me-1"></i><?= esc($tipo['nombre']) ?></span>
                        </div>
                        <div class="small text-muted mb-2">En: <?= esc(implode(', ', $afectados)) ?></div>
                        <div class="small fw-semibold mb-1">Qué buscar</div>
                        <ul class="small text-muted">
                            <?php foreach (array_slice($p['sintomas'], 0, 2) as $sintoma): ?>
                                <li><?= esc($sintoma) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="#problema-<?= esc($clave) ?>" class="small text-success fw-semibold text-decoration-none mt-auto">
                            Cómo prevenirlo <i class="bi bi-arrow-down-short"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 mt-4">
    <h5 class="seccion-titulo mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Mis reportes</h5>
    <div class="btn-group btn-group-sm" role="group" aria-label="Filtrar reportes por estado">
        <a href="<?= base_url('huerto/problemas') ?>" class="btn <?= $filtro === 'pendientes' ? 'btn-huerto' : 'btn-outline-success' ?>">Sin resolver</a>
        <a href="<?= base_url('huerto/problemas?ver=resueltos') ?>" class="btn <?= $filtro === 'resueltos' ? 'btn-huerto' : 'btn-outline-success' ?>">Resueltos</a>
    </div>
</div>

<?php if (empty($reportes)): ?>
    <div class="estado-vacio mb-5">
        <i class="bi bi-shield-check"></i>
        <h5 class="fw-bold mt-2 mb-1"><?= $filtro === 'resueltos' ? 'Todavía no hay problemas resueltos.' : 'No tenés problemas sin resolver.' ?></h5>
        <p class="text-muted small mb-0">Si ves hojas comidas, manchas o bichos, reportalo con una foto.</p>
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
                    <div class="fw-bold" style="font-family: var(--hc-serif); font-size: 1.02rem; color: var(--hc-tinta);">
                        <?= esc($nombreProblema($r['problema'])) ?>
                        <?php if ($r['cultivo_nombre']): ?><span class="fw-normal text-muted" style="font-family: var(--hc-sans); font-size: .86rem;">· <?= esc($r['cultivo_nombre']) ?></span><?php endif; ?>
                    </div>
                    <div class="small text-muted text-truncate"><?= esc($r['descripcion'] ?? 'Sin notas adicionales') ?></div>
                    <div class="small mt-1 d-flex flex-wrap gap-1">
                        <span class="badge text-bg-light"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                        <span class="badge bg-<?= $gravedad['color'] ?>-subtle text-<?= $gravedad['color'] ?>-emphasis border"><?= $gravedad['nombre'] ?></span>
                    </div>
                </div>
                <span class="badge text-bg-<?= $estado['color'] ?> flex-shrink-0"><i class="bi <?= $estado['icono'] ?> me-1"></i><span class="d-none d-sm-inline"><?= $estado['nombre'] ?></span></span>
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
                        <span class="badge text-bg-<?= $tipo['color'] ?>"><i class="bi <?= $tipo['icono'] ?>"></i></span>
                        <?= esc($p['nombre']) ?>
                        <span class="small text-muted fw-normal d-none d-md-inline" style="font-family: var(--hc-sans);">— <?= $tipo['nombre'] ?></span>
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
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="catalogo-columna">
                                    <div class="catalogo-subtitulo"><i class="bi bi-eye me-1 text-secondary"></i>Cómo reconocerlo</div>
                                    <ul class="small mb-0 ps-3"><?php foreach ($p['sintomas'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="catalogo-columna">
                                    <div class="catalogo-subtitulo"><i class="bi bi-prescription2 me-1 text-success"></i>Qué hacer</div>
                                    <ul class="small mb-0 ps-3"><?php foreach ($p['manejo'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="catalogo-columna">
                                    <div class="catalogo-subtitulo"><i class="bi bi-shield-check me-1 text-primary"></i>Cómo prevenirlo</div>
                                    <ul class="small mb-0 ps-3"><?php foreach ($p['prevencion'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                                </div>
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
