<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    /* Cubierta de confirmación de riego */
    .riego-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--hc-acequia-oscuro);
        color: #fff;
        text-align: center;
        cursor: pointer;
        animation: riegoSale .5s var(--ease) 8s forwards;
    }
    .riego-overlay.saliendo { animation: riegoSale .5s var(--ease) forwards; }
    @keyframes riegoSale {
        to { opacity: 0; transform: translateY(-100%); visibility: hidden; }
    }

    .riego-escena {
        position: relative;
        width: 140px;
        height: 140px;
    }
    .riego-escena .gota {
        position: absolute;
        top: 0;
        font-size: 1.05rem;
        color: #93cef5;
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
        100% { transform: translateY(74px) scale(.6); opacity: 0; }
    }
    .riego-escena .planta {
        position: absolute;
        left: 50%;
        bottom: 14px;
        transform: translateX(-50%);
        font-size: 3.1rem;
        line-height: 1;
        color: #8cc63f;
    }
    .riego-escena .suelo {
        position: absolute;
        left: 18%;
        right: 18%;
        bottom: 6px;
        height: 8px;
        border-radius: 4px;
        background: rgba(255, 255, 255, .2);
    }
    .riego-overlay.completa .gota { animation-play-state: paused; visibility: hidden; }

    .riego-titulo {
        font-family: var(--hc-serif);
        color: #fff;
        font-size: clamp(1.45rem, 2.8vw, 1.85rem);
        font-weight: 700;
        margin: 0;
    }
    .riego-progreso { width: min(300px, 82vw); }
    .riego-progreso-cabecera {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: .45rem;
        font-size: .82rem;
        color: rgba(255, 255, 255, .85);
    }
    .riego-litros {
        font-family: var(--hc-sans);
        font-weight: 700;
        color: #fff;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .riego-carga {
        height: 6px;
        border-radius: 3px;
        background: rgba(255, 255, 255, .22);
        overflow: hidden;
    }
    .riego-carga span {
        display: block;
        width: 100%;
        height: 100%;
        border-radius: inherit;
        background: #93cef5;
        transform: scaleX(0);
        transform-origin: left;
    }
    .riego-overlay.completa .riego-paso { color: #fff; font-weight: 600; }

    /* Ficha técnica del plan de riego */
    .ficha-riego {
        border-top-color: var(--hc-acequia);
    }
    .ficha-riego > .huerto-hero h1 > i {
        color: var(--hc-acequia);
    }
    .riego-ficha-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: var(--hc-lienzo);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    @media (min-width: 768px) {
        .riego-ficha-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .riego-celda {
        padding: .75rem .9rem;
        border-right: 1px solid var(--hc-borde-fino);
        border-bottom: 1px solid var(--hc-borde-fino);
    }
    .riego-celda:last-child { border-right: 0; }
    .riego-celda .etiqueta {
        font-size: .74rem;
        color: var(--hc-tinta-2);
        font-weight: 500;
        margin-bottom: .15rem;
    }
    .riego-celda .valor {
        font-family: var(--hc-serif);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--hc-tinta);
        line-height: 1.15;
    }

    .tabla-calendario-riego {
        width: 100%;
        border-collapse: collapse;
        font-size: .86rem;
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        overflow: hidden;
    }
    .tabla-calendario-riego th {
        background: var(--hc-salvia);
        padding: .55rem .85rem;
        border-bottom: 1px solid var(--hc-borde);
        font-weight: 700;
        color: var(--hc-tinta);
        text-align: left;
    }
    .tabla-calendario-riego td {
        padding: .55rem .85rem;
        border-bottom: 1px solid var(--hc-borde-fino);
        color: var(--hc-tinta);
    }
    .tabla-calendario-riego tr:last-child td { border-bottom: 0; }
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
        <a href="<?= base_url('/') ?>" class="hc-volver mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <div class="panel-form ficha-riego">
            <div class="huerto-hero rounded-0">
                <?php if (! empty($planta['ultimo_riego'])): ?>
                    <h1 class="mb-1"><i class="bi bi-droplet-fill"></i><?= $nombre ?>: riego registrado</h1>
                    <p class="mb-0">Riego registrado el <?= date('d/m/Y \a \l\a\s H:i', strtotime($planta['ultimo_riego'])) ?>. Este es su plan de riego.</p>
                <?php else: ?>
                    <h1 class="mb-1"><i class="bi bi-droplet-half"></i><?= $nombre ?>: plan de riego</h1>
                    <p class="mb-0">Todavía no tiene riegos registrados. Este es su plan de riego.</p>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <div class="riego-ficha-grid">
                    <div class="riego-celda">
                        <div class="etiqueta"><i class="bi bi-hourglass-split me-1"></i>Hasta el próximo riego</div>
                        <div class="valor"><?= $diasFaltan === 0 ? 'Hoy' : 'En ' . $diasFaltan . ($diasFaltan === 1 ? ' día' : ' días') ?></div>
                    </div>
                    <div class="riego-celda">
                        <div class="etiqueta"><i class="bi bi-arrow-repeat me-1"></i>Frecuencia</div>
                        <div class="valor">Cada <?= $frecuencia ?> <?= $frecuencia == 1 ? 'día' : 'días' ?></div>
                    </div>
                    <div class="riego-celda">
                        <div class="etiqueta"><i class="bi bi-droplet me-1"></i>Por riego este mes</div>
                        <div class="valor" style="color: var(--hc-acequia);"><?= number_format($cantidad, 1, ',', '.') ?> L</div>
                    </div>
                    <div class="riego-celda">
                        <div class="etiqueta"><i class="bi bi-basket2 me-1"></i>Riegos hasta la cosecha</div>
                        <div class="valor"><?= $riegosRestantes ?></div>
                    </div>
                </div>

                <p class="small text-muted mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    La planta necesita <strong><?= number_format($litrosPlanta, 1, ',', '.') ?> L</strong> en primavera u otoño. Los litros de cada riego se ajustan al mes y a tu método de riego (<?= esc(mb_strtolower($metodo['nombre'])) ?>, aprovecha ~<?= round($metodo['eficiencia'] * 100) ?>% del agua).
                </p>

                <?php if ($usaTurno): ?>
                    <div class="alert <?= $riegosEntreTurnos > 0 ? 'alert-warning' : 'alert-success' ?> mb-4">
                        <i class="bi bi-water me-1"></i>
                        <?php if (! empty($proximosTurnos)): ?>
                            Próximo turno de agua: <strong><?= $proximosTurnos[0]->format('Y-m-d') === date('Y-m-d') ? 'hoy' : $proximosTurnos[0]->format('d/m') ?></strong>.
                        <?php endif; ?>
                        <?php if ($riegosEntreTurnos > 0): ?>
                            Esta planta necesita <strong><?= $riegosEntreTurnos ?> riego(s) entre turnos</strong>: guardá unos <strong><?= number_format($riegosEntreTurnos * $cantidad, 1, ',', '.') ?> L</strong> para ella.
                        <?php else: ?>
                            No necesitás guardar agua entre turnos: podés regarla en los días de turno según su frecuencia.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="hc-seccion-form mt-3 pt-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h6 class="seccion-titulo mb-0"><i class="bi bi-clock me-1"></i>Horario recomendado: <?= esc($horario['nombre']) ?></h6>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($horario['franjas'] as $franja): ?>
                                <span class="badge text-bg-light"><i class="bi bi-sun me-1"></i><?= esc($franja) ?> hs</span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <h6 class="seccion-titulo mb-2"><i class="bi bi-calendar-week me-1"></i>Próximos riegos</h6>
                    <?php if (empty($calendario)): ?>
                        <p class="text-muted small mb-0">No quedan riegos programados antes de la fecha estimada de cosecha.</p>
                    <?php else: ?>
                        <div class="border rounded-2 overflow-hidden">
                            <table class="tabla-calendario-riego">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Agua</th>
                                        <th>Horario</th>
                                        <th class="text-end">Litros</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calendario as $riego): ?>
                                        <tr>
                                            <td class="fw-semibold"><i class="bi bi-droplet text-primary me-1"></i><?= $riego['fecha']->format('d/m/Y') ?></td>
                                            <td>
                                                <?php if ($usaTurno): ?>
                                                    <?php if ($riego['turno']): ?>
                                                        <span class="badge text-bg-success">Día de turno</span>
                                                    <?php else: ?>
                                                        <span class="badge text-bg-light">Con agua guardada</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">Agua de red</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted small"><?= esc(implode(' y ', $horario['franjas'])) ?> hs</td>
                                            <td class="text-end fw-bold"><?= number_format($riego['litros'], 1, ',', '.') ?> L</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-2 border-top">
                    <a href="<?= base_url('/') ?>" class="btn btn-huerto"><i class="bi bi-grid-1x2 me-1"></i> Volver al panel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        const pantalla = document.getElementById('pantallaRiego');
        const barra = document.getElementById('riegoBarra');
        const relleno = barra.querySelector('span');
        const textoPaso = document.getElementById('riegoPaso');
        const textoLitros = document.getElementById('riegoLitros');

        const litros = parseFloat(pantalla.dataset.litros) || 0;
        const reducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const duracion = reducido ? 800 : 3200;
        const pasos = [
            [0,   'Abriendo el agua…'],
            [12,  'Mojando la tierra…'],
            [55,  'Llegando a las raíces…'],
            [100, 'Riego registrado'],
        ];
        const formato = n => n.toLocaleString('es-AR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        const total = formato(litros);
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
                setTimeout(quitar, 650);
            }
        };
        setTimeout(() => requestAnimationFrame(avanzar), 350);
    })();
</script>
<?= $this->endSection() ?>
