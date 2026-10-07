<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .foto-problema {
        width: 100%;
        max-height: 420px;
        object-fit: contain;
        background: #0f3d2e;
        border-radius: 1.25rem;
    }
    .dato {
        background: var(--huerto-crema);
        border-radius: .8rem;
        padding: .6rem .8rem;
        height: 100%;
    }
    .dato .etiqueta {
        font-size: .72rem;
        color: #6c757d;
        font-weight: 500;
    }
    .dato .valor {
        font-weight: 700;
        color: var(--huerto-verde-oscuro);
        font-size: .9rem;
    }
    .respuesta {
        background: #eaf6ef;
        border-left: 4px solid var(--huerto-verde);
        border-radius: 1rem;
        padding: 1.1rem 1.25rem;
        white-space: pre-line;
    }
    .bloque h6 {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: .6rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $estado = $estados[$reporte['estado']] ?? $estados['abierto'];
    $gravedad = $gravedades[$reporte['gravedad']] ?? $gravedades['pocas'];
    $fechaHora = fn ($valor) => date('d/m/Y H:i', strtotime($valor));
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <a href="<?= base_url('huerto/problemas') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Volver a problemas
        </a>

        <?php if (session()->getFlashdata('mensaje')): ?>
            <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i> <?= esc(session()->getFlashdata('mensaje')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i> <?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if ($reporte['problema'] && isset($brotes[$reporte['problema']]) && $reporte['estado'] !== 'resuelto'): ?>
            <div class="alert alert-danger rounded-4">
                <i class="bi bi-exclamation-octagon-fill me-1"></i>
                <strong>No es solo en tu huerto:</strong> <?= $brotes[$reporte['problema']] ?> huertas de <?= esc($zona['nombre'] ?? 'la zona') ?> reportaron lo mismo en los últimos <?= \App\Models\ReporteProblemaModel::BROTE_DIAS ?> días.
            </div>
        <?php endif; ?>

        <div class="panel-form mb-4">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <h4 class="fw-bold mb-1"><?= esc($problema['nombre'] ?? 'Problema sin identificar') ?></h4>
                        <p class="mb-0 opacity-75 small">
                            <?= $reporte['cultivo_nombre'] ? 'En ' . esc($reporte['cultivo_nombre']) : 'Varios cultivos o todo el huerto' ?>
                            · Reportado el <?= $fechaHora($reporte['created_at']) ?>
                        </p>
                    </div>
                    <span class="badge rounded-pill text-bg-<?= $estado['color'] ?> fs-6"><i class="bi <?= $estado['icono'] ?> me-1"></i><?= $estado['nombre'] ?></span>
                </div>
            </div>
            <div class="p-4 p-md-5">
                <div class="row g-4">
                    <?php if ($reporte['foto']): ?>
                        <div class="col-md-6">
                            <a href="<?= base_url('huerto/problemas/' . $reporte['id'] . '/foto') ?>" target="_blank">
                                <img src="<?= base_url('huerto/problemas/' . $reporte['id'] . '/foto') ?>" alt="Foto del problema" class="foto-problema">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="<?= $reporte['foto'] ? 'col-md-6' : 'col-12' ?>">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="dato">
                                    <div class="etiqueta">Cuánto afecta</div>
                                    <div class="valor"><span class="badge rounded-pill text-bg-<?= $gravedad['color'] ?> me-1">&nbsp;</span><?= $gravedad['nombre'] ?></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="dato">
                                    <div class="etiqueta">Tipo</div>
                                    <div class="valor"><?= $problema ? ($tipos[$problema['tipo']]['nombre'] ?? '') : 'A identificar' ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="bloque">
                            <h6>Lo que se ve</h6>
                            <p class="mb-0" style="white-space: pre-line"><?= esc($reporte['descripcion'] ?? 'Sin descripción.') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-form mb-4">
            <div class="p-4 p-md-5">
                <h5 class="seccion-titulo mb-3"><i class="bi bi-person-badge me-2"></i>Respuesta del técnico</h5>

                <?php if ($reporte['respuesta']): ?>
                    <div class="respuesta mb-2"><?= esc($reporte['respuesta']) ?></div>
                    <p class="small text-muted mb-3">
                        <?= esc($reporte['respondido_por']) ?> · <?= $fechaHora($reporte['respondido_at']) ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted"><i class="bi bi-hourglass-split me-1"></i>Todavía no hay respuesta. Mientras tanto, mirá abajo qué podés hacer.</p>
                <?php endif; ?>

                <details <?= $reporte['respuesta'] ? '' : 'open' ?>>
                    <summary class="small fw-semibold text-success mb-3" style="cursor: pointer">
                        <?= $reporte['respuesta'] ? 'Editar la respuesta' : 'Responder (técnico)' ?>
                    </summary>
                    <form action="<?= base_url('huerto/problemas/' . $reporte['id'] . '/responder') ?>" method="POST" class="panel-form shadow-none border-0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="respondido_por">Nombre del técnico</label>
                                <input type="text" name="respondido_por" id="respondido_por" class="form-control" maxlength="100" required value="<?= esc(old('respondido_por', $reporte['respondido_por'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="diagnostico">Diagnóstico</label>
                                <select name="problema" id="diagnostico" class="form-select rounded-3">
                                    <option value="">Sin identificar</option>
                                    <?php foreach ($catalogo as $clave => $p): ?>
                                        <option value="<?= esc($clave) ?>" <?= old('problema', $reporte['problema']) === $clave ? 'selected' : '' ?>><?= esc($p['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Confirmá o corregí lo que eligió el huertero: se usa para las alertas de la zona.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="respuesta">Qué hacer</label>
                                <textarea name="respuesta" id="respuesta" class="form-control" rows="4" maxlength="4000" required><?= esc(old('respuesta', $reporte['respuesta'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success rounded-pill px-4"><i class="bi bi-send me-1"></i> Guardar respuesta</button>
                        </div>
                    </form>
                </details>
            </div>
        </div>

        <?php if ($problema): ?>
            <div class="panel-form mb-4">
                <div class="p-4 p-md-5">
                    <h5 class="seccion-titulo mb-3"><i class="bi bi-journal-medical me-2"></i>Qué hacer con <?= esc(mb_strtolower($problema['nombre'])) ?></h5>
                    <div class="row g-4 bloque">
                        <div class="col-md-4">
                            <h6>Cómo reconocerlo</h6>
                            <ul class="small mb-0"><?php foreach ($problema['sintomas'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Qué hacer</h6>
                            <ul class="small mb-0"><?php foreach ($problema['manejo'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Cómo prevenirlo</h6>
                            <ul class="small mb-0"><?php foreach ($problema['prevencion'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                        </div>
                    </div>
                    <?php if ($problema['tipo'] === 'fisiopatia'): ?>
                        <a href="<?= base_url('huerto/configuracion') ?>" class="btn btn-sm btn-light rounded-pill px-3 mt-3"><i class="bi bi-droplet-half me-1"></i>Revisar el sistema de riego</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($sugerencias): ?>
            <div class="panel-form mb-4">
                <div class="p-4 p-md-5">
                    <h5 class="seccion-titulo mb-1"><i class="bi bi-search me-2"></i>Puede ser alguno de estos</h5>
                    <p class="small text-muted mb-3">Problemas que afectan a <?= $reporte['cultivo_nombre'] ? esc($reporte['cultivo_nombre']) : 'las huertas' ?>, primero los más frecuentes en esta época.</p>
                    <div class="row g-3">
                        <?php foreach ($sugerencias as $clave => $deTemporada): ?>
                            <?php $p = $catalogo[$clave]; $tipo = $tipos[$p['tipo']] ?? $tipos['plaga']; ?>
                            <div class="col-md-6">
                                <a href="<?= base_url('huerto/problemas#problema-' . $clave) ?>" class="dato d-block text-decoration-none">
                                    <div class="valor">
                                        <i class="bi <?= $tipo['icono'] ?> text-<?= $tipo['color'] ?> me-1"></i><?= esc($p['nombre']) ?>
                                        <?php if ($deTemporada): ?><span class="badge rounded-pill text-bg-warning ms-1">De temporada</span><?php endif; ?>
                                    </div>
                                    <div class="small text-muted"><?= esc($p['sintomas'][0] ?? '') ?></div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($reporte['estado'] !== 'resuelto'): ?>
            <form action="<?= base_url('huerto/problemas/' . $reporte['id'] . '/resolver') ?>" method="POST" class="text-center" onsubmit="return confirm('¿Ya se solucionó el problema?')">
                <button type="submit" class="btn btn-outline-success rounded-pill px-4"><i class="bi bi-check2-circle me-1"></i> Ya se solucionó</button>
            </form>
        <?php else: ?>
            <p class="text-center text-success fw-semibold"><i class="bi bi-check2-circle me-1"></i>Resuelto el <?= $fechaHora($reporte['resuelto_at']) ?></p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
