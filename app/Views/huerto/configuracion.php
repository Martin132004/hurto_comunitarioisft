<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .resumen-ficha-riego {
        background: var(--hc-lienzo);
        border: 1px solid var(--hc-borde);
        border-top: 3px solid var(--hc-acequia);
        border-radius: 6px;
        overflow: hidden;
    }
    .resumen-ficha-cabecera {
        background: var(--hc-salvia);
        border-bottom: 1px solid var(--hc-borde);
        padding: .65rem 1rem;
        font-family: var(--hc-serif);
        font-weight: 700;
        font-size: .95rem;
        color: var(--hc-tinta);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .resumen-ficha-grilla {
        display: grid;
        grid-template-columns: 1fr;
        background: var(--hc-papel);
    }
    @media (min-width: 576px) {
        .resumen-ficha-grilla {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    .resumen-ficha-celda {
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--hc-borde-fino);
        border-right: 1px solid var(--hc-borde-fino);
    }
    .resumen-ficha-celda:last-child { border-right: 0; }
    .resumen-ficha-celda .valor {
        font-family: var(--hc-serif);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--hc-tinta);
        line-height: 1.1;
    }
    .resumen-ficha-celda .detalle {
        font-size: .78rem;
        color: var(--hc-tinta-2);
        margin-top: .2rem;
    }
    .resumen-ficha-pie {
        padding: .75rem 1rem;
        background: var(--hc-lienzo);
        border-top: 1px solid var(--hc-borde);
    }
    .caja-turnos {
        background: var(--hc-lienzo);
        border: 1px solid var(--hc-borde);
        border-left: 4px solid var(--hc-acequia);
        border-radius: 6px;
        padding: 1rem 1.15rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $numero = fn ($valor) => number_format((float) $valor, 1, ',', '.');
    $meses = \App\Libraries\Fechas::MESES;
    $fecha = fn ($md) => \App\Libraries\Fechas::diaMes($md);
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <a href="<?= base_url('/') ?>" class="hc-volver mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <?= $this->include('layout/avisos') ?>

        <div class="panel-form">
            <div class="huerto-hero rounded-0">
                <h4 class="mb-1"><i class="bi bi-sliders"></i>Mi huerto</h4>
                <p class="mb-0">La zona define las fechas de siembra y los avisos de helada de la guía de cultivo.</p>
            </div>
            <div class="p-4">
                <form action="<?= base_url('huerto/configuracion') ?>" method="POST">

                    <div class="mb-4">
                        <h5 class="seccion-titulo mb-3" style="font-size: 1.05rem;">
                            <span class="hc-paso-numero">1</span> Nombre y zona
                        </h5>
                        <div class="mb-3" style="max-width: 440px;">
                            <label class="form-label" for="campo_nombre_huerto"><i class="bi bi-signpost-split me-1"></i> Nombre del huerto</label>
                            <input type="text" id="campo_nombre_huerto" name="nombre" class="form-control" value="<?= esc($huerto['nombre']) ?>" maxlength="100" required>
                        </div>

                        <label class="form-label"><i class="bi bi-geo-alt me-1"></i> Zona</label>
                        <?php if (empty($zonas)): ?>
                            <p class="text-muted small">Las zonas todavía no están cargadas.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($zonas as $zona): ?>
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="zona" id="zona_<?= esc($zona['clave']) ?>" value="<?= esc($zona['clave']) ?>" <?= $huerto['zona'] === $zona['clave'] ? 'checked' : '' ?> required>
                                        <label class="zona-opcion" for="zona_<?= esc($zona['clave']) ?>">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                                <span class="zona-nombre"><?= esc($zona['nombre']) ?></span>
                                                <span class="badge text-bg-light"><?= number_format($zona['altitud_m'], 0, ',', '.') ?> m s.n.m.</span>
                                            </div>
                                            <div class="small fw-semibold text-muted mb-1"><?= esc($zona['departamentos']) ?></div>
                                            <div class="small text-muted mb-2"><?= esc($zona['descripcion']) ?></div>
                                            <div class="small pt-1 border-top" style="border-color: var(--hc-borde-fino) !important;">
                                                <i class="bi bi-snow text-primary me-1"></i>
                                                <strong>Heladas:</strong> del <?= $fecha($zona['primera_helada']) ?> al <?= $fecha($zona['ultima_helada']) ?> (aprox.)
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="seccion-config mb-4">
                        <h5 class="seccion-titulo mb-1" style="font-size: 1.05rem;">
                            <span class="hc-paso-numero">2</span> Sistema de riego
                        </h5>
                        <p class="text-muted small mb-3">Cada huerto riega distinto: contanos cómo es el tuyo y ajustamos los litros y el plan de riego.</p>

                        <label class="form-label"><i class="bi bi-water me-1"></i> ¿De dónde sale el agua?</label>
                        <div class="row g-3 mb-4">
                            <?php foreach ($fuentes as $clave => $fuente): ?>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="fuente_agua" id="fuente_<?= $clave ?>" value="<?= $clave ?>" <?= $huerto['fuente_agua'] === $clave ? 'checked' : '' ?>>
                                    <label class="zona-opcion" for="fuente_<?= $clave ?>">
                                        <span class="zona-nombre"><i class="bi <?= $fuente['icono'] ?> me-1 text-primary"></i><?= $fuente['nombre'] ?></span>
                                        <div class="small text-muted mt-1"><?= $fuente['detalle'] ?></div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div id="datosTurno" class="caja-turnos mb-4" <?= $huerto['fuente_agua'] === 'turno' ? '' : 'hidden' ?>>
                            <label class="form-label mb-2"><i class="bi bi-calendar-range me-1"></i> ¿Cómo es tu turno de agua?</label>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="turno_modo" id="modoIntervalo" value="intervalo" <?= $huerto['turno_modo'] !== 'semana' ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold small" for="modoIntervalo">Cada cierta cantidad de días</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="turno_modo" id="modoSemana" value="semana" <?= $huerto['turno_modo'] === 'semana' ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold small" for="modoSemana">Días fijos de la semana</label>
                                </div>
                            </div>

                            <div id="turnoIntervalo" class="row g-3" <?= $huerto['turno_modo'] === 'semana' ? 'hidden' : '' ?>>
                                <div class="col-sm-6 col-md-4">
                                    <label class="form-label small" for="campo_turno_cada">El turno llega cada</label>
                                    <div class="input-group">
                                        <span class="input-group-text">cada</span>
                                        <input type="number" id="campo_turno_cada" name="turno_cada_dias" class="form-control" min="1" max="60" value="<?= esc($huerto['turno_cada_dias'] ?? 7) ?>">
                                        <span class="input-group-text">días</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-5">
                                    <label class="form-label small" for="campo_turno_ref">Fecha de un turno (el último o el próximo)</label>
                                    <input type="date" id="campo_turno_ref" name="turno_referencia" class="form-control" value="<?= esc($huerto['turno_referencia'] ?? date('Y-m-d')) ?>">
                                </div>
                            </div>

                            <div id="turnoSemana" <?= $huerto['turno_modo'] === 'semana' ? '' : 'hidden' ?>>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php $diasTurno = explode(',', (string) $huerto['turno_dias_semana']); ?>
                                    <?php foreach ($dias as $numeroDia => $nombreDia): ?>
                                        <input type="checkbox" class="btn-check" name="turno_dias_semana[]" id="dia_<?= $numeroDia ?>" value="<?= $numeroDia ?>" <?= in_array((string) $numeroDia, $diasTurno, true) ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-success btn-sm px-3" for="dia_<?= $numeroDia ?>"><?= $nombreDia ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label" for="campo_metodo_riego"><i class="bi bi-droplet-half me-1"></i> Método de riego</label>
                                <select id="campo_metodo_riego" name="metodo_riego" class="form-control">
                                    <?php foreach ($metodos as $clave => $metodo): ?>
                                        <option value="<?= $clave ?>" data-fuentes="<?= implode(',', $metodo['fuentes']) ?>" <?= $huerto['metodo_riego'] === $clave ? 'selected' : '' ?>>
                                            <?= $metodo['nombre'] ?> (aprovecha ~<?= round($metodo['eficiencia'] * 100) ?>% del agua)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text" id="ayudaMetodo"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="campo_reservorio"><i class="bi bi-bucket me-1"></i> Agua que podés guardar (tanque, tachos o reservorio)</label>
                                <div class="input-group">
                                    <input type="number" id="campo_reservorio" name="reservorio_litros" class="form-control" min="0" step="10" value="<?= esc($huerto['reservorio_litros']) ?>">
                                    <span class="input-group-text">litros</span>
                                </div>
                                <div class="form-text">Poné 0 si no guardás agua.</div>
                            </div>
                        </div>

                        <div class="resumen-ficha-riego">
                            <div class="resumen-ficha-cabecera">
                                <span><i class="bi bi-clipboard-data me-1 text-primary"></i> Tu huerto este mes (<?= $meses[(int) date('n') - 1] ?>)</span>
                                <span class="small fw-normal text-muted" style="font-family: var(--hc-sans);">Los valores se actualizan al guardar</span>
                            </div>
                            <div class="resumen-ficha-grilla">
                                <div class="resumen-ficha-celda">
                                    <div class="valor" style="color: var(--hc-acequia);"><?= $numero($resumen['litros_semana']) ?> L</div>
                                    <div class="detalle">de agua por semana para los cultivos en curso</div>
                                </div>
                                <div class="resumen-ficha-celda">
                                    <div class="valor">× <?= number_format($resumen['coeficiente'], 2, ',', '.') ?></div>
                                    <div class="detalle"><?= $huerto['zona'] ? 'ajuste por la demanda de agua del mes en tu zona' : 'elegí la zona para ajustar por mes' ?></div>
                                </div>
                                <div class="resumen-ficha-celda">
                                    <div class="valor"><?= round($resumen['metodo']['eficiencia'] * 100) ?>%</div>
                                    <div class="detalle">del agua aprovecha la planta con <?= mb_strtolower($resumen['metodo']['nombre']) ?></div>
                                </div>
                            </div>
                            <?php if ($resumen['usa_turno']): ?>
                                <div class="resumen-ficha-pie">
                                    <div class="small mb-1">
                                        <i class="bi bi-calendar-check me-1 text-primary"></i><strong>Próximos turnos:</strong>
                                        <?= implode(', ', array_map(fn ($f) => $f->format('d/m'), $resumen['proximos_turnos'])) ?>
                                        (hasta <?= $resumen['dias_entre_turnos'] ?> días entre turnos)
                                    </div>
                                    <?php if ($resumen['agua_entre_turnos'] <= 0): ?>
                                        <div class="small text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Con el turno alcanza: ningún cultivo necesita riego entre turnos.</div>
                                    <?php elseif ($resumen['reservorio_alcanza']): ?>
                                        <div class="small text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Entre turnos necesitás unos <?= $numero($resumen['agua_entre_turnos']) ?> L guardados y tenés <?= $resumen['reservorio'] ?> L: alcanza.</div>
                                    <?php else: ?>
                                        <div class="small text-danger fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Entre turnos necesitás unos <?= $numero($resumen['agua_entre_turnos']) ?> L guardados y tenés <?= $resumen['reservorio'] ?> L. Conviene sumar capacidad o elegir cultivos que se rieguen menos seguido.</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= base_url('/') ?>" class="btn btn-light px-3">Cancelar</a>
                        <button type="submit" class="btn btn-huerto"><i class="bi bi-check2 me-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const mostrarTurno = () => {
        const fuente = document.querySelector('input[name="fuente_agua"]:checked');
        const semana = document.getElementById('modoSemana').checked;
        document.getElementById('datosTurno').hidden = !fuente || fuente.value !== 'turno';
        document.getElementById('turnoIntervalo').hidden = semana;
        document.getElementById('turnoSemana').hidden = !semana;
        mostrarMetodos();
    };

    const mostrarMetodos = () => {
        const fuente = document.querySelector('input[name="fuente_agua"]:checked')?.value ?? 'red';
        const select = document.querySelector('select[name="metodo_riego"]');
        [...select.options].forEach(opcion => {
            const sirve = opcion.dataset.fuentes.split(',').includes(fuente);
            opcion.hidden = !sirve;
            opcion.disabled = !sirve;
        });
        if (select.selectedOptions[0]?.disabled) select.value = 'manguera';
        document.getElementById('ayudaMetodo').textContent = fuente === 'turno'
            ? 'Con turno de canal podés regar por surco o manto el día del turno; la aspersión necesita la presión de la red.'
            : 'Con agua de red el surco y el manto no se usan: hace falta el caudal de una acequia.';
    };
    mostrarMetodos();
    document.querySelectorAll('input[name="fuente_agua"], input[name="turno_modo"]').forEach(input => input.addEventListener('change', mostrarTurno));
</script>
<?= $this->endSection() ?>
