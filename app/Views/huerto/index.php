<!-- Le decimos que use la plantilla base que acabamos de crear -->
<?= $this->extend('layout/template') ?>

<!-- Todo lo que esté aquí adentro se insertará en el main de la plantilla base -->
<?= $this->section('contenido') ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Panel de Control y Alertas</h2>
        <p class="text-muted">inventario y bitácora de cultivos.</p>
    </div>
    <div class="col-md-4 text-end">
        <!-- Botón para ir al formulario de crear (lo haremos después) -->
        <a href="<?= base_url('huerto/crear') ?>" class="btn btn-primary">+ Nuevo Cultivo</a>
    </div>
</div>

<div class="row">
    <!-- Aquí simulamos un cultivo que requiere riego (Alerta Amarilla/Roja) -->
    <div class="col-md-4 mb-3">
        <div class="card border-danger shadow-sm">
            <div class="card-header bg-danger text-white fw-bold">
                ¡Requiere Riego Hoy!
            </div>
            <div class="card-body">
                <h5 class="card-title">Tomate Cherry</h5>
                <p class="card-text mb-1"><small>Plantado: 2026-08-01</small></p>
                <p class="card-text"><small>Último riego: Hace 3 días</small></p>
                
                <a href="<?= base_url('huerto/riego') ?>" class="btn btn-sm btn-outline-primary">Registrar Riego</a>
            </div>
        </div>
    </div>

    <!-- Aquí simulamos un cultivo listo para cosechar (Alerta Verde) -->
    <div class="col-md-4 mb-3">
        <div class="card border-success shadow-sm">
            <div class="card-header bg-success text-white fw-bold">
                ¡Listo para Cosechar!
            </div>
            <div class="card-body">
                <h5 class="card-title">Lechuga</h5>
                <p class="card-text mb-1"><small>Plantado: 2026-07-15</small></p>
                <span class="badge bg-success">Cosecha estimada cumplida</span>
                
                <div class="mt-3">
                    <a href="<?= base_url('huerto/cosechar') ?>" class="btn btn-sm btn-success">Marcar Cosechado</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cultivo normal en crecimiento -->
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                En Crecimiento
            </div>
            <div class="card-body">
                <h5 class="card-title">Zanahoria</h5>
                <p class="card-text mb-1"><small>Faltan 45 días para cosecha</small></p>
                <span class="badge bg-secondary">Estado Normal</span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>