<?= $this->extend('layout/template') ?>

<?= $this->section('estilos') ?>
<style>
    /* Guía de cultivo */
    .guia {
        position: sticky;
        top: 100px;
    }
    .guia-vacia {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: #6c757d;
    }
    .guia-vacia i {
        font-size: 2.5rem;
        color: var(--huerto-verde);
    }
    .guia-dato {
        background: var(--huerto-crema);
        border-radius: .8rem;
        padding: .6rem .8rem;
        height: 100%;
    }
    .guia-dato .etiqueta {
        font-size: .72rem;
        color: #6c757d;
        font-weight: 500;
    }
    .guia-dato .valor {
        font-weight: 700;
        color: var(--huerto-verde-oscuro);
        font-size: .9rem;
    }
    .guia h6 {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
        font-weight: 700;
        margin: 1.25rem 0 .6rem;
    }
    .analisis li {
        display: flex;
        gap: .6rem;
        font-size: .875rem;
        margin-bottom: .6rem;
    }
    .analisis li i {
        flex-shrink: 0;
        margin-top: .1rem;
    }
    .campo-sugerido {
        animation: resaltar 1.2s ease-out;
    }
    @keyframes resaltar {
        from { box-shadow: 0 0 0 .3rem rgba(167, 227, 122, .9); }
        to   { box-shadow: 0 0 0 0 rgba(167, 227, 122, 0); }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<a href="<?= base_url('/') ?>" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center gap-1 mb-3">
    <i class="bi bi-arrow-left"></i> Volver al panel
</a>

<div class="row g-4 align-items-start">
    <div class="col-lg-7">
        <div class="panel-form">
            <div class="huerto-hero rounded-0 py-4 px-4 px-md-5">
                <h4 class="fw-bold mb-1"><i class="bi bi-flower1 me-2"></i>Agregar Nuevo Cultivo</h4>
                <p class="mb-0 opacity-75 small">Completá los datos para empezar a seguir su riego y cosecha.</p>
            </div>
            <div class="p-4 p-md-5">
                <?php if ($plan): ?>
                    <div class="alert alert-success d-flex gap-2 align-items-start mb-4">
                        <i class="bi bi-calendar2-check mt-1"></i>
                        <div>Estás sembrando lo que planificaste en el plano 3D<?= $plan['plantas'] ? ' (' . (int) $plan['plantas'] . ' plantas)' : '' ?>. Revisá los datos y guardalo.<?= $plan['notas'] ? '<div class="small mt-1">' . esc($plan['notas']) . '</div>' : '' ?></div>
                    </div>
                <?php endif; ?>
                <form id="formCultivo" action="<?= base_url('huerto/crear') ?>" method="POST">
                    <?php if ($plan): ?>
                        <input type="hidden" name="plan_id" value="<?= (int) $plan['id'] ?>">
                    <?php endif; ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-tag me-1"></i> Nombre de la Planta</label>
                            <input type="text" name="nombre_planta" class="form-control" required placeholder="Ej. Tomate" list="listaEspecies" autocomplete="off" value="<?= esc($plan['especie'] ?? '') ?>">
                            <datalist id="listaEspecies">
                                <?php foreach ($especies as $especie): ?>
                                    <option value="<?= esc($especie['nombre']) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-bookmark me-1"></i> Variedad (Opcional)</label>
                            <input type="text" name="variedad" class="form-control" placeholder="Ej. Perita" value="<?= esc($plan['variedad'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-event me-1"></i> Fecha de Siembra</label>
                            <input type="date" name="fecha_siembra" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            <div class="form-text">Si viene de almácigo, poné la fecha de trasplante.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-basket2 me-1"></i> Días para Cosecha</label>
                            <input type="number" name="dias_cosecha_estimados" class="form-control" min="1" required value="<?= esc($plan['dias_cosecha'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-droplet me-1"></i> Frecuencia Riego (Días)</label>
                            <input type="number" name="frecuencia_riego_dias" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-clock me-1"></i> Horario de Riego</label>
                            <select name="horario_riego" class="form-control" required>
                                <?php foreach ($horarios as $clave => $horario): ?>
                                    <option value="<?= $clave ?>"><?= $horario['nombre'] ?> (<?= implode(' y ', $horario['franjas']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-cup-straw me-1"></i> Cantidad por Riego (Litros)</label>
                            <input type="number" name="cantidad_riego_litros" class="form-control" step="0.1" min="0.1" required placeholder="Ej. 1.5">
                            <div class="form-text">Lo que necesita la planta en primavera u otoño. El sistema lo ajusta según el mes y tu método de riego.</div>
                        </div>
                    </div>
                    <?php if ($canteros): ?>
                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-bounding-box me-1"></i> Cantero (Opcional)</label>
                            <select name="cantero_id" class="form-control">
                                <option value="">Sin ubicar en el plano</option>
                                <?php foreach ($canteros as $id => $nombre): ?>
                                    <option value="<?= $id ?>" <?= (int) ($plan['cantero_id'] ?? $_GET['cantero'] ?? 0) === $id ? 'selected' : '' ?>><?= esc($nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Así aparece en el <a href="<?= base_url('huerto/plano') ?>">huerto 3D</a> y entra en la planificación de ese cantero.</div>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('/') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-huerto"><i class="bi bi-check-lg me-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <aside class="panel-form guia" aria-live="polite">
            <div id="guiaContenido" class="p-4">
                <div class="guia-vacia">
                    <i class="bi bi-book"></i>
                    <p class="fw-bold text-dark mt-3 mb-1">Guía de cultivo</p>
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
<script>
    // Datos enviados por el controlador (el catálogo viene de la tabla 'especies')
    const ESPECIES = <?= json_encode($especies, $json) ?>;
    const ACTIVOS  = <?= json_encode($activos, $json) ?>;
    const HORARIOS = <?= json_encode($horarios, $json) ?>;
    const ZONA     = <?= json_encode($zona, $json) ?>;
    const RIEGO    = <?= json_encode($riego, $json) ?>;
    const URL_CONFIGURACION = <?= json_encode(base_url('huerto/configuracion'), $json) ?>;
    const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    const form  = document.getElementById('formCultivo');
    const guia  = document.getElementById('guiaContenido');
    const guiaInicial = guia.innerHTML;
    const campo = nombre => form.elements[nombre];

    // ---------- Identificación de la especie ----------

    // "Tomátes  Cherry" -> "tomates cherry"
    function normalizar(texto) {
        return String(texto).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, ' ').trim();
    }

    // Índice: nombre o sinónimo normalizado -> especie
    const indice = {};
    ESPECIES.forEach(especie => {
        [especie.nombre, ...especie.sinonimos].forEach(n => indice[normalizar(n)] = especie);
    });

    function buscar(texto) {
        // Probamos el texto tal cual y en singular ("tomates" -> "tomate", "morrones" -> "morron")
        const singularS  = texto.split(' ').map(p => p.replace(/s$/, '')).join(' ');
        const singularEs = texto.split(' ').map(p => p.replace(/es$/, '')).join(' ');
        return indice[texto] || indice[singularS] || indice[singularEs] || null;
    }

    function identificar(texto) {
        const palabras = normalizar(texto).split(' ').filter(Boolean);
        // Si no encontramos "tomate cherry grande", probamos "tomate cherry" y después "tomate"
        for (let n = palabras.length; n > 0; n--) {
            const especie = buscar(palabras.slice(0, n).join(' '));
            if (especie) return especie;
        }
        return null;
    }

    // Cultivos que ya están en el huerto, con su especie reconocida
    const activos = ACTIVOS.map(c => ({ ...c, especie: identificar(c.nombre_planta) }));

    // ---------- Utilidades ----------

    function esc(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    function numero(valor, decimales = 1) {
        return Number(valor).toLocaleString('es-AR', { maximumFractionDigits: decimales });
    }

    function listaNombres(nombres) {
        if (nombres.length <= 1) return nombres.join('');
        return nombres.slice(0, -1).join(', ') + ' y ' + nombres[nombres.length - 1];
    }

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

    function diasEntre(desde, hasta) {
        return Math.round((hasta - desde) / 86400000);
    }

    function fechaLocal(iso) {
        const [a, m, d] = iso.split('-').map(Number);
        return new Date(a, m - 1, d);
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

    // En las zonas frías, las especies sensibles a la helada se siembran más tarde
    function mesesEnZona(meses, especie) {
        if (!ZONA || !especie.sensible_helada || !Number(ZONA.desfase_meses)) return meses;
        return meses.map(m => (m - 1 + Number(ZONA.desfase_meses)) % 12 + 1);
    }

    // "09-15" -> "15 de septiembre"
    function diaMes(md) {
        const [m, d] = md.split('-').map(Number);
        return d + ' de ' + MESES[m - 1];
    }

    function aMesDia(fecha) {
        return String(fecha.getMonth() + 1).padStart(2, '0') + '-' + String(fecha.getDate()).padStart(2, '0');
    }

    // El período de heladas va de la primera helada (otoño) a la última (primavera)
    function enPeriodoDeHeladas(fecha) {
        const md = aMesDia(fecha);
        return md >= ZONA.primera_helada && md < ZONA.ultima_helada;
    }

    function proximaPrimeraHelada(desde) {
        const [m, d] = ZONA.primera_helada.split('-').map(Number);
        const helada = new Date(desde.getFullYear(), m - 1, d);
        if (helada <= desde) helada.setFullYear(helada.getFullYear() + 1);
        return helada;
    }

    // ---------- Análisis personalizado ----------

    const ICONOS = {
        ok:     'bi-check-circle-fill text-success',
        aviso:  'bi-exclamation-triangle-fill text-warning',
        alerta: 'bi-x-octagon-fill text-danger',
        info:   'bi-info-circle-fill text-primary',
    };

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

    function dato(icono, etiqueta, valor) {
        return `<div class="col-6"><div class="guia-dato">
                    <div class="etiqueta"><i class="bi ${icono} me-1"></i>${etiqueta}</div>
                    <div class="valor">${valor}</div>
                </div></div>`;
    }

    function mostrarGuia(especie, textoEscrito) {
        const horario = HORARIOS[especie.horario] ? HORARIOS[especie.horario].nombre : especie.horario;
        const reconocido = normalizar(textoEscrito) !== normalizar(especie.nombre)
            ? `<p class="small text-muted mb-0">Reconocido a partir de «${esc(textoEscrito)}»</p>` : '';
        const capitalizar = texto => texto.replace(/^./, l => l.toUpperCase());
        const almacigo = mesesEnZona(especie.meses_almacigo, especie);
        const zona = ZONA
            ? `<i class="bi bi-geo-alt me-1"></i>Calendario para <strong>${esc(ZONA.nombre)}</strong> · <a href="${URL_CONFIGURACION}" class="text-success">cambiar</a>`
            : `<i class="bi bi-geo-alt me-1"></i>Fechas de referencia. <a href="${URL_CONFIGURACION}" class="text-success">Elegí la zona de tu huerto</a> para ajustarlas.`;

        guia.innerHTML = `
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <span class="badge rounded-pill text-bg-light border mb-2">${esc(especie.familia)}</span>
                    <h5 class="fw-bold mb-0" style="color: var(--huerto-verde-oscuro)"><i class="bi bi-book me-2"></i>Guía: ${esc(especie.nombre)}</h5>
                    ${reconocido}
                </div>
            </div>
            <div class="small text-muted mt-2">${zona}</div>

            <h6>Cómo cultivarlo</h6>
            <div class="row g-2">
                ${almacigo.length ? dato('bi-house-door', 'Almácigo', capitalizar(rangoMeses(almacigo))) : ''}
                ${dato('bi-calendar3', almacigo.length ? 'Trasplante' : 'Siembra', capitalizar(rangoMeses(mesesEnZona(especie.meses, especie))))}
                ${dato('bi-tree', 'Tipo de siembra', esc(especie.tipo_siembra))}
                ${dato('bi-sun', 'Exposición', esc(especie.exposicion))}
                ${dato('bi-arrows', 'Distancia / profundidad', `${especie.distancia_cm} cm / ${especie.profundidad_cm > 0 ? numero(especie.profundidad_cm) + ' cm' : 'a ras'}`)}
                ${dato('bi-snow', 'Heladas', especie.sensible_helada ? 'No las soporta' : 'Las tolera')}
                ${dato('bi-moisture', 'Tolerancia a sales', esc(especie.tolerancia_sal))}
            </div>

            <h6>Valores recomendados</h6>
            <div class="row g-2 mb-3">
                ${dato('bi-basket2', 'Días a cosecha', especie.dias_cosecha)}
                ${dato('bi-droplet', 'Riego', 'Cada ' + especie.frecuencia + ' día(s)')}
                ${dato('bi-clock', 'Horario', esc(horario))}
                ${dato('bi-cup-straw', 'Por riego', numero(especie.litros) + ' L')}
            </div>
            <button type="button" id="usarRecomendados" class="btn btn-huerto btn-sm w-100">
                <i class="bi bi-magic me-1"></i> Usar valores recomendados
            </button>

            <h6>Análisis para tu huerto</h6>
            <ul class="list-unstyled analisis mb-0" id="analisis"></ul>

            ${especie.consejos.length ? `
                <h6>Consejos</h6>
                <ul class="small ps-3 mb-0">${especie.consejos.map(c => `<li class="mb-1">${esc(c)}</li>`).join('')}</ul>` : ''}
        `;

        document.getElementById('usarRecomendados').addEventListener('click', () => aplicarRecomendados(especie));
        actualizarAnalisis(especie);
    }

    function actualizarAnalisis(especie) {
        const lista = document.getElementById('analisis');
        if (!lista) return;
        lista.innerHTML = analizar(especie)
            .map(item => `<li><i class="bi ${ICONOS[item.tipo]}"></i><span>${item.html}</span></li>`)
            .join('');
    }

    function mostrarNoEncontrado(texto) {
        guia.innerHTML = `
            <div class="guia-vacia">
                <i class="bi bi-search"></i>
                <p class="fw-bold text-dark mt-3 mb-1">No tenemos guía para «${esc(texto)}»</p>
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

    // ---------- Eventos ----------

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

    // Viene precargado desde una siembra planificada: completamos el resto con lo recomendado
    if (campo('nombre_planta').value) {
        const diasPlan = campo('dias_cosecha_estimados').value;
        alEscribirNombre();
        if (especieActual) {
            aplicarRecomendados(especieActual);
            if (diasPlan) campo('dias_cosecha_estimados').value = diasPlan;
            actualizarAnalisis(especieActual);
        }
    }

    // Cualquier cambio en el resto del formulario recalcula el análisis
    ['fecha_siembra', 'dias_cosecha_estimados', 'frecuencia_riego_dias', 'horario_riego', 'cantidad_riego_litros'].forEach(nombre => {
        campo(nombre).addEventListener('input', () => especieActual && actualizarAnalisis(especieActual));
    });
</script>
<?= $this->endSection() ?>
