<?= $this->extend('layout/template') ?>
<?= $this->section('contenido') ?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Agregar Nuevo Cultivo</h4>
            </div>
            <div class="card-body">
                <form action="<?= base_url('huerto/crear') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Planta</label>
                        <input type="text" name="nombre_planta" class="form-control" required placeholder="Ej. Tomate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Variedad (Opcional)</label>
                        <input type="text" name="variedad" class="form-control" placeholder="Ej. Perita">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Siembra</label>
                            <input type="date" name="fecha_siembra" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Días para Cosecha</label>
                            <input type="number" name="dias_cosecha_estimados" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Frecuencia Riego (Días)</label>
                            <input type="number" name="frecuencia_riego_dias" class="form-control" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="<?= base_url('/') ?>" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>