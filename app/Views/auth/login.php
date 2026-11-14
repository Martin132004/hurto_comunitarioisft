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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,600;0,8..60,700;1,8..60,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/agrotech-tokens.css') ?>" rel="stylesheet">
    <style>
        body {
            font-family: var(--hc-sans);
            color: var(--hc-tinta);
            font-size: .9375rem;
            -webkit-font-smoothing: antialiased;
            background-color: var(--hc-lienzo);
            overflow-x: hidden;
        }

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
            max-width: 400px;
            background: var(--hc-papel);
            border: 1px solid var(--hc-borde);
            border-top: 4px solid var(--hc-hoja);
            border-radius: 8px;
            overflow: hidden;
        }

        .encabezado {
            background: var(--hc-salvia);
            border-bottom: 1px solid var(--hc-borde);
            padding: 1.5rem 1.75rem 1.25rem;
            text-align: center;
        }
        .encabezado .logo {
            display: block;
            height: 76px;
            width: auto;
            margin: 0 auto .9rem;
        }
        .encabezado h1 {
            font-family: var(--hc-serif);
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--hc-tinta);
            margin: 0 0 .25rem;
            letter-spacing: -.012em;
        }
        .encabezado p {
            color: var(--hc-tinta-2);
            font-size: .86rem;
            margin: 0;
        }

        .cuerpo-tarjeta {
            padding: 1.6rem 1.75rem 1.5rem;
        }
        @media (max-width: 420px) {
            .acceso { padding: 1rem .75rem; }
            .encabezado, .cuerpo-tarjeta { padding-left: 1.25rem; padding-right: 1.25rem; }
        }

        .error-acceso {
            display: flex;
            align-items: flex-start;
            gap: .55rem;
            padding: .65rem .85rem;
            margin-bottom: 1.15rem;
            border-radius: 6px;
            background: var(--hc-alerta-claro);
            border: 1px solid #f0c5c1;
            border-left: 4px solid var(--hc-alerta);
            color: var(--hc-alerta-oscuro);
            font-size: .85rem;
            font-weight: 500;
        }
        .error-acceso i { color: var(--hc-alerta); flex-shrink: 0; margin-top: .1rem; }

        .grupo + .grupo { margin-top: 1.05rem; }
        .form-label {
            font-weight: 600;
            font-size: .84rem;
            color: var(--hc-tinta);
            margin-bottom: .35rem;
        }

        .campo { position: relative; }
        .campo > .icono {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--hc-tinta-3);
            pointer-events: none;
            transition: color .15s;
        }
        .campo .form-control {
            height: 44px;
            padding: .55rem .85rem .55rem 2.45rem;
            background-color: var(--hc-papel);
            border: 1px solid #9eb098;
            border-radius: 6px;
            color: var(--hc-tinta);
            font-size: .92rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .campo.con-boton .form-control { padding-right: 2.9rem; }
        .campo .form-control:hover { border-color: #7c9275; }
        .campo .form-control:focus {
            background-color: var(--hc-papel);
            border-color: var(--hc-hoja);
            box-shadow: 0 0 0 3px rgba(27, 102, 54, .15);
        }
        .campo:focus-within > .icono { color: var(--hc-hoja); }

        .campo .form-control.is-invalid {
            border-color: var(--hc-alerta);
            background-image: none;
            padding-right: .85rem;
        }
        .campo.con-boton .form-control.is-invalid { padding-right: 2.9rem; }
        .campo .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(179, 38, 30, .14); }
        .campo .form-control.is-invalid ~ .icono,
        .campo:has(.is-invalid) > .icono { color: var(--hc-alerta); }
        .mensaje-campo { font-size: .79rem; color: var(--hc-alerta); margin-top: .32rem; font-weight: 500; }

        .ver-clave {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 5px;
            background: transparent;
            color: var(--hc-tinta-2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
        }
        .ver-clave:hover { background: var(--hc-lienzo); color: var(--hc-tinta); }
        .ver-clave:focus-visible { outline: 2px solid var(--hc-hoja); outline-offset: 0; }

        .aviso-mayus { font-size: .79rem; color: var(--hc-cosecha); margin-top: .32rem; font-weight: 600; }

        .btn-entrar {
            height: 44px;
            margin-top: 1.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: var(--hc-hoja);
            border: 1px solid var(--hc-hoja-oscuro);
            color: #fff;
            font-weight: 600;
            font-size: .92rem;
            border-radius: 6px;
            transition: background .15s, border-color .15s;
        }
        .btn-entrar:hover { background: var(--hc-hoja-oscuro); border-color: var(--hc-hoja-oscuro); color: #fff; }
        .btn-entrar:focus-visible { background: var(--hc-hoja-oscuro); color: #fff; outline: 2px solid var(--hc-hoja); outline-offset: 2px; }
        .btn-entrar:disabled,
        .btn-entrar.cargando {
            background: var(--hc-hoja-oscuro);
            border-color: var(--hc-hoja-oscuro);
            color: #fff;
            opacity: 1;
            cursor: progress;
        }

        .nota {
            border-top: 1px solid var(--hc-borde-fino);
            background: var(--hc-lienzo);
            padding: .85rem 1.5rem;
            font-size: .78rem;
            color: var(--hc-tinta-2);
            text-align: center;
            margin: 0;
            line-height: 1.45;
        }

        .version {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .74rem;
            color: var(--hc-tinta-2);
        }
        .version code {
            font-family: var(--hc-sans);
            font-weight: 600;
            font-size: .72rem;
            color: var(--hc-tinta);
            background: var(--hc-papel);
            border: 1px solid var(--hc-borde);
            border-radius: 4px;
            padding: .1rem .45rem;
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

        <div class="cuerpo-tarjeta">
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
        </div>

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

        ver.addEventListener('click', () => {
            const mostrar = clave.type === 'password';
            clave.type = mostrar ? 'text' : 'password';
            icono.className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
            ver.setAttribute('aria-pressed', mostrar);
            ver.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
            clave.focus();
        });

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
