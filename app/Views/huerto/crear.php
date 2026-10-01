<?= $this->extend('layout/template') ?>
<?= $this->section('contenido') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="<?= base_url('/') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>

        <div class="panel-form">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <h4 class="fw-bold mb-1"><i class="bi bi-flower1 me-2"></i>Agregar Nuevo Cultivo</h4>
                <p class="mb-0 opacity-75 small">Completá los datos para empezar a seguir su riego y cosecha.</p>
            </div>
            <div class="p-4 p-md-5">
                <form action="<?= base_url('huerto/crear') ?>" method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-tag me-1"></i> Nombre de la Planta</label>
                            <input type="text" name="nombre_planta" class="form-control" required placeholder="Ej. Tomate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-bookmark me-1"></i> Variedad (Opcional)</label>
                            <input type="text" name="variedad" class="form-control" placeholder="Ej. Perita">
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-event me-1"></i> Fecha de Siembra</label>
                            <input type="date" name="fecha_siembra" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-basket2 me-1"></i> Días para Cosecha</label>
                            <input type="number" name="dias_cosecha_estimados" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-droplet me-1"></i> Frecuencia Riego (Días)</label>
                            <input type="number" name="frecuencia_riego_dias" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-clock me-1"></i> Horario de Riego</label>
                            <select name="horario_riego" class="form-control" required>
                                <?php foreach ($horarios as $clave => $horario): ?>
                                    <option value="<?= $clave ?>"><?= $horario['nombre'] ?> (<?= implode(' y ', $horario['franjas']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-cup-straw me-1"></i> Cantidad por Riego (Litros)</label>
                            <input type="number" name="cantidad_riego_litros" class="form-control" step="0.1" min="0.1" required placeholder="Ej. 1.5">
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
