<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .ficha-cosecha {
        border-top-color: var(--hc-cosecha);
    }
    .ficha-cosecha > .huerto-hero h4 > i {
        color: var(--hc-cosecha);
    }
    .cosecha-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: var(--hc-lienzo);
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .cosecha-meta-item {
        padding: .55rem .8rem;
        border-right: 1px solid var(--hc-borde-fino);
    }
    .cosecha-meta-item:last-child { border-right: 0; }
    .cosecha-meta-item dt {
        font-size: .72rem;
        font-weight: 500;
        color: var(--hc-tinta-2);
        margin-bottom: .1rem;
    }
    .cosecha-meta-item dd {
        margin: 0;
        font-size: .88rem;
        font-weight: 700;
        color: var(--hc-tinta);
    }
    .btn-check:checked + .opcion.opcion-cosecha {
        border-color: var(--hc-cosecha);
        border-left-color: var(--hc-cosecha);
        background: var(--hc-cosecha-claro);
    }
    .btn-cosecha-principal {
        background: var(--hc-cosecha);
        border: 1px solid var(--hc-cosecha-oscuro);
        color: #fff;
        font-weight: 600;
    }
    .btn-cosecha-principal:hover {
        background: var(--hc-cosecha-oscuro);
        border-color: var(--hc-cosecha-oscuro);
        color: #fff;
    }
    .tabla-anteriores {
        width: 100%;
        border-collapse: collapse;
        font-size: .83rem;
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        overflow: hidden;
    }
    .tabla-anteriores th {
        background: var(--hc-salvia);
        padding: .45rem .75rem;
        border-bottom: 1px solid var(--hc-borde);
        font-weight: 700;
        color: var(--hc-tinta);
        text-align: left;
    }
    .tabla-anteriores td {
        padding: .45rem .75rem;
        border-bottom: 1px solid var(--hc-borde-fino);
        color: var(--hc-tinta);
    }
    .tabla-anteriores tr:last-child td { border-bottom: 0; }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $kg = fn ($valor) => number_format((float) $valor, 2, ',', '.');
    $total = array_sum(array_map(fn ($c) => (float) $c['kg'], $anteriores));
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <a href="<?= base_url('/') ?>" class="hc-volver mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <?= $this->include('layout/avisos') ?>

        <div class="panel-form ficha-cosecha">
            <div class="huerto-hero rounded-0">
                <h4 class="mb-1 text-capitalize"><i class="bi bi-basket2-fill"></i>Cosechar <?= esc($planta['nombre_planta']) ?></h4>
                <p class="mb-0">Anotá cuántos kilos sacaste y si el cultivo va a seguir dando.</p>
            </div>
            <div class="p-4">

                <dl class="cosecha-meta-grid">
                    <div class="cosecha-meta-item">
                        <dt>Cultivo</dt>
                        <dd class="text-capitalize"><?= esc($planta['nombre_planta']) ?></dd>
                    </div>
                    <div class="cosecha-meta-item">
                        <dt>Variedad</dt>
                        <dd><?= $planta['variedad'] ? esc($planta['variedad']) : '—' ?></dd>
                    </div>
                    <div class="cosecha-meta-item">
                        <dt>Sembrado el</dt>
                        <dd><?= date('d/m/Y', strtotime($planta['fecha_siembra'])) ?></dd>
                    </div>
                    <div class="cosecha-meta-item">
                        <dt>Ya cosechaste</dt>
                        <dd><?= $anteriores ? $kg($total) . ' kg en ' . count($anteriores) . (count($anteriores) === 1 ? ' vez' : ' veces') : 'Todavía nada' ?></dd>
                    </div>
                </dl>

                <form action="<?= base_url('huerto/cosechar/' . $planta['id']) ?>" method="POST">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label" for="fecha"><i class="bi bi-calendar3 me-1"></i> Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required
                                   value="<?= esc(old('fecha', date('Y-m-d'))) ?>" min="<?= esc($planta['fecha_siembra']) ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="kg"><i class="bi bi-speedometer2 me-1"></i> Kilos cosechados</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" name="kg" id="kg" class="form-control" placeholder="Ej.: 2,5" value="<?= esc(old('kg', '')) ?>">
                                <span class="input-group-text">kg</span>
                            </div>
                            <div class="form-text">Si no lo pesaste, dejalo vacío.</div>
                        </div>
                    </div>

                    <label class="form-label"><i class="bi bi-arrow-repeat me-1"></i> ¿Terminó el cultivo?</label>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <input type="radio" class="btn-check" name="final" id="final_no" value="0" <?= old('final', '1') === '0' ? 'checked' : '' ?>>
                            <label class="opcion opcion-cosecha" for="final_no">
                                <span class="titulo"><i class="bi bi-arrow-clockwise me-1"></i>No, va a seguir dando</span>
                                <div class="small text-muted mt-1">Tomate, zapallito, acelga… El cultivo sigue en el panel.</div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <input type="radio" class="btn-check" name="final" id="final_si" value="1" <?= old('final', '1') === '1' ? 'checked' : '' ?>>
                            <label class="opcion opcion-cosecha" for="final_si">
                                <span class="titulo"><i class="bi bi-check2-all me-1"></i>Sí, fue la última</span>
                                <div class="small text-muted mt-1">El cultivo pasa a Cosechado.</div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="observaciones"><i class="bi bi-journal-text me-1"></i> Observaciones <span class="text-muted fw-normal">(opcional)</span></label>
                        <input type="text" name="observaciones" id="observaciones" class="form-control" maxlength="255" placeholder="Ej.: frutos chicos por el calor" value="<?= esc(old('observaciones', '')) ?>">
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= base_url('/') ?>" class="btn btn-light px-3">Cancelar</a>
                        <button type="submit" class="btn btn-cosecha-principal px-3"><i class="bi bi-check2 me-1"></i> Registrar cosecha</button>
                    </div>
                </form>

                <?php if ($anteriores): ?>
                    <div class="hc-seccion-form">
                        <div class="fw-bold small mb-2" style="font-family: var(--hc-serif); font-size: .95rem;">
                            <i class="bi bi-clock-history me-1 text-warning"></i> Cosechas anteriores
                        </div>
                        <div class="border rounded-2 overflow-hidden">
                            <table class="tabla-anteriores">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Observaciones</th>
                                        <th class="text-end">Kilos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anteriores as $ant): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($ant['fecha'])) ?></td>
                                            <td><?= $ant['final'] ? 'Última' : 'Sigue dando' ?></td>
                                            <td class="text-muted"><?= $ant['observaciones'] ? esc($ant['observaciones']) : '—' ?></td>
                                            <td class="text-end fw-semibold"><?= $ant['kg'] !== null ? $kg($ant['kg']) . ' kg' : 'Sin pesar' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
