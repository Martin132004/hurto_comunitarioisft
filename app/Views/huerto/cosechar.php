<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .opcion {
        display: block;
        cursor: pointer;
        border: 2px solid transparent;
        background: var(--huerto-crema);
        border-radius: 1rem;
        padding: .9rem 1.1rem;
        height: 100%;
        transition: border-color .2s, background .2s;
    }
    .btn-check:checked + .opcion {
        border-color: var(--huerto-verde);
        background: #fff;
        box-shadow: 0 8px 20px -12px rgba(15, 61, 46, .5);
    }
    .opcion .titulo {
        font-weight: 700;
        color: var(--huerto-verde-oscuro);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $kg = fn ($valor) => number_format((float) $valor, 2, ',', '.');
    $total = array_sum(array_map(fn ($c) => (float) $c['kg'], $anteriores));
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <a href="<?= base_url('/') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i> <?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <div class="panel-form">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <h4 class="fw-bold mb-1 text-capitalize"><i class="bi bi-basket2 me-2"></i>Cosechar <?= esc($planta['nombre_planta']) ?></h4>
                <p class="mb-0 opacity-75 small">
                    <?= $planta['variedad'] ? esc($planta['variedad']) . ' · ' : '' ?>Sembrado el <?= date('d/m/Y', strtotime($planta['fecha_siembra'])) ?>
                </p>
            </div>
            <div class="p-4 p-md-5">
                <?php if ($anteriores): ?>
                    <div class="alert alert-light border rounded-4 small">
                        <i class="bi bi-clock-history me-1"></i>
                        Ya cosechaste <?= count($anteriores) ?> vez/veces: <?= $kg($total) ?> kg en total.
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('huerto/cosechar/' . $planta['id']) ?>" method="POST">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label" for="fecha"><i class="bi bi-calendar3 me-1"></i> Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required
                                   value="<?= esc(old('fecha', date('Y-m-d'))) ?>" min="<?= esc($planta['fecha_siembra']) ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="kg"><i class="bi bi-speedometer me-1"></i> Kilos cosechados</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" name="kg" id="kg" class="form-control" placeholder="Ej.: 2,5" value="<?= esc(old('kg', '')) ?>">
                                <span class="input-group-text rounded-end-3">kg</span>
                            </div>
                            <div class="form-text">Si no lo pesaste, dejalo vacío.</div>
                        </div>
                    </div>

                    <label class="form-label">¿Terminó el cultivo?</label>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <input type="radio" class="btn-check" name="final" id="final_no" value="0" <?= old('final', '1') === '0' ? 'checked' : '' ?>>
                            <label class="opcion" for="final_no">
                                <span class="titulo">No, va a seguir dando</span>
                                <div class="small text-muted mt-1">Tomate, zapallito, acelga… El cultivo sigue en el panel.</div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <input type="radio" class="btn-check" name="final" id="final_si" value="1" <?= old('final', '1') === '1' ? 'checked' : '' ?>>
                            <label class="opcion" for="final_si">
                                <span class="titulo">Sí, fue la última</span>
                                <div class="small text-muted mt-1">El cultivo pasa a Cosechado.</div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="observaciones"><i class="bi bi-chat-left-text me-1"></i> Observaciones <span class="text-muted fw-normal">(opcional)</span></label>
                        <input type="text" name="observaciones" id="observaciones" class="form-control" maxlength="255" placeholder="Ej.: frutos chicos por el calor" value="<?= esc(old('observaciones', '')) ?>">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('/') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-huerto"><i class="bi bi-check2 me-1"></i> Registrar cosecha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
