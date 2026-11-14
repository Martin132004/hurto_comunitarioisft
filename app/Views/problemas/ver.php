<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .marco-foto-problema {
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: #131f17;
        overflow: hidden;
    }
    .foto-problema {
        width: 100%;
        max-height: 400px;
        object-fit: contain;
        display: block;
    }
    .ficha-problema-grilla {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: var(--hc-lienzo);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .ficha-problema-celda {
        padding: .6rem .85rem;
        border-right: 1px solid var(--hc-borde-fino);
        border-bottom: 1px solid var(--hc-borde-fino);
    }
    .ficha-problema-celda:nth-child(2n) { border-right: 0; }
    .ficha-problema-celda .etiqueta {
        font-size: .72rem;
        color: var(--hc-tinta-2);
        font-weight: 500;
        margin-bottom: .1rem;
    }
    .ficha-problema-celda .valor {
        font-weight: 700;
        color: var(--hc-tinta);
        font-size: .88rem;
    }
    .respuesta {
        background: var(--hc-hoja-claro);
        border: 1px solid #b7dcc4;
        border-left: 4px solid var(--hc-hoja);
        border-radius: 6px;
        padding: 1rem 1.15rem;
        white-space: pre-line;
        color: var(--hc-tinta);
        font-size: .92rem;
    }
    .columna-vademecum {
        background: var(--hc-lienzo);
        border: 1px solid var(--hc-borde-fino);
        border-radius: 6px;
        padding: .8rem .95rem;
        height: 100%;
    }
    .columna-vademecum-titulo {
        font-family: var(--hc-serif);
        font-size: .9rem;
        font-weight: 700;
        color: var(--hc-tinta);
        padding-bottom: .3rem;
        margin-bottom: .5rem;
        border-bottom: 1px solid var(--hc-borde);
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
        <a href="<?= base_url('huerto/problemas') ?>" class="hc-volver mb-3">
            <i class="bi bi-arrow-left"></i> Volver a problemas
        </a>

        <?= $this->include('layout/avisos') ?>

        <div class="panel-form mb-4">
            <div class="huerto-hero rounded-0">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <h4 class="mb-1"><i class="bi bi-bug"></i><?= esc($problema['nombre'] ?? 'Síntoma o agente pendiente de clasificar') ?></h4>
                        <p class="mb-0">
                            <?= $reporte['cultivo_nombre'] ? 'En ' . esc($reporte['cultivo_nombre']) : 'Varios cultivos o todo el huerto' ?>
                            · Reportado el <?= $fechaHora($reporte['created_at']) ?>
                        </p>
                    </div>
                    <span class="badge text-bg-<?= $estado['color'] ?> fs-6"><i class="bi <?= $estado['icono'] ?> me-1"></i><?= $estado['nombre'] ?></span>
                </div>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <?php if ($reporte['foto']): ?>
                        <div class="col-md-6">
                            <a href="<?= base_url('huerto/problemas/' . $reporte['id'] . '/foto') ?>" target="_blank" class="marco-foto-problema d-block" title="Ver la foto completa">
                                <img src="<?= base_url('huerto/problemas/' . $reporte['id'] . '/foto') ?>" alt="Foto del problema" class="foto-problema">
                            </a>
                            <div class="small text-muted mt-1 text-end"><i class="bi bi-arrows-fullscreen me-1"></i>Tocá la foto para verla completa</div>
                        </div>
                    <?php endif; ?>
                    <div class="<?= $reporte['foto'] ? 'col-md-6' : 'col-12' ?>">
                        <div class="ficha-problema-grilla">
                            <div class="ficha-problema-celda">
                                <div class="etiqueta">Cuánto afecta</div>
                                <div class="valor"><span class="badge text-bg-<?= $gravedad['color'] ?> me-1">&nbsp;</span><?= $gravedad['nombre'] ?></div>
                            </div>
                            <div class="ficha-problema-celda">
                                <div class="etiqueta">Tipo</div>
                                <div class="valor"><?= $problema ? ($tipos[$problema['tipo']]['nombre'] ?? '') : 'Pendiente de diagnóstico' ?></div>
                            </div>
                        </div>
                        <div class="columna-vademecum" style="height: auto;">
                            <div class="columna-vademecum-titulo"><i class="bi bi-journal-text me-1"></i>Lo que se ve</div>
                            <p class="mb-0 small" style="white-space: pre-line"><?= esc($reporte['descripcion'] ?? 'Sin notas adicionales.') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-form mb-4">
            <div class="p-4">
                <h5 class="seccion-titulo mb-3"><i class="bi bi-person-badge me-2"></i>Respuesta del técnico</h5>

                <?php if ($reporte['respuesta']): ?>
                    <div class="respuesta mb-2"><?= esc($reporte['respuesta']) ?></div>
                    <p class="small text-muted mb-3">
                        <strong><?= esc($reporte['respondido_por']) ?></strong> · <?= $fechaHora($reporte['respondido_at']) ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>Todavía no hay respuesta. Mientras tanto, mirá abajo qué podés hacer.</p>
                <?php endif; ?>

                <details <?= $reporte['respuesta'] ? '' : 'open' ?>>
                    <summary class="small fw-semibold text-success mb-3" style="cursor: pointer">
                        <?= $reporte['respuesta'] ? 'Editar la respuesta' : 'Responder (técnico)' ?>
                    </summary>
                    <form action="<?= base_url('huerto/problemas/' . $reporte['id'] . '/responder') ?>" method="POST" class="p-3 rounded-2 border" style="background: var(--hc-lienzo);">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="respondido_por">Nombre del técnico</label>
                                <input type="text" name="respondido_por" id="respondido_por" class="form-control" maxlength="100" required placeholder="Ej.: Ing. Agr. M. Gómez" value="<?= esc(old('respondido_por', $reporte['respondido_por'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="diagnostico">Diagnóstico</label>
                                <select name="problema" id="diagnostico" class="form-select">
                                    <option value="">Sin identificar</option>
                                    <?php foreach ($catalogo as $clave => $p): ?>
                                        <option value="<?= esc($clave) ?>" <?= old('problema', $reporte['problema']) === $clave ? 'selected' : '' ?>><?= esc($p['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Confirmá o corregí lo que eligió el huertero: así el aviso de problemas repetidos y los reportes usan el diagnóstico correcto.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="respuesta">Qué hacer</label>
                                <textarea name="respuesta" id="respuesta" class="form-control" rows="4" maxlength="4000" required placeholder="Qué tiene que hacer el huertero y cómo."><?= esc(old('respuesta', $reporte['respuesta'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-huerto"><i class="bi bi-send me-1"></i> Guardar respuesta</button>
                        </div>
                    </form>
                </details>
            </div>
        </div>

        <?php if ($problema): ?>
            <div class="panel-form mb-4">
                <div class="p-4">
                    <h5 class="seccion-titulo mb-3"><i class="bi bi-journal-medical me-2"></i>Qué hacer con <?= esc(mb_strtolower($problema['nombre'])) ?></h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="columna-vademecum">
                                <div class="columna-vademecum-titulo"><i class="bi bi-eye me-1 text-secondary"></i>Cómo reconocerlo</div>
                                <ul class="small mb-0 ps-3"><?php foreach ($problema['sintomas'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="columna-vademecum">
                                <div class="columna-vademecum-titulo"><i class="bi bi-prescription2 me-1 text-success"></i>Qué hacer</div>
                                <ul class="small mb-0 ps-3"><?php foreach ($problema['manejo'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="columna-vademecum">
                                <div class="columna-vademecum-titulo"><i class="bi bi-shield-check me-1 text-primary"></i>Cómo prevenirlo</div>
                                <ul class="small mb-0 ps-3"><?php foreach ($problema['prevencion'] as $item): ?><li><?= esc($item) ?></li><?php endforeach; ?></ul>
                            </div>
                        </div>
                    </div>
                    <?php if ($problema['tipo'] === 'fisiopatia'): ?>
                        <div class="mt-3 pt-2 border-top">
                            <a href="<?= base_url('huerto/configuracion') ?>" class="btn btn-sm btn-light"><i class="bi bi-droplet-half me-1"></i>Revisar el sistema de riego</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($sugerencias): ?>
            <div class="panel-form mb-4">
                <div class="p-4">
                    <h5 class="seccion-titulo mb-1"><i class="bi bi-search me-2"></i>Puede ser alguno de estos</h5>
                    <p class="small text-muted mb-3">Problemas que afectan a <?= $reporte['cultivo_nombre'] ? esc($reporte['cultivo_nombre']) : 'las huertas' ?>, primero los más frecuentes en esta época.</p>
                    <div class="row g-2">
                        <?php foreach ($sugerencias as $clave => $deTemporada): ?>
                            <?php $p = $catalogo[$clave]; $tipo = $tipos[$p['tipo']] ?? $tipos['plaga']; ?>
                            <div class="col-md-6">
                                <a href="<?= base_url('huerto/problemas#problema-' . $clave) ?>" class="opcion d-block text-decoration-none">
                                    <div class="titulo">
                                        <i class="bi <?= $tipo['icono'] ?> text-<?= $tipo['color'] ?> me-1"></i><?= esc($p['nombre']) ?>
                                        <?php if ($deTemporada): ?><span class="badge text-bg-warning ms-1">De temporada</span><?php endif; ?>
                                    </div>
                                    <div class="small text-muted mt-1"><?= esc($p['sintomas'][0] ?? '') ?></div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($reporte['estado'] !== 'resuelto'): ?>
            <form action="<?= base_url('huerto/problemas/' . $reporte['id'] . '/resolver') ?>" method="POST" class="text-center" onsubmit="return confirm('¿Ya se solucionó el problema?')">
                <button type="submit" class="btn btn-outline-success px-4"><i class="bi bi-check2-circle me-1"></i> Ya se solucionó</button>
            </form>
        <?php else: ?>
            <div class="alert alert-success text-center fw-semibold mb-0">
                <i class="bi bi-check2-circle me-1"></i>Resuelto el <?= $fechaHora($reporte['resuelto_at']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
