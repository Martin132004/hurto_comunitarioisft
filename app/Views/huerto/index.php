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
    <!-- Verificamos si hay cultivos cargados -->
    <?php if(empty($cultivos)): ?>
        <div class="col-12">
            <div class="alert alert-info">Aún no hay cultivos registrados en tu huerto.</div>
        </div>
    <?php else: ?>
        
        <!-- Recorremos cada planta real de la base de datos -->
        <?php foreach ($cultivos as $planta): ?>
            
            <?php 
                // Definimos el color de la tarjeta según el algoritmo del controlador
                $borde = 'border-secondary';
                $encabezado = 'bg-light text-dark';
                $mensaje_alerta = 'En Crecimiento';

                // Priorizamos si ya está cosechado (ignoramos otras alertas)
                if ($planta['estado'] == 'Cosechado') {
                    $borde = 'border-info';
                    $encabezado = 'bg-info text-white';
                    $mensaje_alerta = 'Cosechado y finalizado';
                }
                // Alerta Verde: Listo para cosecha
                elseif ($planta['alerta_cosecha']) {
                    $borde = 'border-success';
                    $encabezado = 'bg-success text-white fw-bold';
                    $mensaje_alerta = '¡Listo para Cosechar!';
                } 
                // Alerta Roja/Amarilla: Requiere riego
                elseif ($planta['alerta_riego']) {
                    $borde = 'border-danger';
                    $encabezado = 'bg-danger text-white fw-bold';
                    $mensaje_alerta = '¡Requiere Riego Hoy!';
                }
            ?>

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm <?= $borde ?>">
                    <div class="card-header <?= $encabezado ?>">
                        <?= $mensaje_alerta ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-capitalize"><?= esc($planta['nombre_planta']) ?></h5>
                        <?php if($planta['variedad']): ?>
                            <h6 class="card-subtitle mb-2 text-muted"><?= esc($planta['variedad']) ?></h6>
                        <?php endif; ?>
                        
                        <hr>
                        <p class="card-text mb-1"><small>🌱 Sembrado: <?= $planta['fecha_siembra'] ?></small></p>
                        <p class="card-text"><small>💧 Último Riego: <?= $planta['ultimo_riego'] ? date('Y-m-d', strtotime($planta['ultimo_riego'])) : 'No registrado' ?></small></p>
                        
                        <!-- Botones de acción -->
                        <div class="mt-3 d-flex justify-content-between">
                            <?php if($planta['estado'] != 'Cosechado'): ?>
                                <a href="<?= base_url('huerto/riego/'.$planta['id']) ?>" class="btn btn-sm btn-outline-primary">Regar</a>
                                
                                <?php if($planta['alerta_cosecha']): ?>
                                    <a href="<?= base_url('huerto/estado/'.$planta['id']) ?>" class="btn btn-sm btn-success">Cosechar</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <a href="<?= base_url('huerto/eliminar/'.$planta['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar esta planta?')">🗑️</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>