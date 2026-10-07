<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .papel-contenedor {
        overflow-x: auto;
        border-radius: 1.25rem;
        box-shadow: 0 20px 40px -24px rgba(15, 61, 46, .45);
    }
    .papel {
        background: #fff;
        min-width: 720px;
        padding: 2.5rem 2.25rem;
    }
    .selector-mes .form-control {
        border-radius: .8rem;
        max-width: 200px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php
    $anterior = date('Y-m', strtotime($mes . '-01 -1 month'));
    $siguiente = date('Y-m', strtotime($mes . '-01 +1 month'));
    $t = $reporte['totales'];
    $n = fn ($valor, $decimales = 1) => number_format((float) $valor, $decimales, ',', '.');
?>

<section class="huerto-hero mb-4">
    <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
        <div class="col-lg-7">
            <span class="badge-fecha d-inline-flex align-items-center gap-2 mb-3">
                <i class="bi bi-file-earmark-text"></i> Informe N° <?= esc($reporte['numero']) ?>
            </span>
            <h1 class="display-6 mb-2">Reporte de <?= esc(mb_strtolower($reporte['periodo'])) ?></h1>
            <p class="mb-0 opacity-75">Producción, riego, siembras y problemas del mes, listo para presentar o archivar.</p>
        </div>
        <div class="col-lg-5">
            <form method="GET" action="<?= base_url('huerto/reportes') ?>" class="selector-mes d-flex align-items-center gap-2 justify-content-lg-end">
                <a href="<?= base_url('huerto/reportes?mes=' . $anterior) ?>" class="btn btn-light rounded-circle <?= $anterior < $minimo ? 'disabled' : '' ?>" aria-label="Mes anterior"><i class="bi bi-chevron-left"></i></a>
                <input type="month" name="mes" class="form-control" value="<?= esc($mes) ?>" min="<?= esc($minimo) ?>" max="<?= date('Y-m') ?>" onchange="this.form.submit()" aria-label="Mes del reporte">
                <a href="<?= base_url('huerto/reportes?mes=' . $siguiente) ?>" class="btn btn-light rounded-circle <?= $siguiente > date('Y-m') ? 'disabled' : '' ?>" aria-label="Mes siguiente"><i class="bi bi-chevron-right"></i></a>
            </form>
        </div>
    </div>
</section>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-basket2"></i></div>
            <div>
                <div class="stat-valor"><?= $n($t['kg'], 1) ?> kg</div>
                <div class="stat-label">Cosechados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-droplet-half"></i></div>
            <div>
                <div class="stat-valor"><?= $n($t['litros'], 0) ?> L</div>
                <div class="stat-label">Agua aplicada</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check2-square"></i></div>
            <div>
                <div class="stat-valor"><?= $t['cumplimiento'] !== null ? $t['cumplimiento'] . ' %' : '-' ?></div>
                <div class="stat-label">Riegos registrados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-bug"></i></div>
            <div>
                <div class="stat-valor"><?= $t['problemas'] ?></div>
                <div class="stat-label">Problemas reportados</div>
            </div>
        </div>
    </div>
</div>

<?php if ($reporte['parcial']): ?>
    <div class="alert alert-info rounded-4 small"><i class="bi bi-info-circle me-1"></i>Es el mes en curso: el reporte llega hasta hoy y puede cambiar.</div>
<?php endif; ?>

<form method="GET" action="<?= base_url('huerto/reportes/pdf') ?>" class="stat-card mb-4 flex-wrap align-items-end">
    <input type="hidden" name="mes" value="<?= esc($mes) ?>">
    <div class="flex-grow-1">
        <label class="form-label fw-semibold small mb-1" for="observaciones" style="color: var(--huerto-verde-oscuro)">Observaciones para el reporte <span class="text-muted fw-normal">(opcional)</span></label>
        <textarea name="observaciones" id="observaciones" class="form-control rounded-3" rows="2" maxlength="600" placeholder="Ej.: el turno de agua se cortó una semana; la helada del 12 dañó los tomates."></textarea>
    </div>
    <button type="submit" class="btn btn-huerto"><i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF</button>
</form>

<div class="papel-contenedor mb-4">
    <div class="papel">
        <?= view('reportes/hoja', $reporte) ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Vista previa de las observaciones en la hoja
    const campo = document.getElementById('observaciones');
    const hoja = document.querySelector('.hoja .observaciones');
    campo.addEventListener('input', () => {
        hoja.innerText = campo.value || ' ';
    });
</script>
<?= $this->endSection() ?>
