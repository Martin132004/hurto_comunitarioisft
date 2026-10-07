<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe mensual N° <?= esc($numero) ?></title>
    <style>
        @page { margin: 14mm 12mm 18mm 12mm; }
        body { margin: 0; }
        .pie {
            position: fixed;
            bottom: -11mm;
            left: 0;
            right: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: #8a978f;
            border-top: 1px solid #dfe6e1;
            padding-top: 3px;
        }
    </style>
</head>
<body>
    <div class="pie">
        Informe mensual de seguimiento N° <?= esc($numero) ?> · <?= esc($huerto['nombre']) ?> · <?= esc($periodo) ?>
    </div>

    <?= $this->include('reportes/hoja') ?>
</body>
</html>
