<?php // Resultado de la acción anterior: confirmación en verde, error en rojo ?>
<?php if ($mensaje = session()->getFlashdata('mensaje')): ?>
    <div class="alert alert-success mb-3"><i class="bi bi-check-circle-fill me-1"></i> <?= esc($mensaje) ?></div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3" role="alert"><i class="bi bi-exclamation-circle-fill me-1"></i> <?= esc($error) ?></div>
<?php endif; ?>
