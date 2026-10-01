<?= $this->extend('layout/template') ?>

<?= $this->section('contenido') ?>

<?php
    $total = count($cultivos ?? []);
    $porRegar = 0;
    $porCosechar = 0;
    $cosechados = 0;
    foreach ($cultivos ?? [] as $c) {
        if ($c['estado'] == 'Cosechado') { $cosechados++; }
        elseif ($c['alerta_cosecha']) { $porCosechar++; }
        elseif ($c['alerta_riego']) { $porRegar++; }
    }
?>

<section class="huerto-hero mb-4">
    <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
        <div class="col-lg-8">
            <span class="badge-fecha d-inline-flex align-items-center gap-2 mb-3">
                <i class="bi bi-calendar3"></i> Hoy, <?= date('d/m/Y') ?>
            </span>
            <h1 class="display-6 mb-2">Panel de Control y Alertas</h1>
            <p class="mb-0 opacity-75">Inventario y bitácora de cultivos.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="<?= base_url('huerto/crear') ?>" class="btn btn-huerto">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Cultivo
            </a>
        </div>
    </div>
</section>

<div class="row g-3 mb-5">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-flower2"></i></div>
            <div>
                <div class="stat-valor"><?= $total ?></div>
                <div class="stat-label">Cultivos totales</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-droplet-half"></i></div>
            <div>
                <div class="stat-valor"><?= $porRegar ?></div>
                <div class="stat-label">Requieren riego</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-basket2"></i></div>
            <div>
                <div class="stat-valor"><?= $porCosechar ?></div>
                <div class="stat-label">Listos para cosechar</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-subtle text-info-emphasis"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-valor"><?= $cosechados ?></div>
                <div class="stat-label">Cosechados</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="seccion-titulo mb-0"><i class="bi bi-list-ul me-2"></i>Mis cultivos</h5>
</div>

<div class="row">
    <?php if(empty($cultivos)): ?>
        <div class="col-12">
            <div class="estado-vacio">
                <i class="bi bi-tree"></i>
                <h5 class="fw-bold mt-3">Aún no hay cultivos registrados en tu huerto.</h5>
                <p class="text-muted mb-4">Empezá agregando tu primera planta para seguir su riego y cosecha.</p>
                <a href="<?= base_url('huerto/crear') ?>" class="btn btn-huerto">
                    <i class="bi bi-plus-lg me-1"></i> Agregar cultivo
                </a>
            </div>
        </div>
    <?php else: ?>

        <?php foreach ($cultivos as $planta): ?>

            <?php
                $borde = 'border-secondary';
                $encabezado = 'bg-light text-dark';
                $mensaje_alerta = 'En Crecimiento';

                if ($planta['estado'] == 'Cosechado') {
                    $borde = 'border-info';
                    $encabezado = 'bg-info text-white';
                    $mensaje_alerta = 'Cosechado y finalizado';
                }
                elseif ($planta['alerta_cosecha']) {
                    $borde = 'border-success';
                    $encabezado = 'bg-success text-white fw-bold';
                    $mensaje_alerta = '¡Listo para Cosechar!';
                }
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
                        <p class="card-text"><small>🕒 Riego cada <?= $planta['frecuencia_riego_dias'] ?> día(s) · <?= esc(\App\Models\CultivoModel::HORARIOS_RIEGO[$planta['horario_riego']]['nombre'] ?? 'Mañana') ?> · <?= number_format((float) $planta['cantidad_riego_litros'], 1, ',', '.') ?> L</small></p>
                        
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