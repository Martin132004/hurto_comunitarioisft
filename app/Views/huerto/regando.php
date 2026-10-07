<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    /* Pantalla de riego en curso */
    .riego-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        padding: 1rem;
        background: linear-gradient(180deg, #0b3a5c, #1f7a4d);
        transition: opacity .6s;
    }
    .riego-overlay.oculto {
        opacity: 0;
        pointer-events: none;
    }
    .regadera {
        font-size: 5rem;
        animation: inclinar 1.2s ease-in-out infinite alternate;
    }
    @keyframes inclinar {
        from { transform: rotate(0deg); }
        to   { transform: rotate(-25deg); }
    }
    .gotas {
        position: relative;
        width: 120px;
        height: 90px;
    }
    .gotas i {
        position: absolute;
        top: 0;
        color: #7cc8ff;
        font-size: 1.3rem;
        animation: caer 1s linear infinite;
    }
    .gotas i:nth-child(1) { left: 15%; animation-delay: 0s; }
    .gotas i:nth-child(2) { left: 40%; animation-delay: .3s; }
    .gotas i:nth-child(3) { left: 65%; animation-delay: .6s; }
    .gotas i:nth-child(4) { left: 85%; animation-delay: .15s; }
    @keyframes caer {
        from { transform: translateY(0); opacity: 1; }
        to   { transform: translateY(80px); opacity: 0; }
    }
    .riego-progreso {
        width: min(320px, 80vw);
        height: 10px;
        border-radius: 50rem;
        background: rgba(255, 255, 255, .2);
        overflow: hidden;
    }
    .riego-progreso div {
        height: 100%;
        width: 0;
        background: #7cc8ff;
        animation: llenar 3s linear forwards;
    }
    @keyframes llenar {
        to { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
        .regadera, .gotas i, .riego-progreso div { animation-duration: .01s; }
    }

    /* Resumen del plan de riego */
    .plan-dato {
        background: var(--huerto-crema);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        height: 100%;
    }
    .plan-dato .valor {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--huerto-verde-oscuro);
    }
    .plan-dato .etiqueta {
        font-size: .8rem;
        color: #6c757d;
        font-weight: 500;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $hoy = new \DateTime('today');
    $diasFaltan = (int) $hoy->diff((clone $proximoRiego)->setTime(0, 0))->days;
    $nombre = esc($planta['nombre_planta']);
?>

<div id="pantallaRiego" class="riego-overlay" role="status" aria-live="polite">
    <i class="bi bi-droplet-fill regadera"></i>
    <div class="gotas" aria-hidden="true">
        <i class="bi bi-droplet-fill"></i><i class="bi bi-droplet-fill"></i><i class="bi bi-droplet-fill"></i><i class="bi bi-droplet-fill"></i>
    </div>
    <h2 class="fw-bold mb-2">Regando <?= $nombre ?>...</h2>
    <p class="opacity-75 mb-4">Aplicando <?= number_format($cantidad, 1, ',', '.') ?> L de agua</p>
    <div class="riego-progreso"><div></div></div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="<?= base_url('/') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <div class="panel-form">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <h4 class="fw-bold mb-1"><i class="bi bi-check2-circle me-2"></i>¡<?= $nombre ?> fue regado!</h4>
                <p class="mb-0 opacity-75 small">Riego registrado el <?= date('d/m/Y \a \l\a\s H:i', strtotime($planta['ultimo_riego'])) ?>. Este es su plan de riego.</p>
            </div>
            <div class="p-4 p-md-5">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="plan-dato">
                            <div class="valor"><?= $diasFaltan ?> <?= $diasFaltan == 1 ? 'día' : 'días' ?></div>
                            <div class="etiqueta"><i class="bi bi-hourglass-split me-1"></i>Hasta el próximo riego</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="plan-dato">
                            <div class="valor">Cada <?= $frecuencia ?></div>
                            <div class="etiqueta"><i class="bi bi-arrow-repeat me-1"></i><?= $frecuencia == 1 ? 'Día' : 'Días' ?> (frecuencia)</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="plan-dato">
                            <div class="valor"><?= number_format($cantidad, 1, ',', '.') ?> L</div>
                            <div class="etiqueta"><i class="bi bi-cup-straw me-1"></i>Por riego este mes</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="plan-dato">
                            <div class="valor"><?= $riegosRestantes ?></div>
                            <div class="etiqueta"><i class="bi bi-basket2 me-1"></i>Riegos hasta la cosecha</div>
                        </div>
                    </div>
                </div>

                <p class="small text-muted mb-4">
                    <i class="bi bi-info-circle me-1"></i>
                    La planta necesita <?= number_format($litrosPlanta, 1, ',', '.') ?> L en primavera u otoño. Los litros de cada riego se ajustan al mes
                    y a tu método de riego (<?= esc(mb_strtolower($metodo['nombre'])) ?>, aprovecha ~<?= round($metodo['eficiencia'] * 100) ?>% del agua).
                </p>

                <?php if ($usaTurno): ?>
                    <div class="alert <?= $riegosEntreTurnos > 0 ? 'alert-warning' : 'alert-success' ?> rounded-4 small">
                        <i class="bi bi-water me-1"></i>
                        <?php if (! empty($proximosTurnos)): ?>
                            Próximo turno de agua: <strong><?= $proximosTurnos[0]->format('Y-m-d') === date('Y-m-d') ? 'hoy' : $proximosTurnos[0]->format('d/m') ?></strong>.
                        <?php endif; ?>
                        <?php if ($riegosEntreTurnos > 0): ?>
                            Esta planta necesita <?= $riegosEntreTurnos ?> riego(s) entre turnos: guardá unos <?= number_format($riegosEntreTurnos * $cantidad, 1, ',', '.') ?> L para ella.
                        <?php else: ?>
                            Con regarla en cada turno alcanza.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <h6 class="seccion-titulo"><i class="bi bi-clock me-2"></i>Horario recomendado: <?= $horario['nombre'] ?></h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php foreach ($horario['franjas'] as $franja): ?>
                        <span class="badge rounded-pill text-bg-light border px-3 py-2"><i class="bi bi-sun me-1"></i><?= $franja ?> hs</span>
                    <?php endforeach; ?>
                </div>

                <h6 class="seccion-titulo"><i class="bi bi-calendar-week me-2"></i>Próximos riegos</h6>
                <?php if (empty($calendario)): ?>
                    <p class="text-muted mb-0">No quedan riegos programados antes de la fecha estimada de cosecha.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($calendario as $riego): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-1 px-0">
                                <span>
                                    <i class="bi bi-droplet text-primary me-2"></i><?= $riego['fecha']->format('d/m/Y') ?>
                                    <?php if ($usaTurno): ?>
                                        <?php if ($riego['turno']): ?>
                                            <span class="badge rounded-pill text-bg-success ms-1">Día de turno</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill text-bg-light border ms-1">Con agua guardada</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </span>
                                <small class="text-muted"><?= implode(' y ', $horario['franjas']) ?> hs · <?= number_format($riego['litros'], 1, ',', '.') ?> L</small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="d-flex justify-content-end mt-4">
                    <a href="<?= base_url('/') ?>" class="btn btn-huerto"><i class="bi bi-grid-1x2 me-1"></i> Volver al panel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // La animación de riego dura 3 segundos y luego muestra el plan
    setTimeout(function () {
        document.getElementById('pantallaRiego').classList.add('oculto');
    }, 3000);
</script>
<?= $this->endSection() ?>
