<?php
    $error = session()->getFlashdata('error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · AgroTech</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('img/agrotech-icono.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/agrotech-tokens.css') ?>" rel="stylesheet">
    <style>
        :root {
            /* Borde de los campos: gris medio con 3:1 sobre blanco para que se distinga el campo */
            --campo-borde: #8b978f;
        }

        body {
            font-family: var(--bs-body-font-family);
            color: var(--texto);
            font-size: .9375rem;
            -webkit-font-smoothing: antialiased;
            background-color: var(--fondo);
            overflow-x: hidden;
        }

        h1 { font-family: 'Outfit', var(--bs-body-font-family); letter-spacing: -.015em; }

        /* ---------- Estructura ---------- */
        .acceso {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }
        .tarjeta {
            width: 100%;
            max-width: 392px;
            background: var(--superficie);
            border: 1px solid var(--borde);
            border-radius: calc(var(--radio) + 4px);
            box-shadow: 0 1px 2px rgba(10, 58, 31, .06), 0 20px 48px -24px rgba(10, 58, 31, .25);
            padding: 2rem 2rem 1.6rem;
            animation: aparece .45s var(--ease) both;
        }
        @keyframes aparece {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: none; }
        }
        @media (max-width: 420px) {
            .acceso { padding: 1rem .75rem; }
            .tarjeta { padding: 1.6rem 1.25rem 1.4rem; }
        }

        /* ---------- Encabezado: logo, título, subtítulo ---------- */
        .encabezado { text-align: center; margin-bottom: 1.5rem; }
        .encabezado .logo { display: block; height: 88px; width: auto; margin: 0 auto 1.1rem; }
        .encabezado h1 { font-size: 1.4rem; font-weight: 600; margin: 0 0 .3rem; }
        .encabezado p { color: var(--texto-2); font-size: .875rem; margin: 0; }
        @media (max-height: 640px) {
            .encabezado .logo { height: 68px; margin-bottom: .8rem; }
            .encabezado { margin-bottom: 1.2rem; }
        }

        /* ---------- Error de acceso ---------- */
        .error-acceso {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .6rem .8rem;
            margin-bottom: 1.1rem;
            border-radius: var(--radio-sm);
            background: var(--alerta-100);
            border: 1px solid var(--alerta-200);
            color: #7d2118;
            font-size: .85rem;
            font-weight: 500;
            animation: aparece .3s var(--ease) both;
        }
        .error-acceso i { color: var(--alerta-600); flex-shrink: 0; }

        /* ---------- Campos ---------- */
        .grupo + .grupo { margin-top: 1rem; }
        .form-label { font-weight: 600; font-size: .85rem; margin-bottom: .35rem; }

        .campo { position: relative; }
        .campo > .icono {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--texto-2);
            pointer-events: none;
            transition: color .15s;
        }
        .campo .form-control {
            height: 46px;
            padding: .6rem .9rem .6rem 2.5rem;
            background-color: var(--superficie);
            border: 1px solid var(--campo-borde);
            border-radius: var(--radio-sm);
            color: var(--texto);
            transition: border-color .15s, box-shadow .15s;
        }
        .campo.con-boton .form-control { padding-right: 3rem; }
        .campo .form-control:hover { border-color: var(--at-500); }
        .campo .form-control:focus {
            background-color: var(--superficie);
            border-color: var(--at-600);
            box-shadow: 0 0 0 3px rgba(57, 163, 63, .15);
        }
        .campo:focus-within > .icono { color: var(--at-700); }

        /* Error: borde rojo suave, sin el ícono de Bootstrap dentro del campo */
        .campo .form-control.is-invalid {
            border-color: var(--alerta-600);
            background-image: none;
            padding-right: .9rem;
        }
        .campo.con-boton .form-control.is-invalid { padding-right: 3rem; }
        .campo .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(192, 57, 43, .12); }
        .campo .form-control.is-invalid ~ .icono,
        .campo:has(.is-invalid) > .icono { color: var(--alerta-600); }
        .mensaje-campo { font-size: .8rem; color: var(--alerta-600); margin-top: .35rem; }

        .ver-clave {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: var(--texto-2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
        }
        .ver-clave:hover { background: var(--fondo); color: var(--texto); }
        .ver-clave:focus-visible { outline: 2px solid var(--at-700); outline-offset: 0; }

        .aviso-mayus { font-size: .8rem; color: var(--cosecha-600); margin-top: .35rem; }

        /* ---------- Botón principal ---------- */
        .btn-entrar {
            height: 48px;
            margin-top: 1.4rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            background: var(--at-600);
            border: 1px solid var(--at-700);
            color: #fff;
            font-weight: 600;
            border-radius: var(--radio-sm);
            box-shadow: 0 1px 0 rgba(255, 255, 255, .15) inset, 0 6px 16px -8px rgba(31, 125, 67, .7);
            transition: background .15s, border-color .15s, transform .15s var(--ease);
        }
        .btn-entrar:hover { background: var(--at-700); border-color: var(--at-800); color: #fff; }
        .btn-entrar:focus-visible { background: var(--at-700); color: #fff; outline: 2px solid var(--at-700); outline-offset: 2px; }
        .btn-entrar:active { transform: scale(.985); }
        .btn-entrar:disabled,
        .btn-entrar.cargando {
            background: var(--at-700);
            border-color: var(--at-700);
            color: #fff;
            opacity: 1;
            cursor: progress;
        }

        /* ---------- Textos secundarios ---------- */
        .nota { font-size: .78rem; color: var(--texto-2); text-align: center; margin: 1.1rem 0 0; line-height: 1.45; text-wrap: balance; }

        .version {
            margin-top: 1.1rem;
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .72rem;
            color: var(--texto-2);
        }
        .version code {
            font-family: 'Geist Mono', ui-monospace, monospace;
            font-size: .7rem;
            color: var(--texto-2);
            background: var(--superficie);
            border: 1px solid var(--borde);
            border-radius: 6px;
            padding: .1rem .4rem;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

<main class="acceso">
    <div class="tarjeta">
        <header class="encabezado">
            <img src="<?= base_url('img/agrotech-logo.png') ?>" alt="AgroTech" class="logo">
            <h1>Iniciar sesión</h1>
            <p>Ingresá con tus credenciales para continuar.</p>
        </header>

        <?php if ($error): ?>
            <div class="error-acceso" role="alert" id="errorAcceso">
                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                <span><?= esc($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="POST" id="formLogin" novalidate>
            <div class="grupo">
                <label class="form-label" for="usuario">Usuario</label>
                <div class="campo">
                    <input type="text" class="form-control <?= $error ? 'is-invalid' : '' ?>" id="usuario" name="usuario"
                           value="<?= esc(old('usuario', '')) ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required
                           <?= $error ? 'aria-invalid="true" aria-describedby="errorAcceso"' : '' ?> <?= old('usuario') ? '' : 'autofocus' ?>>
                    <i class="bi bi-person icono" aria-hidden="true"></i>
                </div>
                <div class="mensaje-campo" id="errorUsuario" hidden>Ingresá tu usuario.</div>
            </div>

            <div class="grupo">
                <label class="form-label" for="password">Contraseña</label>
                <div class="campo con-boton">
                    <input type="password" class="form-control <?= $error ? 'is-invalid' : '' ?>" id="password" name="password"
                           autocomplete="current-password" required
                           <?= $error ? 'aria-invalid="true" aria-describedby="errorAcceso"' : '' ?> <?= old('usuario') ? 'autofocus' : '' ?>>
                    <i class="bi bi-lock icono" aria-hidden="true"></i>
                    <button type="button" class="ver-clave" id="verClave" aria-label="Mostrar contraseña" aria-pressed="false" aria-controls="password">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="mensaje-campo" id="errorPassword" hidden>Ingresá tu contraseña.</div>
                <div class="aviso-mayus" id="avisoMayus" hidden>
                    <i class="bi bi-capslock-fill me-1" aria-hidden="true"></i>Bloq Mayús está activado
                </div>
            </div>

            <button type="submit" class="btn btn-entrar w-100" id="btnEntrar">
                <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>Iniciar sesión
            </button>
        </form>

        <p class="nota">¿Olvidaste tu contraseña? Pedile al administrador que la cambie.</p>
    </div>

    <div class="version">
        <span>AgroTech</span>
        <code>v<?= AGROTECH_VERSION ?></code>
    </div>
</main>

<script>
    (function () {
        const clave = document.getElementById('password');
        const ver = document.getElementById('verClave');
        const icono = ver.querySelector('i');

        // Mostrar u ocultar la contraseña
        ver.addEventListener('click', () => {
            const mostrar = clave.type === 'password';
            clave.type = mostrar ? 'text' : 'password';
            icono.className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
            ver.setAttribute('aria-pressed', mostrar);
            ver.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
            clave.focus();
        });

        // Aviso de Bloq Mayús mientras se escribe la contraseña
        const aviso = document.getElementById('avisoMayus');
        const revisarMayus = e => { if (e.getModifierState) aviso.hidden = !e.getModifierState('CapsLock'); };
        clave.addEventListener('keyup', revisarMayus);
        clave.addEventListener('keydown', revisarMayus);
        clave.addEventListener('blur', () => aviso.hidden = true);

        const form = document.getElementById('formLogin');
        const boton = document.getElementById('btnEntrar');
        const textoBoton = boton.innerHTML;
        const mensajes = { usuario: document.getElementById('errorUsuario'), password: document.getElementById('errorPassword') };

        const marcar = (campo, conError) => {
            campo.classList.toggle('is-invalid', conError);
            mensajes[campo.name].hidden = !conError;
            if (conError) {
                campo.setAttribute('aria-invalid', 'true');
                campo.setAttribute('aria-describedby', mensajes[campo.name].id);
            } else {
                campo.removeAttribute('aria-invalid');
                campo.removeAttribute('aria-describedby');
            }
        };

        // Campos vacíos: aviso debajo del campo. Con datos: estado de carga y sin envíos dobles.
        form.addEventListener('submit', e => {
            if (boton.disabled) {
                e.preventDefault();
                return;
            }
            const vacios = [form.usuario, form.password].filter(campo => !campo.value.trim());
            [form.usuario, form.password].forEach(campo => marcar(campo, vacios.includes(campo)));
            if (vacios.length) {
                e.preventDefault();
                vacios[0].focus();
                return;
            }
            boton.disabled = true;
            boton.classList.add('cargando');
            boton.setAttribute('aria-busy', 'true');
            boton.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Iniciando sesión…';
        });
        [form.usuario, form.password].forEach(campo => campo.addEventListener('input', () => {
            if (campo.classList.contains('is-invalid')) marcar(campo, false);
        }));

        // Si se vuelve a esta página con "atrás", el botón no queda bloqueado
        window.addEventListener('pageshow', e => {
            if (!e.persisted) return;
            boton.disabled = false;
            boton.classList.remove('cargando');
            boton.removeAttribute('aria-busy');
            boton.innerHTML = textoBoton;
        });
    })();
</script>
</body>
</html>
