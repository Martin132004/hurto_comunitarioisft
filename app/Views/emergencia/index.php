<?= $this->extend('layout/template') ?>

<?= $this->section('contenido') ?>

<?php
    $estados = ['terminada' => 'Terminó', 'cancelada' => 'Falsa alarma', 'activa' => 'En curso'];
?>

<?= $this->include('layout/avisos') ?>

<section class="em-aviso-titulo mb-4" aria-labelledby="tituloEmergencia">
    <div class="em-rayas" aria-hidden="true"></div>
    <div class="cuerpo">
        <h1 id="tituloEmergencia"><i class="bi bi-cloud-lightning-rain me-2 text-danger"></i>¿Qué se viene?</h1>
        <p>Si te avisaron que hoy o en los próximos días viene alguno de estos fenómenos, tocalo y te mostramos qué hacer paso a paso.</p>
    </div>
</section>

<div class="row g-3 mb-4">
    <?php foreach ($tipos as $clave => $tipo): ?>
        <div class="col-md-4">
            <form action="<?= base_url('huerto/emergencia') ?>" method="POST" class="h-100">
                <input type="hidden" name="tipo" value="<?= esc($clave, 'attr') ?>">
                <button type="submit" class="em-opcion em-<?= esc($clave, 'attr') ?>">
                    <span class="icono" aria-hidden="true"><i class="bi <?= $tipo['icono'] ?>"></i></span>
                    <span class="titulo"><?= esc($tipo['boton']) ?></span>
                    <span class="resumen"><?= esc($tipo['resumen']) ?></span>
                    <span class="accion">Activar plan de emergencia <i class="bi bi-arrow-right"></i></span>
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($historial): ?>
    <section class="em-tarjeta" aria-labelledby="tituloHistorial">
        <h2 id="tituloHistorial"><i class="bi bi-clock-history me-1 text-success"></i> Emergencias anteriores</h2>
        <ul class="em-historial">
            <?php foreach ($historial as $e): ?>
                <?php
                    $tipo = $tipos[$e['tipo']] ?? null;
                    if (! $tipo) {
                        continue;
                    }
                    $cantidadDanos = count(\App\Models\EmergenciaModel::lista($e['danos']));
                ?>
                <li class="em-<?= esc($e['tipo'], 'attr') ?>">
                    <a href="<?= base_url('huerto/emergencia/' . $e['id']) ?>">
                        <span class="icono" aria-hidden="true"><i class="bi <?= $tipo['icono'] ?>"></i></span>
                        <span class="flex-grow-1">
                            <span class="fw-semibold d-block" style="font-family: var(--hc-serif); font-size: .98rem;"><?= esc($tipo['nombre']) ?> · <?= date('d/m/Y', strtotime($e['inicio'])) ?></span>
                            <span class="small text-muted">
                                <?= $estados[$e['estado']] ?? esc($e['estado']) ?>
                                <?php if ($e['estado'] === 'terminada'): ?>
                                    · <?= $cantidadDanos ? $cantidadDanos . ' daño' . ($cantidadDanos > 1 ? 's' : '') : 'sin daños' ?>
                                <?php endif; ?>
                            </span>
                        </span>
                        <i class="bi bi-chevron-right text-muted" aria-hidden="true"></i>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>
