<?= $this->extend('layout/template') ?>

<?= $this->section('contenido') ?>

<?php
    $total = array_sum(array_map('count', $pasos));
    $hechosPlan = 0;
    foreach ($pasos as $lista) {
        $hechosPlan += count(array_intersect(array_keys($lista), $hechos));
    }
    $minutos = (int) floor((time() - strtotime($emergencia['inicio'])) / 60);
    $hace = $minutos < 60 ? $minutos . ' min' : floor($minutos / 60) . ' h ' . ($minutos % 60) . ' min';
    $numeroFase = 0;
?>

<div class="em-<?= esc($emergencia['tipo'], 'attr') ?>" data-pasos data-url="<?= base_url('huerto/emergencia/' . $emergencia['id'] . '/paso') ?>">

    <section class="em-cabecera mb-3" aria-labelledby="tituloPlan">
        <div class="em-rayas" aria-hidden="true"></div>
        <div class="cuerpo">
            <span class="em-estado"><span class="em-punto" aria-hidden="true"></span> Emergencia en curso</span>
            <h1 id="tituloPlan"><i class="bi <?= $tipo['icono'] ?>" aria-hidden="true"></i><?= esc($tipo['nombre']) ?></h1>
            <div class="meta">Activada a las <?= date('H:i', strtotime($emergencia['inicio'])) ?> (hace <?= $hace ?>). <?= esc($tipo['resumen']) ?></div>

            <div class="em-progreso" aria-live="polite">
                <div class="texto">
                    <span>Pasos hechos</span>
                    <strong><span data-hechos><?= $hechosPlan ?></span> de <?= $total ?></strong>
                </div>
                <div class="barra" role="progressbar" aria-label="Pasos hechos" aria-valuemin="0" aria-valuemax="<?= $total ?>" aria-valuenow="<?= $hechosPlan ?>" data-barra>
                    <span style="width: <?= $total ? round($hechosPlan / $total * 100) : 0 ?>%"></span>
                </div>
            </div>

            <div class="em-acciones">
                <a href="<?= base_url('huerto/emergencia/' . $emergencia['id'] . '/terminar') ?>" class="btn btn-light">
                    <i class="bi bi-check2-circle me-1"></i> Ya terminó
                </a>
                <form action="<?= base_url('huerto/emergencia/' . $emergencia['id'] . '/cancelar') ?>" method="POST"
                      onsubmit="return confirm('¿Cerrar como falsa alarma? No se registran daños.')">
                    <button type="submit" class="btn btn-outline-light"><i class="bi bi-x-lg me-1"></i> Falsa alarma, no vino</button>
                </form>
            </div>
        </div>
    </section>

    <div class="em-seguridad mb-2" role="note">
        <i class="bi bi-shield-exclamation" aria-hidden="true"></i>
        <div><strong>Primero tu seguridad.</strong> <?= esc($tipo['seguridad']) ?></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <?php foreach ($pasos as $fase => $lista): ?>
                <?php if (! $lista) { continue; } ?>
                <h2 class="em-fase-titulo"><span class="numero"><?= ++$numeroFase ?></span><?= esc($tipo['fases'][$fase]) ?></h2>
                <ul class="em-pasos">
                    <?php foreach ($lista as $clave => $paso): ?>
                        <li>
                            <label class="em-paso">
                                <input type="checkbox" data-clave="<?= esc($clave, 'attr') ?>" <?= in_array($clave, $hechos, true) ? 'checked' : '' ?>>
                                <span>
                                    <span class="texto d-block"><?= esc($paso['texto']) ?></span>
                                    <span class="detalle d-block"><?= esc($paso['detalle']) ?></span>
                                </span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>

        <div class="col-lg-4">
            <section class="em-tarjeta mt-lg-4" aria-labelledby="tituloRiesgo">
                <h2 id="tituloRiesgo"><i class="bi bi-flower1 me-1"></i> Tus cultivos más expuestos</h2>
                <?php if ($enRiesgo): ?>
                    <ul class="em-riesgo">
                        <?php foreach ($enRiesgo as $r): ?>
                            <li>
                                <span class="nombre"><?= esc(mb_strtolower($r['cultivo']['nombre_planta'])) ?><?= $r['cultivo']['variedad'] ? ' ' . esc($r['cultivo']['variedad']) : '' ?></span>
                                <span class="motivo"><?= esc($r['motivo']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="small text-muted mb-0">Ninguno de tus cultivos en curso está especialmente expuesto. Igual seguí los pasos para proteger todo el huerto.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/emergencia.js') ?>"></script>
<?= $this->endSection() ?>
