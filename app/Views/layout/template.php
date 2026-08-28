<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huerto Comunitario</title>
    <!-- Cargamos Bootstrap 5 directamente desde su CDN oficial -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <?= $this->renderSection('estilos') ?>
</head>
<body class="bg-light">

    <!-- Menú de navegación-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">Huerto Comunitario</a>
        </div>
    </nav>

    <!-- Contenedor principal donde se inyectará el contenido de las otras vistas -->
    <main class="container">
        <?= $this->renderSection('contenido') ?>
    </main>

    <!-- Pie de página simple -->
    <footer class="text-center mt-5 mb-3 text-muted">
        <small>&copy; <?= date('Y') ?> - Trabajo Práctico PP III - rodriguez && carrizo</small>
    </footer>

    <!-- Script de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts personalizados -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>