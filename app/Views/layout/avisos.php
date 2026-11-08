<?php // Resultado de la acción anterior: confirmación en verde, error en rojo ?>
<?php if ($mensaje = session()->getFlashdata('mensaje')): ?>
    <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i> <?= esc($mensaje) ?></div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger rounded-4" role="alert"><i class="bi bi-exclamation-circle me-1"></i> <?= esc($error) ?></div>
<?php endif; ?>
