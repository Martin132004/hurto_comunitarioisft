<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huerto Comunitario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --huerto-verde: #1f7a4d;
            --huerto-verde-oscuro: #0f3d2e;
            --huerto-lima: #a7e37a;
            --huerto-crema: #f5f7f2;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--huerto-crema);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main { flex: 1; }

        /* Barra de navegación */
        .huerto-nav {
            background: rgba(15, 61, 46, .92);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, .12);
        }
        .huerto-nav .navbar-brand {
            font-weight: 800;
            letter-spacing: -.02em;
        }
        .huerto-logo {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--huerto-lima), #4cc38a);
            color: var(--huerto-verde-oscuro);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .huerto-nav .nav-link {
            color: rgba(255, 255, 255, .75);
            font-weight: 500;
            border-radius: 50rem;
            padding: .4rem 1rem !important;
            transition: all .2s;
        }
        .huerto-nav .nav-link:hover,
        .huerto-nav .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .12);
        }

        /* Encabezado tipo hero */
        .huerto-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            padding: 2.5rem;
            color: #fff;
            background:
                radial-gradient(circle at 85% 20%, rgba(167, 227, 122, .45), transparent 45%),
                linear-gradient(135deg, var(--huerto-verde-oscuro), var(--huerto-verde));
            box-shadow: 0 20px 40px -20px rgba(15, 61, 46, .6);
        }
        .huerto-hero::after {
            content: "\F4A6";
            font-family: "bootstrap-icons";
            position: absolute;
            right: -1rem;
            bottom: -3rem;
            font-size: 12rem;
            opacity: .08;
            transform: rotate(-15deg);
        }
        .huerto-hero h1 {
            font-weight: 800;
            letter-spacing: -.03em;
        }
        .huerto-hero .badge-fecha {
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 50rem;
            padding: .35rem .9rem;
            font-size: .8rem;
            font-weight: 600;
        }

        /* Botones del layout */
        .btn-huerto {
            background: var(--huerto-lima);
            color: var(--huerto-verde-oscuro);
            font-weight: 700;
            border: 0;
            border-radius: 50rem;
            padding: .7rem 1.5rem;
            box-shadow: 0 8px 20px -8px rgba(167, 227, 122, .9);
            transition: transform .2s, box-shadow .2s;
        }
        .btn-huerto:hover {
            background: #b9ee90;
            color: var(--huerto-verde-oscuro);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(167, 227, 122, 1);
        }

        /* Tarjetas de resumen */
        .stat-card {
            background: #fff;
            border-radius: 1.25rem;
            padding: 1.25rem;
            border: 1px solid rgba(15, 61, 46, .06);
            box-shadow: 0 4px 16px -8px rgba(15, 61, 46, .15);
            display: flex;
            align-items: center;
            gap: 1rem;
            height: 100%;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .stat-valor {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
            color: var(--huerto-verde-oscuro);
        }
        .stat-label {
            font-size: .8rem;
            color: #6c757d;
            font-weight: 500;
        }

        .seccion-titulo {
            font-weight: 700;
            color: var(--huerto-verde-oscuro);
            letter-spacing: -.01em;
        }

        /* Estado vacío */
        .estado-vacio {
            background: #fff;
            border: 2px dashed rgba(31, 122, 77, .25);
            border-radius: 1.5rem;
            padding: 3rem 1.5rem;
            text-align: center;
        }
        .estado-vacio i {
            font-size: 3rem;
            color: var(--huerto-verde);
        }

        /* Formularios */
        .panel-form {
            background: #fff;
            border-radius: 1.5rem;
            border: 1px solid rgba(15, 61, 46, .06);
            box-shadow: 0 20px 40px -24px rgba(15, 61, 46, .35);
            overflow: hidden;
        }
        .panel-form .form-control {
            border-radius: .8rem;
            padding: .7rem 1rem;
            background: var(--huerto-crema);
            border-color: transparent;
        }
        .panel-form .form-control:focus {
            background: #fff;
            border-color: var(--huerto-verde);
            box-shadow: 0 0 0 .25rem rgba(31, 122, 77, .15);
        }
        .panel-form .form-label {
            font-weight: 600;
            font-size: .875rem;
            color: var(--huerto-verde-oscuro);
        }

        /* Pie de página */
        .huerto-footer {
            background: var(--huerto-verde-oscuro);
            color: rgba(255, 255, 255, .6);
        }
    </style>
    <?= $this->renderSection('estilos') ?>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark huerto-nav sticky-top py-3 mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('/') ?>">
                <span class="huerto-logo"><i class="bi bi-flower1"></i></span>
                Huerto Comunitario
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav ms-auto gap-1 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= base_url('/') ?>"><i class="bi bi-grid-1x2 me-1"></i> Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('huerto/crear') ? 'active' : '' ?>" href="<?= base_url('huerto/crear') ?>"><i class="bi bi-plus-circle me-1"></i> Nuevo cultivo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        <?= $this->renderSection('contenido') ?>
    </main>

    <footer class="huerto-footer py-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span class="d-flex align-items-center gap-2 text-white fw-semibold">
                <i class="bi bi-flower1"></i> Huerto Comunitario
            </span>
            <small>&copy; <?= date('Y') ?> - Trabajo Práctico PP III - rodriguez &amp; carrizo</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
