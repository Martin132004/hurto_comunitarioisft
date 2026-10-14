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
        gap: 1.1rem;
        padding: 1rem;
        background: var(--agua-600);
        color: #fff;
        text-align: center;
        cursor: pointer;
        /* Respaldo: si el script no corre, se va sola a los 8 segundos */
        animation: riegoSale .6s var(--ease) 8s forwards;
    }
    .riego-overlay.saliendo { animation: riegoSale .6s var(--ease) forwards; }
    @keyframes riegoSale {
        to { opacity: 0; transform: translateY(-100%); visibility: hidden; }
    }

    /* Escena: gotas que caen sobre la planta */
    .riego-escena {
        position: relative;
        width: 150px;
        height: 150px;
        animation: escenaAparece .7s var(--ease) both;
    }
    @keyframes escenaAparece {
        from { opacity: 0; transform: scale(.8); }
        to   { opacity: 1; transform: none; }
    }
    .riego-escena .gota {
        position: absolute;
        top: 0;
        font-size: 1.1rem;
        color: #fff;
        opacity: 0;
        animation: gotaCae 1.1s ease-in infinite;
    }
    .riego-escena .gota:nth-child(1) { left: 22%; animation-delay: 0s; }
    .riego-escena .gota:nth-child(2) { left: 44%; animation-delay: .35s; }
    .riego-escena .gota:nth-child(3) { left: 64%; animation-delay: .7s; }
    .riego-escena .gota:nth-child(4) { left: 34%; animation-delay: .9s; }
    .riego-escena .gota:nth-child(5) { left: 56%; animation-delay: .2s; }
    @keyframes gotaCae {
        0%   { transform: translateY(0); opacity: 0; }
        15%  { opacity: .95; }
        80%  { opacity: .95; }
        100% { transform: translateY(78px) scale(.6); opacity: 0; }
    }
    .riego-escena .planta {
        position: absolute;
        left: 50%;
        bottom: 14px;
        transform: translateX(-50%);
        transform-origin: bottom center;
        font-size: 3.4rem;
        line-height: 1;
        color: var(--at-300);
    }
    .riego-escena .suelo {
        position: absolute;
        left: 18%;
        right: 18%;
        bottom: 6px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .18);
    }
    .riego-overlay.completa .gota { animation-play-state: paused; visibility: hidden; }
    .riego-overlay.completa .planta { animation: plantaCrece .6s var(--ease); }
    @keyframes plantaCrece {
        0%   { transform: translateX(-50%) scale(1); }
        45%  { transform: translateX(-50%) scale(1.15); }
        100% { transform: translateX(-50%) scale(1); }
    }

    .riego-titulo {
        font-size: clamp(1.4rem, 3vw, 1.85rem);
        font-weight: 600;
        letter-spacing: -.02em;
        margin: 0;
    }
    .riego-progreso { width: min(300px, 82vw); }
    .riego-progreso-cabecera {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: .5rem;
        font-size: .8rem;
        color: rgba(255, 255, 255, .85);
    }
    .riego-litros {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-weight: 600;
        color: #fff;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .riego-carga {
        height: 6px;
        border-radius: 6px;
        background: rgba(255, 255, 255, .2);
        overflow: hidden;
    }
    .riego-carga span {
        display: block;
        width: 100%;
        height: 100%;
        border-radius: inherit;
        background: #fff;
        transform: scaleX(0);
        transform-origin: left;
    }
    .riego-overlay.completa .riego-paso { color: #fff; font-weight: 600; }

    /* Resumen del plan de riego */
    .plan-dato {
        background: var(--huerto-crema);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        height: 100%;
    }
    .plan-dato .valor {
        font-family: 'Outfit', sans-serif;
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--huerto-verde-oscuro);
        white-space: nowrap;
    }
    .plan-dato .etiqueta {
        font-size: .8rem;
        color: var(--texto-2);
        font-weight: 500;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $hoy = new \DateTime('today');
    $diasFaltan = (int) $hoy->diff((clone $proximoRiego)->setTime(0, 0))->days;
    $nombreTexto = trim($planta['nombre_planta']);
    $nombre = esc(mb_strtoupper(mb_substr($nombreTexto, 0, 1)) . mb_substr($nombreTexto, 1));
?>

<div id="pantallaRiego" class="riego-overlay" data-cubierta data-litros="<?= round($cantidad, 1) ?>">
    <div class="riego-escena" aria-hidden="true">
        <i class="bi bi-droplet-fill gota"></i><i class="bi bi-droplet-fill gota"></i><i class="bi bi-droplet-fill gota"></i><i class="bi bi-droplet-fill gota"></i><i class="bi bi-droplet-fill gota"></i>
        <span class="suelo"></span>
        <i class="bi bi-flower1 planta"></i>
    </div>
    <h2 class="riego-titulo">Regando <?= $nombre ?></h2>
    <div class="riego-progreso">
        <div class="riego-progreso-cabecera">
            <span class="riego-paso" id="riegoPaso" role="status">Abriendo el agua…</span>
            <span class="riego-litros" id="riegoLitros" aria-hidden="true">0,0 / <?= number_format($cantidad, 1, ',', '.') ?> L</span>
        </div>
        <div class="riego-carga" id="riegoBarra" role="progressbar" aria-label="Agua aplicada" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span></span></div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="<?= base_url('/') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <div class="panel-form">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <?php if (! empty($planta['ultimo_riego'])): ?>
                    <h1 class="fw-bold mb-1"><i class="bi bi-check2-circle me-2"></i><?= $nombre ?>: riego registrado</h1>
                    <p class="mb-0 opacity-75 small">Riego registrado el <?= date('d/m/Y \a \l\a\s H:i', strtotime($planta['ultimo_riego'])) ?>. Este es su plan de riego.</p>
                <?php else: ?>
                    <h1 class="fw-bold mb-1"><i class="bi bi-droplet-half me-2"></i><?= $nombre ?>: plan de riego</h1>
                    <p class="mb-0 opacity-75 small">Todavía no tiene riegos registrados. Este es su plan de riego.</p>
                <?php endif; ?>
            </div>
            <div class="p-4 p-md-5">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <div class="plan-dato">
                            <div class="valor"><?= $diasFaltan ?> <?= $diasFaltan == 1 ? 'día' : 'días' ?></div>
                            <div class="etiqueta"><i class="bi bi-hourglass-split me-1"></i>Hasta el próximo riego</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="plan-dato">
                            <div class="valor">Cada <?= $frecuencia ?></div>
                            <div class="etiqueta"><i class="bi bi-arrow-repeat me-1"></i><?= $frecuencia == 1 ? 'Día' : 'Días' ?> (frecuencia)</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="plan-dato">
                            <div class="valor"><?= number_format($cantidad, 1, ',', '.') ?> L</div>
                            <div class="etiqueta"><i class="bi bi-cup-straw me-1"></i>Por riego este mes</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
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
                            No necesitás guardar agua entre turnos: podés regarla en los días de turno según su frecuencia.
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
    // Animación de riego: la barra cuenta los litros aplicados y al completarse muestra el plan.
    // Se puede saltear con un clic o Escape.
    (function () {
        const pantalla = document.getElementById('pantallaRiego');
        const barra = document.getElementById('riegoBarra');
        const relleno = barra.querySelector('span');
        const textoPaso = document.getElementById('riegoPaso');
        const textoLitros = document.getElementById('riegoLitros');

        const litros = parseFloat(pantalla.dataset.litros) || 0;
        const reducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const duracion = reducido ? 800 : 3400;
        const pasos = [
            [0,   'Abriendo el agua…'],
            [12,  'Mojando la tierra…'],
            [55,  'Llegando a las raíces…'],
            [100, 'Riego registrado'],
        ];
        const formato = n => n.toLocaleString('es-AR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        const total = formato(litros);
        // Arranca suave, corre parejo y frena al final, como una canilla que se abre y se cierra
        const curva = t => t < .5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
        let inicio = null;
        let terminado = false;

        const quitar = () => {
            if (terminado) return;
            terminado = true;
            pantalla.classList.add('saliendo');
            document.dispatchEvent(new Event('cubierta:sale'));
        };
        pantalla.addEventListener('animationend', e => { if (e.animationName === 'riegoSale') pantalla.remove(); });
        pantalla.addEventListener('click', quitar);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') quitar(); });

        const avanzar = ahora => {
            if (terminado) return;
            if (inicio === null) inicio = ahora;
            const t = Math.min(1, (ahora - inicio) / duracion);
            const valor = Math.round(curva(t) * 100);

            relleno.style.transform = `scaleX(${valor / 100})`;
            textoLitros.textContent = `${formato(litros * valor / 100)} / ${total} L`;
            barra.setAttribute('aria-valuenow', valor);
            const paso = pasos.filter(([desde]) => valor >= desde).pop()[1];
            if (textoPaso.textContent !== paso) textoPaso.textContent = paso;

            if (t < 1) {
                requestAnimationFrame(avanzar);
            } else {
                pantalla.classList.add('completa');
                setTimeout(quitar, 700);
            }
        };
        setTimeout(() => requestAnimationFrame(avanzar), 400);
    })();
</script>
<?= $this->endSection() ?>
