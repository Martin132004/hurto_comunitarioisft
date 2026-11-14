<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    /* Ficha técnica de especie (columna derecha) */
    .guia {
        position: sticky;
        top: 76px;
        border-top-color: var(--hc-acequia);
    }
    .guia-cabecera-ficha {
        background: var(--hc-salvia);
        border-bottom: 1px solid var(--hc-borde);
        padding: 1rem 1.25rem;
    }
    .guia-vacia {
        text-align: center;
        padding: 2.25rem 1.25rem;
        color: var(--hc-tinta-2);
    }
    .guia-vacia .icono-ficha {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        background: var(--hc-salvia);
        border: 1px solid var(--hc-borde);
        color: var(--hc-hoja);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    .guia-subtitulo {
        font-family: var(--hc-serif);
        font-size: .95rem;
        font-weight: 700;
        color: var(--hc-tinta);
        padding-bottom: .3rem;
        border-bottom: 1px solid var(--hc-borde);
        margin: 1.15rem 0 .65rem;
    }
    .guia-grilla {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border: 1px solid var(--hc-borde);
        border-radius: 6px;
        background: var(--hc-papel);
        overflow: hidden;
    }
    .guia-celda {
        padding: .5rem .7rem;
        border-bottom: 1px solid var(--hc-borde-fino);
        border-right: 1px solid var(--hc-borde-fino);
    }
    .guia-celda:nth-child(2n) { border-right: 0; }
    .guia-celda .etiqueta {
        font-size: .72rem;
        color: var(--hc-tinta-2);
        font-weight: 500;
        margin-bottom: .1rem;
    }
    .guia-celda .valor {
        font-weight: 700;
        color: var(--hc-tinta);
        font-size: .86rem;
    }
    .analisis {
        display: flex;
        flex-direction: column;
        gap: .45rem;
    }
    .analisis li {
        display: flex;
        gap: .55rem;
        font-size: .84rem;
        padding: .55rem .7rem;
        border-radius: 6px;
        border: 1px solid var(--hc-borde-fino);
        background: var(--hc-lienzo);
        line-height: 1.4;
    }
    .analisis li i {
        flex-shrink: 0;
        margin-top: .12rem;
    }
    .campo-sugerido {
        animation: resaltar .9s ease-out;
    }
    @keyframes resaltar {
        from { box-shadow: 0 0 0 3px rgba(27, 102, 54, .35); background-color: var(--hc-hoja-claro); }
        to   { box-shadow: 0 0 0 0 rgba(27, 102, 54, 0); background-color: #fff; }
    }
    .subbloque-form {
        background: var(--hc-lienzo);
        border: 1px solid var(--hc-borde-fino);
        border-radius: 6px;
        padding: 1rem 1.1rem;
        margin-bottom: 1rem;
    }
    .subbloque-titulo {
        font-family: var(--hc-serif);
        font-size: .95rem;
        font-weight: 700;
        color: var(--hc-tinta);
        margin-bottom: .75rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<a href="<?= base_url('/') ?>" class="hc-volver mb-3">
    <i class="bi bi-arrow-left"></i> Volver al panel
</a>

<div class="row g-4 align-items-start">
    <div class="col-lg-7">
        <div class="panel-form">
            <div class="huerto-hero rounded-0">
                <h1 class="mb-1"><i class="bi bi-flower1"></i>Agregar nuevo cultivo</h1>
                <p class="mb-0">Completá los datos para empezar a seguir su riego y cosecha.</p>
            </div>
            <div class="p-4">
                <?= $this->include('layout/avisos') ?>
                <form id="formCultivo" action="<?= base_url('huerto/crear') ?>" method="POST">

                    <div class="subbloque-form">
                        <div class="subbloque-titulo">
                            <span class="hc-paso-numero">1</span> Planta
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="campo_nombre_planta"><i class="bi bi-flower1 me-1"></i> Nombre de la planta</label>
                                <input type="text" id="campo_nombre_planta" name="nombre_planta" class="form-control" maxlength="100" required placeholder="Ej. Tomate" list="listaEspecies" autocomplete="off" value="<?= esc(old('nombre_planta')) ?>">
                                <datalist id="listaEspecies">
                                    <?php foreach ($especies as $especie): ?>
                                        <option value="<?= esc($especie['nombre']) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="campo_variedad"><i class="bi bi-tag me-1"></i> Variedad <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" id="campo_variedad" name="variedad" class="form-control" maxlength="100" placeholder="Ej. Perita" value="<?= esc(old('variedad')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="subbloque-form">
                        <div class="subbloque-titulo">
                            <span class="hc-paso-numero">2</span> Siembra y cosecha
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="campo_fecha_siembra"><i class="bi bi-calendar-event me-1"></i> Fecha de siembra</label>
                                <input type="date" id="campo_fecha_siembra" aria-describedby="ayuda_fecha_siembra" name="fecha_siembra" class="form-control" value="<?= esc(old('fecha_siembra', date('Y-m-d'))) ?>" required>
                                <div class="form-text" id="ayuda_fecha_siembra">Si viene de almácigo, poné la fecha de trasplante.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="campo_dias_cosecha_estimados"><i class="bi bi-basket2 me-1"></i> Días para cosecha</label>
                                <div class="input-group">
                                    <input type="number" id="campo_dias_cosecha_estimados" name="dias_cosecha_estimados" class="form-control" min="1" max="400" required placeholder="Ej. 80" value="<?= esc(old('dias_cosecha_estimados')) ?>">
                                    <span class="input-group-text">días</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="subbloque-form mb-4">
                        <div class="subbloque-titulo">
                            <span class="hc-paso-numero">3</span> Riego
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="campo_frecuencia_riego_dias"><i class="bi bi-arrow-repeat me-1"></i> Frecuencia de riego</label>
                                <div class="input-group">
                                    <span class="input-group-text">cada</span>
                                    <input type="number" id="campo_frecuencia_riego_dias" name="frecuencia_riego_dias" class="form-control" min="1" max="60" required placeholder="2" value="<?= esc(old('frecuencia_riego_dias', '')) ?>">
                                    <span class="input-group-text">días</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="campo_horario_riego"><i class="bi bi-clock me-1"></i> Horario de riego</label>
                                <select id="campo_horario_riego" name="horario_riego" class="form-control" required>
                                    <?php foreach ($horarios as $clave => $horario): ?>
                                        <option value="<?= $clave ?>" <?= old('horario_riego') === $clave ? 'selected' : '' ?>><?= $horario['nombre'] ?> (<?= implode(' y ', $horario['franjas']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="campo_cantidad_riego_litros"><i class="bi bi-droplet me-1"></i> Cantidad por riego</label>
                                <div class="input-group">
                                    <input type="number" id="campo_cantidad_riego_litros" aria-describedby="ayuda_cantidad_riego" name="cantidad_riego_litros" class="form-control" step="0.1" min="0.1" required placeholder="Ej. 1.5" value="<?= esc(old('cantidad_riego_litros', '')) ?>">
                                    <span class="input-group-text">L</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-text mt-0" id="ayuda_cantidad_riego">
                                    Lo que necesita la planta en primavera u otoño. El sistema lo ajusta según el mes y tu método de riego (<?= esc(mb_strtolower($riego['metodo'])) ?>).
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= base_url('/') ?>" class="btn btn-light px-3">Cancelar</a>
                        <button type="submit" class="btn btn-huerto"><i class="bi bi-check2 me-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <aside class="panel-form guia" aria-label="Guía de cultivo">
            <div class="guia-cabecera-ficha d-flex align-items-center justify-content-between">
                <span class="fw-bold small"><i class="bi bi-book me-1 text-success"></i> Guía de cultivo</span>
                <?php if ($zona): ?>
                    <span class="badge text-bg-light"><?= esc($zona['nombre']) ?></span>
                <?php endif; ?>
            </div>
            <div id="guiaContenido" class="p-4">
                <div class="guia-vacia">
                    <span class="icono-ficha"><i class="bi bi-journal-richtext"></i></span>
                    <p class="fw-bold text-dark mt-3 mb-1" style="font-family: var(--hc-serif); font-size: 1.05rem;">Guía de cultivo</p>
                    <?php if (empty($especies)): ?>
                        <p class="small mb-0">El catálogo de especies todavía no está cargado.</p>
                    <?php else: ?>
                        <p class="small mb-0">Escribí el nombre de la planta y te mostramos cómo cultivarla y qué conviene según tu huerto.</p>
                    <?php endif; ?>
                </div>
            </div>
        </aside>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php $json = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE; ?>
<script type="module">
    import {
        MESES, esc, num as numero, normalizar, listaNombres, fechaLocal, diasEntre, crearIdentificador, calendarioDeZona,
    } from '<?= base_url('js/huerta.js') ?>?v=<?= filemtime(FCPATH . 'js/huerta.js') ?>';

    // Datos enviados por el controlador (el catálogo viene de la tabla 'especies')
    const ESPECIES = <?= json_encode($especies, $json) ?>;
    const ACTIVOS  = <?= json_encode($activos, $json) ?>;
    const HORARIOS = <?= json_encode($horarios, $json) ?>;
    const ZONA     = <?= json_encode($zona, $json) ?>;
    const RIEGO    = <?= json_encode($riego, $json) ?>;
    const URL_CONFIGURACION = <?= json_encode(base_url('huerto/configuracion'), $json) ?>;

    const form  = document.getElementById('formCultivo');
    const guia  = document.getElementById('guiaContenido');
    const guiaInicial = guia.innerHTML;
    const campo = nombre => form.elements[nombre];

    // ---------- Identificación de la especie ----------

    const identificar = crearIdentificador(ESPECIES);

    // Cultivos que ya están en el huerto, con su especie reconocida
    const activos = ACTIVOS.map(c => ({ ...c, especie: identificar(c.nombre_planta) }));

    // ---------- Utilidades ----------

    // [2,3,4,8,9] -> "febrero a abril y agosto a septiembre"
    function rangoMeses(meses) {
        const ordenados = [...meses].sort((a, b) => a - b);
        const rangos = [];
        ordenados.forEach(m => {
            const ultimo = rangos[rangos.length - 1];
            if (ultimo && m === ultimo[1] + 1) ultimo[1] = m;
            else rangos.push([m, m]);
        });
        // Un rango que termina en diciembre continúa en el que empieza en enero: "noviembre a enero"
        if (rangos.length > 1 && rangos[0][0] === 1 && rangos[rangos.length - 1][1] === 12) {
            rangos[rangos.length - 1][1] = rangos.shift()[1];
        }
        return listaNombres(rangos.map(([desde, hasta]) =>
            desde === hasta ? MESES[desde - 1] : MESES[desde - 1] + ' a ' + MESES[hasta - 1]));
    }

    // Litros a aplicar este mes según la zona y el método de riego del huerto
    function litrosAplicar(litrosPlanta) {
        return Math.round(litrosPlanta * RIEGO.coeficiente / RIEGO.eficiencia * 10) / 10;
    }

    function litrosSemanales(litros, frecuencia) {
        return frecuencia > 0 ? litrosAplicar(litros) * 7 / frecuencia : 0;
    }

    function fechaLarga(fecha) {
        return fecha.toLocaleDateString('es-AR', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    // ---------- Calendario según la zona del huerto ----------

    const { mesesEnZona, enPeriodoDeHeladas, proximaPrimeraHelada } = calendarioDeZona(ZONA);

    // "09-15" -> "15 de septiembre"
    function diaMes(md) {
        const [m, d] = md.split('-').map(Number);
        return d + ' de ' + MESES[m - 1];
    }

    // ---------- Análisis personalizado ----------

    const ICONOS = {
        ok:     'bi-check-circle-fill text-success',
        aviso:  'bi-exclamation-triangle-fill text-warning',
        alerta: 'bi-x-octagon-fill text-danger',
        info:   'bi-info-circle-fill text-primary',
    };
    const TIPOS = { ok: 'Bien', aviso: 'Atención', alerta: 'Problema', info: 'Dato' };

    function analizar(especie) {
        const resultado = [];
        const agregar = (tipo, html) => resultado.push({ tipo, html });

        const fechaSiembra = campo('fecha_siembra').value ? fechaLocal(campo('fecha_siembra').value) : null;
        const dias       = Number(campo('dias_cosecha_estimados').value) || especie.dias_cosecha;
        const frecuencia = Number(campo('frecuencia_riego_dias').value) || especie.frecuencia;
        const litros     = Number(campo('cantidad_riego_litros').value) || especie.litros;

        // 1. Época de siembra según el mes elegido y la zona del huerto
        if (fechaSiembra) {
            const mes = fechaSiembra.getMonth() + 1;
            const meses = mesesEnZona(especie.meses, especie);
            const enZona = ZONA ? ` en ${esc(ZONA.nombre)}` : '';
            if (meses.includes(mes)) {
                agregar('ok', `<strong>Buena época:</strong> ${MESES[mes - 1]} está dentro de la temporada de siembra${enZona} (${rangoMeses(meses)}).`);
            } else {
                let faltan = 1;
                while (!meses.includes((mes - 1 + faltan) % 12 + 1)) faltan++;
                const proximo = MESES[(mes - 1 + faltan) % 12];
                agregar('alerta', `<strong>Fuera de temporada:</strong> en ${MESES[mes - 1]} no se recomienda sembrar ${esc(especie.nombre.toLowerCase())}${enZona}. La próxima ventana empieza en ${proximo} (${faltan === 1 ? 'el mes que viene' : 'en ' + faltan + ' meses'}).`);
            }

            // 2. Fecha estimada de cosecha
            const cosecha = new Date(fechaSiembra);
            cosecha.setDate(cosecha.getDate() + dias);
            agregar('info', `<strong>Cosecha estimada:</strong> ${fechaLarga(cosecha)} (en ${dias} días).`);

            // 3. Heladas de la zona (solo para las especies que no las soportan)
            if (ZONA && especie.sensible_helada) {
                if (enPeriodoDeHeladas(fechaSiembra)) {
                    agregar('alerta', `<strong>Riesgo de helada:</strong> en ${esc(ZONA.nombre)} la última helada suele ser cerca del ${diaMes(ZONA.ultima_helada)}. Si lo plantás antes, cubrilo de noche con nylon o media sombra.`);
                }
                const helada = proximaPrimeraHelada(fechaSiembra);
                if (cosecha > helada) {
                    agregar('aviso', `La cosecha estimada cae después de la primera helada de la zona (cerca del ${diaMes(ZONA.primera_helada)}). Sembrá antes o elegí una variedad de ciclo más corto.`);
                } else if (!enPeriodoDeHeladas(fechaSiembra)) {
                    agregar('ok', `Llega a cosecharse antes de la primera helada de la zona (cerca del ${diaMes(ZONA.primera_helada)}).`);
                }
            }
        }

        // 4. Tolerancia a la salinidad del suelo y del agua
        if (especie.tolerancia_sal === 'Baja') {
            agregar('aviso', `<strong>Sensible a las sales:</strong> plantalo donde no haya costra blanca en el suelo y regá en profundidad de vez en cuando para lavar las sales.`);
        } else if (especie.tolerancia_sal === 'Alta') {
            agregar('ok', `Tolera bien los suelos y el agua con sales: buena opción para los sectores más salitrosos del huerto.`);
        }

        // 5. Valores cargados muy distintos a los recomendados
        const cargado = nombre => Number(campo(nombre).value);
        const desvio = (valor, recomendado) => valor > 0 && (valor >= recomendado * 1.5 || valor <= recomendado * 0.5);

        if (desvio(cargado('frecuencia_riego_dias'), especie.frecuencia)) {
            agregar('aviso', cargado('frecuencia_riego_dias') > especie.frecuencia
                ? `Regar cada ${cargado('frecuencia_riego_dias')} días es poco para esta especie (se recomienda cada ${especie.frecuencia}): puede faltarle agua.`
                : `Regar cada ${cargado('frecuencia_riego_dias')} día(s) es más seguido de lo recomendado (cada ${especie.frecuencia}): riesgo de exceso de agua y hongos.`);
        }
        if (desvio(cargado('cantidad_riego_litros'), especie.litros)) {
            agregar('aviso', cargado('cantidad_riego_litros') > especie.litros
                ? `${numero(cargado('cantidad_riego_litros'))} L por riego es mucho (se recomiendan ${numero(especie.litros)} L): el suelo puede encharcarse.`
                : `${numero(cargado('cantidad_riego_litros'))} L por riego puede quedar corto (se recomiendan ${numero(especie.litros)} L).`);
        }
        if (desvio(cargado('dias_cosecha_estimados'), especie.dias_cosecha)) {
            agregar('aviso', `Los ${cargado('dias_cosecha_estimados')} días hasta la cosecha difieren bastante del promedio para esta especie (${especie.dias_cosecha} días). Revisalo si no es una variedad especial.`);
        }
        if (campo('horario_riego').value !== especie.horario && HORARIOS[especie.horario]) {
            agregar('info', `El horario recomendado es <strong>${HORARIOS[especie.horario].nombre.toLowerCase()}</strong> (${HORARIOS[especie.horario].franjas.join(' y ')}).`);
        }

        // 6. Relación con los cultivos que ya están en el huerto
        const enHuerto = activos.filter(c => c.especie);
        const nombresUnicos = lista => [...new Set(lista.map(c => c.especie.nombre))];

        const malos = enHuerto.filter(c => especie.malos_vecinos.includes(c.especie.nombre) || c.especie.malos_vecinos.includes(especie.nombre));
        if (malos.length) {
            agregar('alerta', `<strong>Mal vecino:</strong> en tu huerto hay ${esc(listaNombres(nombresUnicos(malos)).toLowerCase())}. No los plantes cerca: compiten o se perjudican.`);
        }

        const buenos = enHuerto.filter(c => especie.buenos_vecinos.includes(c.especie.nombre) && !malos.includes(c));
        if (buenos.length) {
            agregar('ok', `<strong>Buen vecino:</strong> combina bien con ${esc(listaNombres(nombresUnicos(buenos)).toLowerCase())}, que ya tenés. Plantalo cerca.`);
        }

        const sugeridos = especie.buenos_vecinos.filter(n => !enHuerto.some(c => c.especie.nombre === n));
        if (sugeridos.length) {
            agregar('info', `Podrías acompañarlo con ${esc(listaNombres(sugeridos.slice(0, 3)).toLowerCase())}.`);
        }

        const mismaFamilia = enHuerto.filter(c => c.especie.familia === especie.familia && c.especie.nombre !== especie.nombre);
        if (mismaFamilia.length) {
            agregar('aviso', `Ya tenés ${esc(listaNombres(nombresUnicos(mismaFamilia)).toLowerCase())}, de la misma familia (${esc(especie.familia)}). Comparten plagas y enfermedades: separalos y no repitas la familia en ese lugar la próxima temporada.`);
        }

        const mismaEspecie = enHuerto.filter(c => c.especie.nombre === especie.nombre);
        if (mismaEspecie.length) {
            const ultima = mismaEspecie.map(c => fechaLocal(c.fecha_siembra)).sort((a, b) => b - a)[0];
            const hace = fechaSiembra ? diasEntre(ultima, fechaSiembra) : null;
            if (hace !== null && Math.abs(hace) < 15) {
                agregar('info', `Ya sembraste ${esc(especie.nombre.toLowerCase())} hace ${Math.abs(hace)} día(s). Si espaciás las siembras unas 2 o 3 semanas vas a cosechar de forma escalonada en lugar de todo junto.`);
            } else {
                agregar('info', `Ya tenés ${mismaEspecie.length} cultivo(s) de ${esc(especie.nombre.toLowerCase())} en curso.`);
            }
        }

        // 7. Impacto en el consumo de agua del huerto
        agregar('info', `<strong>Este mes</strong>, regando con ${esc(RIEGO.metodo.toLowerCase())}, aplicá unos <strong>${numero(litrosAplicar(litros))} L por riego</strong> (la planta necesita ${numero(litros)} L en primavera u otoño).`);

        const aguaActual = ACTIVOS.reduce((total, c) => total + litrosSemanales(Number(c.cantidad_riego_litros), Number(c.frecuencia_riego_dias)), 0);
        const aguaNueva  = litrosSemanales(litros, frecuencia);
        agregar('info', ACTIVOS.length
            ? `<strong>Agua:</strong> este cultivo suma unos ${numero(aguaNueva)} L por semana. El huerto pasaría de ${numero(aguaActual)} L a ${numero(aguaActual + aguaNueva)} L semanales.`
            : `<strong>Agua:</strong> este cultivo va a necesitar unos ${numero(aguaNueva)} L por semana.`);

        // 8. Turno de agua: riegos que hay que cubrir con agua guardada
        if (RIEGO.usa_turno) {
            const intervalo = Number(RIEGO.dias_entre_turnos);
            const riegosEntre = Math.max(0, Math.ceil(intervalo / frecuencia) - 1);
            if (riegosEntre === 0) {
                agregar('ok', `<strong>Turno de agua:</strong> regándolo cada ${frecuencia} día(s) alcanza con los días de turno.`);
            } else {
                const necesita = riegosEntre * litrosAplicar(litros);
                const total = Number(RIEGO.agua_entre_turnos) + necesita;
                const texto = `<strong>Turno de agua:</strong> entre turnos pasan hasta ${intervalo} días y este cultivo se riega cada ${frecuencia}: necesita ${riegosEntre} riego(s) con agua guardada (unos ${numero(necesita)} L).`;
                if (total <= RIEGO.reservorio) {
                    agregar('ok', `${texto} Sumado al resto del huerto serían ${numero(total)} L de los ${RIEGO.reservorio} L que podés guardar: alcanza.`);
                } else {
                    agregar(RIEGO.reservorio > 0 ? 'aviso' : 'alerta', `${texto} Sumado al resto del huerto necesitarías ${numero(total)} L guardados y ${RIEGO.reservorio > 0 ? 'podés guardar ' + RIEGO.reservorio + ' L' : 'no cargaste reservorio'}.`);
                }
            }
        }

        return resultado;
    }

    // ---------- Dibujo de la guía ----------

    function celda(icono, etiqueta, valor) {
        return `<div class="guia-celda">
                    <div class="etiqueta"><i class="bi ${icono} me-1"></i>${etiqueta}</div>
                    <div class="valor">${valor}</div>
                </div>`;
    }

    function mostrarGuia(especie, textoEscrito) {
        const horario = HORARIOS[especie.horario] ? HORARIOS[especie.horario].nombre : especie.horario;
        const reconocido = normalizar(textoEscrito) !== normalizar(especie.nombre)
            ? `<div class="small text-muted">Reconocido a partir de «${esc(textoEscrito)}»</div>` : '';
        const capitalizar = texto => texto.replace(/^./, l => l.toUpperCase());
        const almacigo = mesesEnZona(especie.meses_almacigo, especie);
        const zona = ZONA
            ? `<i class="bi bi-geo-alt me-1"></i>Calendario para <strong>${esc(ZONA.nombre)}</strong> · <a href="${URL_CONFIGURACION}" class="text-success">cambiar</a>`
            : `<i class="bi bi-geo-alt me-1"></i>Fechas de referencia. <a href="${URL_CONFIGURACION}" class="text-success">Elegí la zona de tu huerto</a> para ajustarlas.`;

        guia.innerHTML = `
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <span class="badge text-bg-light mb-1">${esc(especie.familia)}</span>
                    <h4 class="mb-0" style="font-family: var(--hc-serif); font-size: 1.3rem;">Guía: ${esc(especie.nombre)}</h4>
                    ${reconocido}
                </div>
            </div>
            <div class="small text-muted mt-1">${zona}</div>

            <div class="guia-subtitulo">Cómo cultivarlo</div>
            <div class="guia-grilla">
                ${almacigo.length ? celda('bi-house-door', 'Almácigo', capitalizar(rangoMeses(almacigo))) : ''}
                ${celda('bi-calendar3', almacigo.length ? 'Trasplante' : 'Siembra', capitalizar(rangoMeses(mesesEnZona(especie.meses, especie))))}
                ${celda('bi-tree', 'Tipo de siembra', esc(especie.tipo_siembra))}
                ${celda('bi-sun', 'Exposición', esc(especie.exposicion))}
                ${celda('bi-arrows-expand-vertical', 'Distancia / profundidad', `${especie.distancia_cm} cm / ${especie.profundidad_cm > 0 ? numero(especie.profundidad_cm) + ' cm' : 'a ras'}`)}
                ${celda('bi-snow', 'Heladas', especie.sensible_helada ? 'No las soporta' : 'Las tolera')}
                ${celda('bi-moisture', 'Tolerancia a sales', esc(especie.tolerancia_sal))}
            </div>

            <div class="guia-subtitulo">Valores recomendados</div>
            <div class="guia-grilla mb-2">
                ${celda('bi-basket2', 'Días a cosecha', especie.dias_cosecha)}
                ${celda('bi-arrow-repeat', 'Riego', 'Cada ' + especie.frecuencia + ' día(s)')}
                ${celda('bi-clock', 'Horario', esc(horario))}
                ${celda('bi-droplet', 'Por riego', numero(especie.litros) + ' L')}
            </div>
            <button type="button" id="usarRecomendados" class="btn btn-outline-success btn-sm w-100 fw-semibold">
                <i class="bi bi-arrow-down-left-square me-1"></i> Usar valores recomendados
            </button>

            <div class="guia-subtitulo">Análisis para tu huerto</div>
            <ul class="list-unstyled analisis mb-0" id="analisis"></ul>

            ${especie.consejos.length ? `
                <div class="guia-subtitulo">Consejos</div>
                <ul class="small ps-3 mb-0 text-muted">${especie.consejos.map(c => `<li class="mb-1">${esc(c)}</li>`).join('')}</ul>` : ''}
        `;

        document.getElementById('usarRecomendados').addEventListener('click', () => aplicarRecomendados(especie));
        actualizarAnalisis(especie);
    }

    function actualizarAnalisis(especie) {
        const lista = document.getElementById('analisis');
        if (!lista) return;
        lista.innerHTML = analizar(especie)
            .map(item => `<li><i class="bi ${ICONOS[item.tipo]}" aria-hidden="true"></i><span><span class="visually-hidden">${TIPOS[item.tipo]}: </span>${item.html}</span></li>`)
            .join('');
    }

    function mostrarNoEncontrado(texto) {
        guia.innerHTML = `
            <div class="guia-vacia">
                <span class="icono-ficha"><i class="bi bi-search"></i></span>
                <p class="fw-bold text-dark mt-3 mb-1" style="font-family: var(--hc-serif);">No tenemos guía para «${esc(texto)}»</p>
                <p class="small mb-0">Podés cargarlo igual completando los datos a mano. Especies disponibles: ${esc(ESPECIES.map(e => e.nombre).join(', '))}.</p>
            </div>`;
    }

    function aplicarRecomendados(especie) {
        const valores = {
            dias_cosecha_estimados: especie.dias_cosecha,
            frecuencia_riego_dias:  especie.frecuencia,
            horario_riego:          especie.horario,
            cantidad_riego_litros:  especie.litros,
        };
        Object.entries(valores).forEach(([nombre, valor]) => {
            const input = campo(nombre);
            input.value = valor;
            input.classList.remove('campo-sugerido');
            void input.offsetWidth; // reinicia la animación
            input.classList.add('campo-sugerido');
        });
        actualizarAnalisis(especie);
    }

    let especieActual = null;

    function alEscribirNombre() {
        const texto = campo('nombre_planta').value.trim();
        if (!ESPECIES.length) return;

        if (!texto) {
            especieActual = null;
            guia.innerHTML = guiaInicial;
            return;
        }

        especieActual = identificar(texto);
        especieActual ? mostrarGuia(especieActual, texto) : mostrarNoEncontrado(texto);
    }

    campo('nombre_planta').addEventListener('input', alEscribirNombre);

    if (campo('nombre_planta').value) {
        const diasCargados = campo('dias_cosecha_estimados').value;
        alEscribirNombre();
        if (especieActual) {
            aplicarRecomendados(especieActual);
            if (diasCargados) campo('dias_cosecha_estimados').value = diasCargados;
            actualizarAnalisis(especieActual);
        }
    }

    ['fecha_siembra', 'dias_cosecha_estimados', 'frecuencia_riego_dias', 'horario_riego', 'cantidad_riego_litros'].forEach(nombre => {
        campo(nombre).addEventListener('input', () => especieActual && actualizarAnalisis(especieActual));
    });
</script>
<?= $this->endSection() ?>
