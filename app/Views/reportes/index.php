<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    .cinta-indicadores-mes {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        border-top: 3px solid var(--hc-hoja);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    @media (min-width: 992px) {
        .cinta-indicadores-mes {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .indicador-mes-celda {
        padding: .9rem 1.15rem;
        border-right: 1px solid var(--hc-borde-fino);
        border-bottom: 1px solid var(--hc-borde-fino);
    }
    .indicador-mes-celda:last-child { border-right: 0; }
    .indicador-mes-etiqueta {
        font-size: .76rem;
        font-weight: 600;
        color: var(--hc-tinta-2);
        margin-bottom: .2rem;
        display: flex;
        align-items: center;
        gap: .35rem;
    }
    .indicador-mes-valor {
        font-family: var(--hc-serif);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--hc-tinta);
        line-height: 1.1;
    }
    .barra-exportar-pdf {
        background: var(--hc-papel);
        border: 1px solid var(--hc-borde);
        border-radius: 8px;
        padding: 1rem 1.25rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }
    .papel-contenedor {
        overflow-x: auto;
        border: 1px solid var(--hc-borde);
        border-radius: 8px;
        background: var(--hc-salvia);
        padding: 1.25rem;
    }
    .papel {
        background: #fff;
        min-width: 720px;
        max-width: 920px;
        margin: 0 auto;
        padding: 2.5rem 2.25rem;
        border: 1px solid var(--hc-borde);
    }
    .selector-mes .form-control {
        max-width: 190px;
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
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <span class="badge-fecha mb-2">
                <i class="bi bi-file-earmark-text text-success"></i> Informe N° <?= esc($reporte['numero']) ?>
            </span>
            <h1 class="mb-1">Reporte de <?= esc(mb_strtolower($reporte['periodo'])) ?></h1>
            <p class="mb-0">Producción, riego, siembras y problemas del mes, listo para presentar o archivar.</p>
        </div>
        <div class="col-lg-5">
            <form method="GET" action="<?= base_url('huerto/reportes') ?>" class="selector-mes d-flex align-items-center gap-2 justify-content-lg-end">
                <a href="<?= base_url('huerto/reportes?mes=' . $anterior) ?>" class="btn btn-light px-2 py-1 <?= $anterior < $minimo ? 'disabled' : '' ?>" aria-label="Mes anterior"><i class="bi bi-chevron-left"></i></a>
                <input type="month" name="mes" class="form-control" value="<?= esc($mes) ?>" min="<?= esc($minimo) ?>" max="<?= date('Y-m') ?>" onchange="this.form.submit()" aria-label="Mes del reporte">
                <a href="<?= base_url('huerto/reportes?mes=' . $siguiente) ?>" class="btn btn-light px-2 py-1 <?= $siguiente > date('Y-m') ? 'disabled' : '' ?>" aria-label="Mes siguiente"><i class="bi bi-chevron-right"></i></a>
            </form>
        </div>
    </div>
</section>

<div class="cinta-indicadores-mes">
    <div class="indicador-mes-celda">
        <div class="indicador-mes-etiqueta"><i class="bi bi-basket2-fill text-warning"></i> Cosechados</div>
        <div class="indicador-mes-valor"><?= $n($t['kg'], 1) ?> kg</div>
    </div>
    <div class="indicador-mes-celda">
        <div class="indicador-mes-etiqueta"><i class="bi bi-droplet-fill text-primary"></i> Agua aplicada</div>
        <div class="indicador-mes-valor"><?= $n($t['litros'], 0) ?> L</div>
    </div>
    <div class="indicador-mes-celda">
        <div class="indicador-mes-etiqueta"><i class="bi bi-check2-square text-success"></i> Riegos cumplidos</div>
        <div class="indicador-mes-valor"><?= $t['cumplimiento'] !== null ? $t['cumplimiento'] . ' %' : '—' ?></div>
    </div>
    <div class="indicador-mes-celda">
        <div class="indicador-mes-etiqueta"><i class="bi bi-bug text-danger"></i> Problemas reportados</div>
        <div class="indicador-mes-valor"><?= $t['problemas'] ?></div>
    </div>
</div>

<?php if ($reporte['parcial']): ?>
    <div class="alert alert-info mb-3"><i class="bi bi-info-circle-fill me-1"></i>Es el mes en curso: el reporte llega hasta hoy y puede cambiar.</div>
<?php endif; ?>

<form method="GET" action="<?= base_url('huerto/reportes/pdf') ?>" class="barra-exportar-pdf mb-4">
    <input type="hidden" name="mes" value="<?= esc($mes) ?>">
    <div class="flex-grow-1">
        <label class="form-label mb-1" for="observaciones"><i class="bi bi-journal-text me-1"></i> Observaciones para el reporte <span class="text-muted fw-normal">(opcional)</span></label>
        <textarea name="observaciones" id="observaciones" class="form-control" rows="2" maxlength="600" placeholder="Ej.: el turno de agua se cortó una semana; la helada del 12 dañó los tomates."></textarea>
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
    const campo = document.getElementById('observaciones');
    const hoja = document.querySelector('.hoja .observaciones');
    campo.addEventListener('input', () => {
        hoja.innerText = campo.value || ' ';
    });
</script>
<?= $this->endSection() ?>
