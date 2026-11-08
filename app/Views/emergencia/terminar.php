<?= $this->extend('layout/template') ?>

<?= $this->section('contenido') ?>

<div class="em-<?= esc($emergencia['tipo'], 'attr') ?>">

    <section class="em-cabecera mb-4" aria-labelledby="tituloTerminar">
        <div class="em-rayas" aria-hidden="true"></div>
        <div class="cuerpo">
            <span class="em-estado fin"><i class="bi bi-check2-circle" aria-hidden="true"></i> Ya pasó</span>
            <h1 id="tituloTerminar"><i class="bi <?= $tipo['icono'] ?>" aria-hidden="true"></i>¿Qué daños dejó el <?= esc(mb_strtolower($tipo['nombre'])) ?>?</h1>
            <div class="meta">Recorré el huerto con calma y marcá lo que encontraste. Con eso armamos el plan de reparación.</div>
        </div>
    </section>

    <?= $this->include('layout/avisos') ?>

    <form action="<?= base_url('huerto/emergencia/' . $emergencia['id'] . '/terminar') ?>" method="POST">

        <div class="em-tarjeta mb-3 d-flex flex-wrap align-items-center gap-3">
            <div class="flex-grow-1">
                <strong>¿No se rompió nada?</strong>
                <div class="small text-muted">Cerrá la emergencia sin registrar daños.</div>
            </div>
            <button type="submit" name="sin_danos" value="1" class="btn btn-huerto px-3 py-2">
                <i class="bi bi-emoji-smile me-1"></i> No hubo daños
            </button>
        </div>

        <section class="em-tarjeta mb-3" aria-labelledby="tituloDanos">
            <h2 id="tituloDanos"><span class="badge text-bg-danger me-1">1</span> Marcá los daños que encontraste</h2>
            <div class="row g-2">
                <?php foreach ($danos as $clave => $dano): ?>
                    <div class="col-sm-6 col-lg-4">
                        <input type="checkbox" class="btn-check" name="danos[]" value="<?= esc($clave, 'attr') ?>" id="dano_<?= esc($clave, 'attr') ?>" autocomplete="off">
                        <label class="em-dano" for="dano_<?= esc($clave, 'attr') ?>">
                            <i class="bi <?= $dano['icono'] ?>" aria-hidden="true"></i> <?= esc($dano['nombre']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if ($cultivos): ?>
            <section class="em-tarjeta mb-3" aria-labelledby="tituloAfectados">
                <h2 id="tituloAfectados"><span class="badge text-bg-danger me-1">2</span> ¿Qué cultivos se dañaron?</h2>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($cultivos as $c): ?>
                        <input type="checkbox" class="btn-check" name="cultivos[]" value="<?= $c['id'] ?>" id="cultivo_<?= $c['id'] ?>" autocomplete="off">
                        <label class="em-chip" for="cultivo_<?= $c['id'] ?>"><?= esc(mb_strtolower($c['nombre_planta'])) ?><?= $c['variedad'] ? ' ' . esc($c['variedad']) : '' ?></label>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="em-tarjeta mb-3" aria-labelledby="tituloNotas">
            <h2 id="tituloNotas"><label for="notas"><span class="badge text-bg-danger me-1"><?= $cultivos ? 3 : 2 ?></span> ¿Algo más para anotar? <span class="fw-normal text-muted small">(opcional)</span></label></h2>
            <textarea name="notas" id="notas" class="form-control" rows="3" maxlength="1000" placeholder="Ej. se voló la media sombra del cantero 2"></textarea>
        </section>

        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold">
                <i class="bi bi-tools me-1"></i> Ver cómo reparar
            </button>
            <a href="<?= base_url('huerto/emergencia/' . $emergencia['id']) ?>" class="btn btn-link">Todavía no terminó, volver al plan</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
