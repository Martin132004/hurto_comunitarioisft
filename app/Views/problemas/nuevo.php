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
    .opcion:hover {
        border-color: rgba(31, 122, 77, .3);
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
    .opcion ul {
        padding-left: 1rem;
        margin: .4rem 0 0;
    }
    .zona-foto {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        cursor: pointer;
        border: 2px dashed rgba(31, 122, 77, .3);
        border-radius: 1.25rem;
        background: var(--huerto-crema);
        padding: 1.5rem;
        text-align: center;
        color: var(--huerto-verde-oscuro);
    }
    .zona-foto i {
        font-size: 2rem;
        color: var(--huerto-verde);
    }
    .zona-foto img {
        max-height: 260px;
        max-width: 100%;
        border-radius: 1rem;
    }
    .paso {
        border-top: 1px solid rgba(15, 61, 46, .08);
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<?php $elegido = (int) old('cultivo_id', $elegido ?: ''); ?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <a href="<?= base_url('huerto/problemas') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Volver a problemas
        </a>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i> <?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <div class="panel-form">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <h4 class="fw-bold mb-1"><i class="bi bi-camera me-2"></i>Reportar un problema</h4>
                <p class="mb-0 opacity-75 small">Con una foto y lo que ves, el técnico puede decirte qué es y cómo tratarlo.</p>
            </div>
            <div class="p-4 p-md-5">
                <form action="<?= base_url('huerto/problemas/nuevo') ?>" method="POST" enctype="multipart/form-data" id="formProblema">
                    <label class="form-label" for="cultivo"><i class="bi bi-flower2 me-1"></i> ¿En qué cultivo?</label>
                    <select name="cultivo_id" id="cultivo" class="form-select rounded-3 mb-1" style="max-width: 420px;">
                        <option value="">Varios cultivos o todo el huerto</option>
                        <?php foreach ($cultivos as $cultivo): ?>
                            <option value="<?= $cultivo['id'] ?>" <?= $elegido === (int) $cultivo['id'] ? 'selected' : '' ?>>
                                <?= esc($cultivo['nombre_planta']) ?><?= $cultivo['variedad'] ? ' (' . esc($cultivo['variedad']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="paso">
                        <label class="form-label"><i class="bi bi-image me-1"></i> Foto <span class="text-muted fw-normal">(opcional, pero ayuda mucho)</span></label>
                        <label class="zona-foto" for="foto">
                            <span id="fotoVacia">
                                <i class="bi bi-camera"></i>
                                <span class="d-block fw-semibold">Sacá o elegí una foto</span>
                                <span class="d-block small text-muted">De cerca, con buena luz, que se vea la hoja o el bicho. Si podés, también el envés de la hoja.</span>
                            </span>
                            <img id="fotoVista" alt="Vista previa de la foto" hidden>
                            <span id="fotoInfo" class="small text-muted"></span>
                        </label>
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp" class="visually-hidden">
                    </div>

                    <div class="paso">
                        <label class="form-label mb-0"><i class="bi bi-search me-1"></i> ¿Se parece a alguno de estos?</label>
                        <p class="small text-muted" id="ayudaOpciones">Si no estás seguro, dejá "No sé qué es": el técnico lo va a identificar.</p>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4" data-problema="">
                                <input type="radio" class="btn-check" name="problema" id="problema_ninguno" value="" <?= old('problema', '') === '' ? 'checked' : '' ?>>
                                <label class="opcion" for="problema_ninguno">
                                    <span class="titulo"><i class="bi bi-question-circle me-1"></i>No sé qué es</span>
                                    <div class="small text-muted mt-1">Contalo abajo y subí una foto.</div>
                                </label>
                            </div>
                            <?php foreach ($catalogo as $clave => $p): ?>
                                <?php $tipo = $tipos[$p['tipo']] ?? $tipos['plaga']; ?>
                                <div class="col-md-6 col-lg-4" data-problema="<?= esc($clave) ?>">
                                    <input type="radio" class="btn-check" name="problema" id="problema_<?= esc($clave) ?>" value="<?= esc($clave) ?>" <?= old('problema') === $clave ? 'checked' : '' ?>>
                                    <label class="opcion" for="problema_<?= esc($clave) ?>">
                                        <span class="titulo"><i class="bi <?= $tipo['icono'] ?> text-<?= $tipo['color'] ?> me-1"></i><?= esc($p['nombre']) ?></span>
                                        <ul class="small text-muted">
                                            <?php foreach (array_slice($p['sintomas'], 0, 2) as $sintoma): ?>
                                                <li><?= esc($sintoma) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="paso">
                        <label class="form-label"><i class="bi bi-speedometer2 me-1"></i> ¿Cuánto afecta?</label>
                        <div class="row g-3">
                            <?php foreach ($gravedades as $clave => $gravedad): ?>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="gravedad" id="gravedad_<?= $clave ?>" value="<?= $clave ?>" <?= old('gravedad', 'pocas') === $clave ? 'checked' : '' ?>>
                                    <label class="opcion" for="gravedad_<?= $clave ?>">
                                        <span class="titulo"><span class="badge rounded-pill text-bg-<?= $gravedad['color'] ?> me-1">&nbsp;</span><?= $gravedad['nombre'] ?></span>
                                        <div class="small text-muted mt-1"><?= $gravedad['detalle'] ?></div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="paso">
                        <label class="form-label" for="descripcion"><i class="bi bi-chat-left-text me-1"></i> ¿Qué ves?</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="4" maxlength="2000" placeholder="Ej.: desde hace 3 días las hojas de abajo tienen manchas amarillas y algunas se secaron. Hubo viento fuerte el fin de semana."><?= esc(old('descripcion', '')) ?></textarea>
                        <div class="form-text">Contá desde cuándo pasa, en qué parte de la planta y si cambió algo (viento, helada, riego, abono).</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('huerto/problemas') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-huerto" id="enviar"><i class="bi bi-send me-1"></i> Enviar reporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
    const POSIBLES = <?= json_encode($posibles) ?>;
    const cultivo = document.getElementById('cultivo');
    const ayuda = document.getElementById('ayudaOpciones');

    // Mostramos solo los problemas que pueden afectar al cultivo elegido
    function filtrarOpciones() {
        const posibles = POSIBLES[cultivo.value];
        document.querySelectorAll('[data-problema]').forEach(opcion => {
            const clave = opcion.dataset.problema;
            const visible = clave === '' || ! posibles || posibles.includes(clave);
            opcion.hidden = ! visible;
            const radio = opcion.querySelector('input');
            if (! visible && radio.checked) {
                document.getElementById('problema_ninguno').checked = true;
            }
        });
        const nombre = cultivo.options[cultivo.selectedIndex].text.trim();
        ayuda.textContent = (posibles ? 'Estos son los problemas más comunes en ' + nombre + '. ' : '')
            + 'Si no estás seguro, dejá "No sé qué es": el técnico lo va a identificar.';
    }
    cultivo.addEventListener('change', filtrarOpciones);
    filtrarOpciones();

    // ---------- Foto ----------
    const foto = document.getElementById('foto');
    const vista = document.getElementById('fotoVista');
    const vacia = document.getElementById('fotoVacia');
    const info = document.getElementById('fotoInfo');
    const MAX_LADO = 1600;

    // Achicamos las fotos grandes del celular antes de subirlas: con poca señal se envían más rápido
    async function achicar(archivo) {
        if (archivo.size < 1024 * 1024 || ! window.createImageBitmap || ! window.DataTransfer) {
            return archivo;
        }
        try {
            const imagen = await createImageBitmap(archivo);
            const escala = Math.min(1, MAX_LADO / Math.max(imagen.width, imagen.height));
            const lienzo = document.createElement('canvas');
            lienzo.width = Math.round(imagen.width * escala);
            lienzo.height = Math.round(imagen.height * escala);
            lienzo.getContext('2d').drawImage(imagen, 0, 0, lienzo.width, lienzo.height);
            const blob = await new Promise(resolver => lienzo.toBlob(resolver, 'image/jpeg', 0.82));
            if (! blob || blob.size >= archivo.size) {
                return archivo;
            }
            return new File([blob], archivo.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' });
        } catch (e) {
            return archivo;
        }
    }

    foto.addEventListener('change', async () => {
        const original = foto.files[0];
        if (! original) {
            vista.hidden = true;
            vacia.hidden = false;
            info.textContent = '';
            return;
        }

        info.textContent = 'Preparando la foto…';
        const archivo = await achicar(original);
        if (archivo !== original) {
            const lista = new DataTransfer();
            lista.items.add(archivo);
            foto.files = lista.files;
        }

        vista.src = URL.createObjectURL(archivo);
        vista.hidden = false;
        vacia.hidden = true;
        info.textContent = (archivo.size / 1024 / 1024).toFixed(1).replace('.', ',') + ' MB · tocá para cambiarla';
    });

    document.getElementById('formProblema').addEventListener('submit', () => {
        const boton = document.getElementById('enviar');
        boton.disabled = true;
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando…';
    });
})();
</script>
<?= $this->endSection() ?>
