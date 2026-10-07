<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .zona-opcion {
        display: block;
        cursor: pointer;
        border: 2px solid transparent;
        background: var(--huerto-crema);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        height: 100%;
        transition: border-color .2s, background .2s;
    }
    .zona-opcion:hover {
        border-color: rgba(31, 122, 77, .3);
    }
    .btn-check:checked + .zona-opcion {
        border-color: var(--huerto-verde);
        background: #fff;
        box-shadow: 0 8px 20px -12px rgba(15, 61, 46, .5);
    }
    .zona-opcion .zona-nombre {
        font-weight: 700;
        color: var(--huerto-verde-oscuro);
    }
    .seccion-config {
        border-top: 1px solid rgba(15, 61, 46, .08);
        padding-top: 1.75rem;
        margin-top: .5rem;
    }
    .resumen-riego {
        background: var(--huerto-crema);
        border-radius: 1rem;
        padding: 1.25rem;
    }
    .resumen-riego .valor {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--huerto-verde-oscuro);
        line-height: 1.1;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $numero = fn ($valor) => number_format((float) $valor, 1, ',', '.');
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    // "09-15" -> "15 de septiembre"
    $fecha = fn ($md) => (int) substr($md, 3, 2) . ' de ' . $meses[(int) substr($md, 0, 2) - 1];
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <a href="<?= base_url('/') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <?php if (session()->getFlashdata('mensaje')): ?>
            <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i> <?= esc(session()->getFlashdata('mensaje')) ?></div>
        <?php endif; ?>

        <div class="panel-form">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <h4 class="fw-bold mb-1"><i class="bi bi-geo-alt me-2"></i>Mi huerto</h4>
                <p class="mb-0 opacity-75 small">La zona define las fechas de siembra y los avisos de helada de la guía de cultivo.</p>
            </div>
            <div class="p-4 p-md-5">
                <form action="<?= base_url('huerto/configuracion') ?>" method="POST">
                    <div class="mb-4" style="max-width: 420px;">
                        <label class="form-label"><i class="bi bi-house me-1"></i> Nombre del huerto</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc($huerto['nombre']) ?>" maxlength="100" required>
                    </div>

                    <label class="form-label"><i class="bi bi-map me-1"></i> Zona</label>
                    <?php if (empty($zonas)): ?>
                        <p class="text-muted">Las zonas todavía no están cargadas.</p>
                    <?php else: ?>
                        <div class="row g-3 mb-4">
                            <?php foreach ($zonas as $zona): ?>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="zona" id="zona_<?= esc($zona['clave']) ?>" value="<?= esc($zona['clave']) ?>" <?= $huerto['zona'] === $zona['clave'] ? 'checked' : '' ?> required>
                                    <label class="zona-opcion" for="zona_<?= esc($zona['clave']) ?>">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <span class="zona-nombre"><?= esc($zona['nombre']) ?></span>
                                            <span class="badge rounded-pill text-bg-light border"><?= number_format($zona['altitud_m'], 0, ',', '.') ?> m</span>
                                        </div>
                                        <div class="small text-muted mb-2"><?= esc($zona['departamentos']) ?></div>
                                        <div class="small mb-2"><?= esc($zona['descripcion']) ?></div>
                                        <div class="small">
                                            <i class="bi bi-snow text-primary me-1"></i>
                                            Heladas: del <?= $fecha($zona['primera_helada']) ?> al <?= $fecha($zona['ultima_helada']) ?> (aprox.)
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="seccion-config mb-4">
                        <h5 class="seccion-titulo mb-1"><i class="bi bi-droplet-half me-2"></i>Sistema de riego</h5>
                        <p class="text-muted small mb-3">Cada huerto riega distinto: contanos cómo es el tuyo y ajustamos los litros y el plan de riego.</p>

                        <label class="form-label">¿De dónde sale el agua?</label>
                        <div class="row g-3 mb-4">
                            <?php foreach ($fuentes as $clave => $fuente): ?>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="fuente_agua" id="fuente_<?= $clave ?>" value="<?= $clave ?>" <?= $huerto['fuente_agua'] === $clave ? 'checked' : '' ?>>
                                    <label class="zona-opcion" for="fuente_<?= $clave ?>">
                                        <span class="zona-nombre"><i class="bi <?= $fuente['icono'] ?> me-1"></i><?= $fuente['nombre'] ?></span>
                                        <div class="small text-muted mt-1"><?= $fuente['detalle'] ?></div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div id="datosTurno" class="mb-4" <?= $huerto['fuente_agua'] === 'turno' ? '' : 'hidden' ?>>
                            <label class="form-label">¿Cómo es tu turno de agua?</label>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="turno_modo" id="modoIntervalo" value="intervalo" <?= $huerto['turno_modo'] !== 'semana' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="modoIntervalo">Cada cierta cantidad de días</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="turno_modo" id="modoSemana" value="semana" <?= $huerto['turno_modo'] === 'semana' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="modoSemana">Días fijos de la semana</label>
                                </div>
                            </div>

                            <div id="turnoIntervalo" class="row g-3" <?= $huerto['turno_modo'] === 'semana' ? 'hidden' : '' ?>>
                                <div class="col-sm-6 col-md-4">
                                    <label class="form-label small">El turno llega cada (días)</label>
                                    <input type="number" name="turno_cada_dias" class="form-control" min="1" max="60" value="<?= esc($huerto['turno_cada_dias'] ?? 7) ?>">
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <label class="form-label small">Fecha de un turno (el último o el próximo)</label>
                                    <input type="date" name="turno_referencia" class="form-control" value="<?= esc($huerto['turno_referencia'] ?? date('Y-m-d')) ?>">
                                </div>
                            </div>

                            <div id="turnoSemana" <?= $huerto['turno_modo'] === 'semana' ? '' : 'hidden' ?>><div class="d-flex flex-wrap gap-2">
                                <?php $diasTurno = explode(',', (string) $huerto['turno_dias_semana']); ?>
                                <?php foreach ($dias as $numeroDia => $nombreDia): ?>
                                    <input type="checkbox" class="btn-check" name="turno_dias_semana[]" id="dia_<?= $numeroDia ?>" value="<?= $numeroDia ?>" <?= in_array((string) $numeroDia, $diasTurno, true) ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-success btn-sm rounded-pill px-3" for="dia_<?= $numeroDia ?>"><?= $nombreDia ?></label>
                                <?php endforeach; ?>
                            </div></div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Método de riego</label>
                                <select name="metodo_riego" class="form-control">
                                    <?php foreach ($metodos as $clave => $metodo): ?>
                                        <option value="<?= $clave ?>" <?= $huerto['metodo_riego'] === $clave ? 'selected' : '' ?>>
                                            <?= $metodo['nombre'] ?> (aprovecha ~<?= round($metodo['eficiencia'] * 100) ?>% del agua)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agua que podés guardar (tanque, tachos o reservorio)</label>
                                <div class="input-group">
                                    <input type="number" name="reservorio_litros" class="form-control" min="0" step="10" value="<?= esc($huerto['reservorio_litros']) ?>">
                                    <span class="input-group-text border-0">litros</span>
                                </div>
                                <div class="form-text">Poné 0 si no guardás agua.</div>
                            </div>
                        </div>

                        <div class="resumen-riego">
                            <h6 class="seccion-titulo mb-3"><i class="bi bi-clipboard-data me-2"></i>Tu huerto este mes (<?= $meses[(int) date('n') - 1] ?>)</h6>
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="valor"><?= $numero($resumen['litros_semana']) ?> L</div>
                                    <div class="small text-muted">de agua por semana para los cultivos en curso</div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="valor">× <?= number_format($resumen['coeficiente'], 2, ',', '.') ?></div>
                                    <div class="small text-muted"><?= $huerto['zona'] ? 'ajuste por la demanda de agua del mes en tu zona' : 'elegí la zona para ajustar por mes' ?></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="valor"><?= round($resumen['metodo']['eficiencia'] * 100) ?>%</div>
                                    <div class="small text-muted">del agua aprovecha la planta con <?= mb_strtolower($resumen['metodo']['nombre']) ?></div>
                                </div>
                            </div>
                            <?php if ($resumen['usa_turno']): ?>
                                <hr>
                                <p class="small mb-1">
                                    <i class="bi bi-calendar-check me-1"></i><strong>Próximos turnos:</strong>
                                    <?= implode(', ', array_map(fn ($f) => $f->format('d/m'), $resumen['proximos_turnos'])) ?>
                                    (hasta <?= $resumen['dias_entre_turnos'] ?> días entre turnos)
                                </p>
                                <?php if ($resumen['agua_entre_turnos'] <= 0): ?>
                                    <p class="small mb-0 text-success"><i class="bi bi-check-circle-fill me-1"></i>Con el turno alcanza: ningún cultivo necesita riego entre turnos.</p>
                                <?php elseif ($resumen['reservorio_alcanza']): ?>
                                    <p class="small mb-0 text-success"><i class="bi bi-check-circle-fill me-1"></i>Entre turnos necesitás unos <?= $numero($resumen['agua_entre_turnos']) ?> L guardados y tenés <?= $resumen['reservorio'] ?> L: alcanza.</p>
                                <?php else: ?>
                                    <p class="small mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Entre turnos necesitás unos <?= $numero($resumen['agua_entre_turnos']) ?> L guardados y tenés <?= $resumen['reservorio'] ?> L. Conviene sumar capacidad o elegir cultivos que se rieguen menos seguido.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="small text-muted mb-0 mt-2">Los valores se actualizan al guardar.</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('/') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-huerto"><i class="bi bi-check-lg me-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Mostramos los datos del turno solo si el agua llega por turno de canal
    const mostrarTurno = () => {
        const fuente = document.querySelector('input[name="fuente_agua"]:checked');
        const semana = document.getElementById('modoSemana').checked;
        document.getElementById('datosTurno').hidden = !fuente || fuente.value !== 'turno';
        document.getElementById('turnoIntervalo').hidden = semana;
        document.getElementById('turnoSemana').hidden = !semana;
    };
    document.querySelectorAll('input[name="fuente_agua"], input[name="turno_modo"]').forEach(input => input.addEventListener('change', mostrarTurno));
</script>
<?= $this->endSection() ?>
