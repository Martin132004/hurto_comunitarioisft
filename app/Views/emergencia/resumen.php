<?= $this->extend('layout/template') ?>

<?= $this->section('contenido') ?>

<?php
    $cancelada = $emergencia['estado'] === 'cancelada';
    $reparacion = [];
    foreach (array_keys($danos) as $clave) {
        $reparacion[$clave] = \App\Libraries\PlanEmergencia::pasosReparacion($clave);
    }
    $total = array_sum(array_map('count', $reparacion));
    $hechosReparacion = 0;
    foreach ($reparacion as $lista) {
        $hechosReparacion += count(array_intersect(array_keys($lista), $hechos));
    }

    $inicio = strtotime($emergencia['inicio']);
    $horas = $emergencia['fin'] ? max(0, (strtotime($emergencia['fin']) - $inicio) / 3600) : 0;
    $duracion = $horas < 1 ? round($horas * 60) . ' min' : round($horas, 1) . ' h';
    $problema = $tipo['problema'] ? '&problema=' . $tipo['problema'] : '';
?>

<div class="em-<?= esc($emergencia['tipo'], 'attr') ?>" data-pasos data-url="<?= base_url('huerto/emergencia/' . $emergencia['id'] . '/paso') ?>">

    <section class="em-cabecera mb-4" aria-labelledby="tituloResumen">
        <div class="em-rayas" aria-hidden="true"></div>
        <div class="cuerpo">
            <span class="em-estado fin"><?= $cancelada ? '<i class="bi bi-x-circle" aria-hidden="true"></i> Falsa alarma' : '<i class="bi bi-check2-circle" aria-hidden="true"></i> Terminó' ?></span>
            <h1 id="tituloResumen"><i class="bi <?= $tipo['icono'] ?>" aria-hidden="true"></i><?= esc($tipo['nombre']) ?></h1>
            <div class="meta">
                Del <?= date('d/m/Y \a \l\a\s H:i', $inicio) ?><?= $emergencia['fin'] ? ', duró ' . $duracion : '' ?>.
                <?php if (! $cancelada): ?>
                    <?= $danos ? count($danos) . ' tipo' . (count($danos) > 1 ? 's' : '') . ' de daño registrados.' : ($afectados ? 'Daños sin clasificar.' : 'Sin daños registrados.') ?>
                <?php endif; ?>
            </div>

            <?php if ($total): ?>
                <div class="em-progreso" aria-live="polite">
                    <div class="texto">
                        <span>Reparación</span>
                        <strong><span data-hechos><?= $hechosReparacion ?></span> de <?= $total ?> pasos</strong>
                    </div>
                    <div class="barra" role="progressbar" aria-label="Pasos de reparación hechos" aria-valuemin="0" aria-valuemax="<?= $total ?>" aria-valuenow="<?= $hechosReparacion ?>" data-barra>
                        <span style="width: <?= round($hechosReparacion / $total * 100) ?>%"></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (! $cancelada && ! $danos && ! $afectados): ?>
        <div class="em-tarjeta mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-emoji-smile fs-2 text-success" aria-hidden="true"></i>
            <div>
                <strong>¡Bien! El huerto salió sin daños.</strong>
                <div class="small text-muted">Si en los próximos días aparece algo que no viste, reportalo en Problemas y plagas.</div>
            </div>
        </div>
    <?php elseif (! $cancelada && ! $danos): ?>
        <div class="em-tarjeta mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-question-circle fs-2 text-warning" aria-hidden="true"></i>
            <div>
                <strong>Hay cultivos afectados con daños sin clasificar.</strong>
                <div class="small text-muted">Revisalos y avisale al técnico con una foto para saber cómo repararlos.</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if ($danos): ?>
            <div class="col-lg-8">
                <h2 class="em-fase-titulo mt-0"><i class="bi bi-tools" aria-hidden="true"></i> Plan de reparación</h2>
                <div class="d-grid gap-3">
                    <?php foreach ($danos as $clave => $dano): ?>
                        <section class="em-tarjeta em-reparar" aria-labelledby="dano_<?= esc($clave, 'attr') ?>">
                            <h3 id="dano_<?= esc($clave, 'attr') ?>"><i class="bi <?= $dano['icono'] ?>" aria-hidden="true"></i><?= esc($dano['nombre']) ?></h3>
                            <ul class="em-pasos">
                                <?php foreach ($reparacion[$clave] as $paso => $texto): ?>
                                    <li>
                                        <label class="em-paso">
                                            <input type="checkbox" data-clave="<?= esc($paso, 'attr') ?>" <?= in_array($paso, $hechos, true) ? 'checked' : '' ?>>
                                            <span class="texto"><?= esc($texto) ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ($clave === 'perdidas'): ?>
                                <a href="<?= base_url('huerto/crear') ?>" class="btn btn-huerto btn-sm mt-3"><i class="bi bi-plus-lg me-1"></i> Volver a sembrar</a>
                            <?php endif; ?>
                        </section>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($afectados || $emergencia['notas']): ?>
            <div class="col-lg-4">
                <?php if ($afectados): ?>
                    <section class="em-tarjeta mb-3" aria-labelledby="tituloAfectados">
                        <h2 id="tituloAfectados"><i class="bi bi-flower1 me-1"></i> Cultivos afectados</h2>
                        <ul class="em-riesgo">
                            <?php foreach ($afectados as $c): ?>
                                <li>
                                    <span class="nombre flex-grow-1"><?= esc(mb_strtolower((string) ($c['nombre'] ?? ''))) ?><?= ! empty($c['variedad']) ? ' ' . esc($c['variedad']) : '' ?></span>
                                    <?php if (in_array((int) ($c['id'] ?? 0), $enCurso, true)): ?>
                                        <a href="<?= base_url('huerto/problemas/nuevo?cultivo=' . $c['id'] . $problema) ?>" class="small">Avisar al técnico</a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="small text-muted mt-2 mb-0">Si el daño es grande, avisale al técnico con una foto para que te oriente.</p>
                    </section>
                <?php endif; ?>
                <?php if ($emergencia['notas']): ?>
                    <section class="em-tarjeta" aria-labelledby="tituloNotas">
                        <h2 id="tituloNotas"><i class="bi bi-journal-text me-1"></i> Notas</h2>
                        <p class="mb-0" style="white-space: pre-line; overflow-wrap: anywhere;"><?= esc($emergencia['notas']) ?></p>
                    </section>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <a href="<?= base_url('huerto/emergencia') ?>" class="btn btn-link px-0 mt-3"><i class="bi bi-arrow-left me-1"></i> Volver a emergencias</a>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/emergencia.js') ?>"></script>
<?= $this->endSection() ?>
