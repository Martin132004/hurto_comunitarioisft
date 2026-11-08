// Plano del huerto (2D): el terreno visto desde arriba con sus canteros y elementos.
// Cada cultivo se dibuja en su cantero a su distancia de plantación y el borde del cantero
// avisa si hay algo para hacer. Se dibuja con SVG, sin librerías. Los datos vienen del
// controlador Plano en #datosPlano.

import { esc, normalizar, fechaLocal, sumarDias, crearIdentificador, calendarioDeZona } from './huerta.js';

const D = JSON.parse(document.getElementById('datosPlano').textContent);
const TIPOS = D.tipos;
const RIEGO = D.riego;
const identificar = crearIdentificador(D.especies);
const { enPeriodoDeHeladas } = calendarioDeZona(D.zona);
const hoy = fechaLocal(D.hoy);

// ---------- 1. Utilidades ----------

const $ = selector => document.querySelector(selector);
const num = (valor, decimales = 1) => Number(valor).toLocaleString('es-AR', { maximumFractionDigits: decimales });
const limitar = (v, min, max) => Math.min(max, Math.max(min, v));
const r2 = v => Math.round(v * 100) / 100;
const fechaMedia = f => f.toLocaleDateString('es-AR', { day: 'numeric', month: 'short', year: 'numeric' });

const EMOJIS = {
    tomate: '🍅', frutilla: '🍓', sandia: '🍉', pimiento: '🫑', morron: '🫑', aji: '🌶️', zanahoria: '🥕', zapallito: '🥒',
    zapallo: '🎃', melon: '🍈', pepino: '🥒', berenjena: '🍆', cebolla: '🧅', ajo: '🧄', papa: '🥔', lechuga: '🥬',
    acelga: '🥬', espinaca: '🥬', repollo: '🥬', brocoli: '🥦', albahaca: '🌿', perejil: '🌿', arveja: '🫛', haba: '🫛',
    poroto: '🫘', maiz: '🌽', choclo: '🌽', rabanito: '🌱',
};
const emojiDe = nombre => EMOJIS[Object.keys(EMOJIS).find(k => normalizar(nombre).startsWith(k))] ?? '🌱';

// ---------- 2. Estado ----------

const h = D.huerto;
const CULTIVOS = D.cultivos.map(c => {
    const inicio = fechaLocal(c.fecha);
    return { ...c, inicio, fin: sumarDias(inicio, Math.max(1, c.dias_cosecha)), especie: identificar(c.nombre) };
});

const prepararElemento = e => ({
    clave: 'e' + e.id, id: Number(e.id), tipo: e.tipo, nombre: e.nombre,
    x: Number(e.x), y: Number(e.y), ancho: Number(e.ancho), largo: Number(e.largo),
    rotacion: Number(e.rotacion), media_sombra: Boolean(Number(e.media_sombra)),
});

const estado = {
    terreno: { ancho: Number(h.plano_ancho) || 12, largo: Number(h.plano_largo) || 10 },
    elementos: D.elementos.map(prepararElemento),
    // Cantero de cada cultivo: { id del cultivo: clave del elemento }
    ubicaciones: Object.fromEntries(D.cultivos.map(c => [c.id, c.cantero_id ? 'e' + c.cantero_id : null])),
    seleccion: null,
    cambios: false,
};
let contadorClaves = 0;

const porClave = clave => estado.elementos.find(e => e.clave === clave);
const plantables = () => estado.elementos.filter(e => TIPOS[e.tipo].plantable);
const REDONDOS = ['maceta', 'arbol', 'tanque'];
const area = el => REDONDOS.includes(el.tipo) ? Math.PI * (el.ancho / 2) * (el.largo / 2) : el.ancho * el.largo;

// Cultivos en curso dentro de un cantero
const enCantero = el => CULTIVOS.filter(c => !c.cosechado && estado.ubicaciones[c.id] === el.clave);

// Lo último que hubo en el cantero antes de un cultivo (para la rotación de familias)
const anteriorA = (el, c) => CULTIVOS
    .filter(x => x.id !== c.id && x.especie && estado.ubicaciones[x.id] === el.clave && x.fin <= c.inicio)
    .sort((a, b) => b.fin - a.fin)[0] ?? null;

// Cuántas plantas entran en el cantero a la distancia de la especie
const capacidad = (el, especie, compartido) => Math.max(1, Math.floor(area(el) / compartido / ((especie?.distancia_cm || 30) / 100) ** 2));

// ---------- 3. Avisos de cada cantero (definen el color del borde) ----------

const GRAVEDAD = { grave: 0, alerta: 1, info: 2 };

function avisosDe(el) {
    const lista = enCantero(el);
    const avisos = [];
    const agregar = (tipo, texto) => avisos.push({ tipo, texto });

    lista.forEach(c => {
        if (c.problemas) agregar('grave', `${c.nombre}: ${c.problemas} problema(s) sin resolver.`);
        if (c.especie?.sensible_helada && enPeriodoDeHeladas(hoy)) {
            // Un túnel sin calefacción baja el riesgo pero no lo elimina
            TIPOS[el.tipo].protege_helada
                ? agregar('info', `${c.nombre} está protegido por el túnel, pero en noches muy frías puede helar adentro.`)
                : agregar('grave', `${c.nombre} no soporta heladas y todavía puede helar: cubrilo de noche.`);
        }
        const previo = anteriorA(el, c);
        if (c.especie && previo?.especie.familia === c.especie.familia) {
            agregar('alerta', `${c.nombre} viene después de ${previo.nombre}, de la misma familia (${c.especie.familia}).`);
        }
        if (c.regar) agregar('alerta', `Hay que regar ${c.nombre}.`);
        if (c.cosechar) agregar('alerta', `${c.nombre} está para cosechar.`);
    });

    // Cultivos que no conviene que compartan el cantero
    lista.forEach((a, i) => lista.slice(i + 1).forEach(b => {
        if (a.especie && b.especie && (a.especie.malos_vecinos.includes(b.especie.nombre) || b.especie.malos_vecinos.includes(a.especie.nombre))) {
            agregar('alerta', `${a.nombre} y ${b.nombre} no son buenos vecinos.`);
        }
    }));

    return avisos.sort((a, b) => GRAVEDAD[a.tipo] - GRAVEDAD[b.tipo]);
}

// ---------- 4. Dibujo del plano ----------

const svg = $('#plano');

// Colores de cada tipo: [relleno, borde]
const ESTILO = {
    cantero: ['#5b4330', '#8a6a4a'], tablon: ['#6b5038', '#6b5038'], maceta: ['#5b4330', '#c46a3f'],
    tunel: ['#5b4330', '#9fb7c9'], arbol: ['#4f8f3a', '#3c6e2a'], cortina: ['#3f7a35', '#2f5e27'],
    pared: ['#a3a3a3', '#7d7d7d'], tanque: ['#3b82c4', '#2a5f91'], compostera: ['#7a5a2e', '#5c4322'],
    acequia: ['#5aa9e6', '#3d8fd1'], camino: ['#e8dcc0', '#d6c6a0'],
};
// Semáforo: color del borde según el peor aviso (verde si tiene cultivos y está todo bien)
const SEMAFORO = { grave: '#d64545', alerta: '#f0a020', info: '#3b82c4', ok: '#3fa34d' };

// Tamaño de letra en metros: crece con el terreno para que se lea igual
const letra = () => limitar(Math.max(estado.terreno.ancho, estado.terreno.largo) / 45, 0.2, 2.5);

// Alto que ocupa el elemento ya girado (para poner el nombre justo arriba)
function altoGirado(el) {
    const g = el.rotacion * Math.PI / 180;
    return Math.abs(Math.sin(g)) * el.ancho + Math.abs(Math.cos(g)) * el.largo;
}

// Cada cultivo ocupa una franja del cantero, con sus plantas a la distancia de la especie
function dibujarPlantas(el, lista) {
    if (!lista.length) return '';
    const lado = el.tipo === 'maceta' ? Math.min(el.ancho, el.largo) * 0.65 : null;
    const borde = el.tipo === 'cantero' ? 0.14 : el.tipo === 'tunel' ? 0.3 : 0.05;
    const ancho = lado ?? Math.max(0.1, el.ancho - borde), largo = lado ?? Math.max(0.1, el.largo - borde);
    const franja = largo / lista.length;

    return lista.map((c, i) => {
        const z0 = -largo / 2 + i * franja;
        const d = (c.especie?.distancia_cm || 30) / 100;
        const columnas = Math.max(1, Math.floor(ancho / d)), filas = Math.max(1, Math.floor(franja / d));
        // shortcut: con muchas plantas chicas (zanahoria a 5 cm) se dibuja una muestra de 80 como máximo
        const salto = Math.max(1, Math.ceil(Math.sqrt(columnas * filas / 80)));
        // Las plantas crecen a medida que avanza el ciclo
        const progreso = limitar((hoy - c.inicio) / (c.fin - c.inicio), 0, 1);
        const tam = Math.min(d * salto * 0.9, 0.45) * (0.45 + 0.55 * progreso);
        let plantas = '';
        for (let f = 0; f < filas; f += salto) {
            for (let k = 0; k < columnas; k += salto) {
                plantas += `<text x="${r2(-ancho / 2 + (k + 0.5) * (ancho / columnas))}" y="${r2(z0 + (f + 0.5) * (franja / filas))}" font-size="${r2(tam)}">${emojiDe(c.nombre)}</text>`;
            }
        }
        const separador = lista.length > 1 ? `<rect x="${r2(-ancho / 2)}" y="${r2(z0)}" width="${r2(ancho)}" height="${r2(franja)}" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="0.03"/>` : '';
        return `<g text-anchor="middle" dominant-baseline="central">${separador}${plantas}</g>`;
    }).join('');
}

function dibujarElemento(el) {
    const [relleno, bordeTipo] = ESTILO[el.tipo] ?? ['#999', '#666'];
    const a = el.ancho, l = el.largo;
    let borde = bordeTipo, grosor = 0.05, plantas = '';

    if (TIPOS[el.tipo].plantable) {
        const lista = enCantero(el);
        const peor = avisosDe(el)[0]?.tipo ?? (lista.length ? 'ok' : null);
        if (peor) { borde = SEMAFORO[peor]; grosor = 0.1; }
        plantas = dibujarPlantas(el, lista);
    }

    let forma = REDONDOS.includes(el.tipo)
        ? `<ellipse rx="${a / 2}" ry="${l / 2}" fill="${relleno}" stroke="${borde}" stroke-width="${grosor}"/>`
        : `<rect x="${-a / 2}" y="${-l / 2}" width="${a}" height="${l}" rx="${Math.min(0.08, a / 6)}" fill="${relleno}" stroke="${borde}" stroke-width="${grosor}"/>`;
    if (el.tipo === 'arbol') forma += `<circle r="${Math.min(a, l) * 0.08}" fill="#6d4c2f"/>`;
    if (el.tipo === 'tunel') forma += `<rect x="${-a / 2}" y="${-l / 2}" width="${a}" height="${l}" fill="#ffffff" opacity=".18"/>`;
    if (el.media_sombra) forma += `<rect x="${-a / 2}" y="${-l / 2}" width="${a}" height="${l}" fill="url(#rayado)"/>`;
    if (el.tipo === 'compostera') forma += `<text font-size="${r2(Math.min(a, l) * 0.6)}" text-anchor="middle" dominant-baseline="central">♻️</text>`;
    if (el.clave === estado.seleccion) {
        forma += `<rect x="${-a / 2 - 0.12}" y="${-l / 2 - 0.12}" width="${a + 0.24}" height="${l + 0.24}" fill="none" stroke="#1b5e20" stroke-width="0.06" stroke-dasharray="0.2 0.12" rx="0.1"/>`;
    }

    return `<g data-clave="${el.clave}" transform="translate(${r2(el.x)} ${r2(el.y)}) rotate(${el.rotacion})" style="cursor:grab">${forma}${plantas}</g>`;
}

// Nombres de los elementos, con íconos de lo que hay que hacer hoy. Cada nombre va arriba de su
// elemento; si se pisa con otro ya puesto, prueba abajo y después cada vez más lejos hasta encontrar lugar.
function rotulos(elementos) {
    const t = letra();
    const ocupados = []; // rectángulos [izquierda, arriba, derecha, abajo] de los nombres ya puestos
    const choca = r => ocupados.some(o => r[0] < o[2] && r[2] > o[0] && r[1] < o[3] && r[3] > o[1]);

    return elementos.map(el => {
        const lista = enCantero(el);
        const iconos = (lista.some(c => c.regar) ? '💧' : '') + (lista.some(c => c.cosechar) ? '🧺' : '') + (lista.some(c => c.problemas > 0) ? '🐛' : '');
        const texto = el.nombre + (iconos ? ' · ' + iconos : '');
        const mitadTexto = [...texto].length * t * 0.3; // ancho aproximado: media letra por carácter, a cada lado
        const arriba = el.y - altoGirado(el) / 2 - t * 0.35, abajo = el.y + altoGirado(el) / 2 + t;
        const lugares = [arriba, abajo];
        for (let k = 1; k <= 6; k++) lugares.push(arriba - k * t * 1.1, abajo + k * t * 1.1);

        const rect = y => [el.x - mitadTexto, y - t * 0.85, el.x + mitadTexto, y + t * 0.2];
        const y = lugares.map(v => Math.max(t, v)).find(v => !choca(rect(v))) ?? Math.max(t, arriba);
        ocupados.push(rect(y));

        return `<text x="${r2(el.x)}" y="${r2(y)}" font-size="${r2(t)}" text-anchor="middle" font-weight="600" fill="#22301f"
            stroke="#fff" stroke-width="${r2(t * 0.28)}" stroke-linejoin="round" paint-order="stroke">${esc(texto)}</text>`;
    }).join('');
}

// Orden de dibujo: lo que está al ras del suelo primero y lo más alto al final, para que se vea encima
const CAPAS = ['camino', 'acequia', 'tablon', 'cantero', 'maceta', 'compostera', 'tanque', 'tunel', 'pared', 'cortina', 'arbol'];

function dibujar() {
    const { ancho, largo } = estado.terreno;
    const t = letra();
    let grilla = '';
    for (let x = 1; x < ancho; x++) grilla += `<line x1="${x}" y1="0" x2="${x}" y2="${largo}"/>`;
    for (let y = 1; y < largo; y++) grilla += `<line x1="0" y1="${y}" x2="${ancho}" y2="${y}"/>`;
    const orden = [...estado.elementos].sort((a, b) => CAPAS.indexOf(a.tipo) - CAPAS.indexOf(b.tipo));

    // La unidad del SVG es el metro: el plano queda a escala
    svg.setAttribute('viewBox', `${-t} ${-t} ${ancho + t * 2} ${largo + t * 3}`);
    svg.innerHTML = `
        <defs>
            <pattern id="rayado" width="0.2" height="0.2" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
                <line x1="0" y1="0" x2="0" y2="0.2" stroke="#000" stroke-opacity=".35" stroke-width="0.05"/>
            </pattern>
        </defs>
        <rect width="${ancho}" height="${largo}" fill="#c9d9a8" stroke="#9fb27a" stroke-width="0.05"/>
        <g stroke="#fff" stroke-opacity=".55" stroke-width="0.02">${grilla}</g>
        ${orden.map(dibujarElemento).join('')}
        ${rotulos(orden)}
        <text x="0" y="${r2(largo + t * 1.2)}" font-size="${r2(t * 0.8)}" fill="#5c6656">Grilla de 1 m · terreno de ${num(ancho)} × ${num(largo)} m</text>`;
}

// ---------- 5. Elegir y mover con el mouse ----------

function seleccionar(clave) {
    estado.seleccion = clave;
    dibujar();
    renderSeleccion();
    if (clave) abrirPestana('diseno');
}

function marcarCambios() {
    estado.cambios = true;
    $('#estadoGuardado').textContent = 'Hay cambios sin guardar';
    $('#estadoGuardado').classList.add('pendiente');
    dibujar();
}

// Punto del plano (en metros) bajo el puntero
function puntoPlano(evento) {
    const p = svg.createSVGPoint();
    p.x = evento.clientX;
    p.y = evento.clientY;
    return p.matrixTransform(svg.getScreenCTM().inverse());
}

let arrastre = null;

svg.addEventListener('pointerdown', evento => {
    const clave = evento.target.closest('[data-clave]')?.dataset.clave;
    if (!clave) return seleccionar(null);
    const el = porClave(clave);
    const p = puntoPlano(evento);
    arrastre = { el, dx: el.x - p.x, dy: el.y - p.y, movido: false };
    svg.setPointerCapture(evento.pointerId);
    if (estado.seleccion !== clave) seleccionar(clave);
});

svg.addEventListener('pointermove', evento => {
    if (!arrastre) return;
    const p = puntoPlano(evento);
    // Se acomoda de a 10 cm para que sea fácil alinear
    arrastre.el.x = Math.round(limitar(p.x + arrastre.dx, 0, estado.terreno.ancho) * 10) / 10;
    arrastre.el.y = Math.round(limitar(p.y + arrastre.dy, 0, estado.terreno.largo) * 10) / 10;
    arrastre.movido = true;
    svg.classList.add('moviendo');
    dibujar();
});

const soltar = () => {
    if (arrastre?.movido) {
        marcarCambios();
        renderSeleccion();
    }
    arrastre = null;
    svg.classList.remove('moviendo');
};
svg.addEventListener('pointerup', soltar);
svg.addEventListener('pointercancel', soltar);

// ---------- 6. Pestaña Diseño: agregar y editar elementos ----------

// Busca un lugar libre en el terreno para un elemento nuevo
function lugarLibre(ancho, largo) {
    const choca = (x, y) => estado.elementos.some(e => Math.abs(e.x - x) < (e.ancho + ancho) / 2 + 0.3 && Math.abs(e.y - y) < (e.largo + largo) / 2 + 0.3);
    for (let y = largo / 2 + 0.5; y <= estado.terreno.largo - largo / 2; y += 0.5) {
        for (let x = ancho / 2 + 0.5; x <= estado.terreno.ancho - ancho / 2; x += 0.5) {
            if (!choca(x, y)) return { x, y };
        }
    }
    return { x: estado.terreno.ancho / 2, y: estado.terreno.largo / 2 };
}

$('#paleta').innerHTML = Object.entries(TIPOS).map(([tipo, t]) =>
    `<button type="button" data-agregar="${tipo}"><i class="bi ${t.icono}"></i>${esc(t.nombre)}</button>`).join('');

$('#paleta').addEventListener('click', evento => {
    const tipo = evento.target.closest('[data-agregar]')?.dataset.agregar;
    if (!tipo) return;
    const [ancho, largo] = TIPOS[tipo].medidas;
    const iguales = estado.elementos.filter(e => e.tipo === tipo).length;
    const el = {
        clave: 'n' + (++contadorClaves), id: null, tipo, nombre: `${TIPOS[tipo].nombre.split(' ')[0]} ${iguales + 1}`,
        ancho, largo, rotacion: 0, media_sombra: false, ...lugarLibre(ancho, largo),
    };
    estado.elementos.push(el);
    marcarCambios();
    seleccionar(el.clave);
    renderCultivos();
});

function renderSeleccion() {
    const el = porClave(estado.seleccion);
    if (!el) {
        const superficie = plantables().reduce((total, e) => total + area(e), 0);
        $('#seleccion').innerHTML = `<p class="small text-muted mb-0">Hacé clic en un elemento del plano para editarlo. Tenés ${estado.elementos.length} elemento(s) y ${num(superficie)} m² para cultivar.</p>`;
        return;
    }
    const t = TIPOS[el.tipo];
    const campo = (nombre, etiqueta, paso) => `<div class="col-4"><label class="form-label">${etiqueta}</label><input type="number" class="form-control" data-campo="${nombre}" value="${+el[nombre].toFixed(2)}" step="${paso}"></div>`;

    let cultivos = '';
    if (t.plantable) {
        const lista = enCantero(el);
        cultivos = `
            <div class="subtitulo">Plantado</div>
            ${lista.length ? `<ul class="lista-plano">${lista.map(c => `
                <li><span class="emoji">${emojiDe(c.nombre)}</span><div>
                    <div class="fw-semibold">${esc(c.nombre)}</div>
                    <div class="detalle">Cosecha ${fechaMedia(c.fin)} · entran ≈ ${capacidad(el, c.especie, lista.length)} a ${c.especie?.distancia_cm || 30} cm</div>
                </div></li>`).join('')}</ul>` : '<p class="small text-muted mb-2">Vacío.</p>'}
            ${avisosDe(el).map(a => `<div class="aviso ${a.tipo}"><i class="bi bi-exclamation-circle"></i>${esc(a.texto)}</div>`).join('')}
            ${el.id ? `<a class="btn btn-light btn-sm mt-2" href="${D.urls.nuevo.replace('{id}', el.id)}"><i class="bi bi-plus-lg me-1"></i>Cargar cultivo acá</a>` : ''}`;
    }

    $('#seleccion').innerHTML = `
        <div class="tarjeta-sel">
            <div class="small text-muted mb-2"><i class="bi ${t.icono} text-success me-1"></i>${esc(t.nombre)}${t.plantable ? ` · ${num(area(el))} m²` : ''}</div>
            <div class="row g-2">
                <div class="col-12"><label class="form-label">Nombre</label><input type="text" class="form-control" data-campo="nombre" value="${esc(el.nombre)}" maxlength="60"></div>
                ${campo('ancho', 'Ancho (m)', 0.1)}${campo('largo', 'Largo (m)', 0.1)}${campo('rotacion', 'Giro (°)', 15)}
                ${campo('x', 'X (m)', 0.1)}${campo('y', 'Y (m)', 0.1)}
                ${t.plantable && el.tipo !== 'tunel' ? `<div class="col-12"><div class="form-check form-switch mt-1">
                    <input class="form-check-input" type="checkbox" id="chkMediaSombra" data-campo="media_sombra" ${el.media_sombra ? 'checked' : ''}>
                    <label class="form-check-label small" for="chkMediaSombra">Con media sombra</label></div></div>` : ''}
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-light btn-sm" data-accion="girar"><i class="bi bi-arrow-clockwise me-1"></i>Girar</button>
                <button type="button" class="btn btn-light btn-sm text-danger ms-auto" data-accion="eliminar"><i class="bi bi-trash me-1"></i>Borrar</button>
            </div>
            ${cultivos}
        </div>`;
}

// Límites de cada medida que se puede escribir
const LIMITES = { ancho: [0.2, 50], largo: [0.2, 100], rotacion: [0, 359], x: [0, 200], y: [0, 200] };

$('#seleccion').addEventListener('input', evento => {
    const nombre = evento.target.dataset.campo;
    const el = porClave(estado.seleccion);
    if (!nombre || !el) return;
    if (nombre === 'nombre') el.nombre = evento.target.value;
    else if (nombre === 'media_sombra') el.media_sombra = evento.target.checked;
    else {
        const valor = parseFloat(evento.target.value);
        if (Number.isNaN(valor)) return;
        el[nombre] = limitar(valor, ...LIMITES[nombre]);
    }
    marcarCambios();
    renderCultivos();
});

$('#seleccion').addEventListener('click', evento => {
    const accion = evento.target.closest('[data-accion]')?.dataset.accion;
    const el = porClave(estado.seleccion);
    if (!accion || !el) return;
    if (accion === 'girar') {
        el.rotacion = (el.rotacion + 90) % 360;
    } else {
        estado.elementos = estado.elementos.filter(e => e !== el);
        // Los cultivos que estaban ahí quedan sin ubicar
        Object.keys(estado.ubicaciones).forEach(id => { if (estado.ubicaciones[id] === el.clave) estado.ubicaciones[id] = null; });
        estado.seleccion = null;
        renderCultivos();
        renderHoy();
    }
    marcarCambios();
    renderSeleccion();
});

// ---------- 7. Pestaña Cultivos: en qué cantero está cada uno ----------

function renderCultivos() {
    const activos = CULTIVOS.filter(c => !c.cosechado);
    const canteros = plantables();
    const opciones = elegido => '<option value="">Sin ubicar</option>' + canteros.map(e =>
        `<option value="${e.clave}" ${e.clave === elegido ? 'selected' : ''}>${esc(e.nombre)}</option>`).join('');

    $('#listaCultivos').innerHTML = !activos.length ? '<li class="text-muted">No hay cultivos en curso.</li>'
        : !canteros.length ? '<li class="text-muted">Primero agregá un cantero, tablón o maceta en la pestaña Diseño.</li>'
        : activos.map(c => `
            <li>
                <span class="emoji">${emojiDe(c.nombre)}</span>
                <div class="flex-grow-1 text-truncate fw-semibold">${esc(c.nombre)}${c.variedad ? ` <span class="text-muted fw-normal">${esc(c.variedad)}</span>` : ''}</div>
                <select class="form-select" data-cultivo="${c.id}" aria-label="Cantero de ${esc(c.nombre)}">${opciones(estado.ubicaciones[c.id])}</select>
            </li>`).join('');
}

$('#listaCultivos').addEventListener('change', evento => {
    const id = evento.target.dataset.cultivo;
    if (!id) return;
    estado.ubicaciones[id] = evento.target.value || null;
    marcarCambios();
    renderHoy();
});

// ---------- 8. Pestaña Hoy: tareas del día (también van en la hoja impresa) ----------

// Litros de este mes según la zona y el método de riego
const litrosHoy = c => c.litros * (RIEGO.coeficientes[hoy.getMonth()] ?? 1) / RIEGO.eficiencia;

function tareasDeHoy() {
    const lugar = c => porClave(estado.ubicaciones[c.id])?.nombre ?? 'sin ubicar';
    const activos = CULTIVOS.filter(c => !c.cosechado);
    const regar = activos.filter(c => c.regar);
    const grupos = [
        {
            titulo: `💧 Regar (≈ ${num(regar.reduce((total, c) => total + litrosHoy(c), 0), 0)} L en total)`,
            items: regar.map(c => ({
                texto: `${esc(c.nombre)} en ${esc(lugar(c))} · ≈ ${num(litrosHoy(c))} L`,
                accion: `<form action="${D.urls.regar.replace('{id}', c.id)}" method="POST"><button type="submit" class="btn btn-light btn-sm py-0 px-2">Hecho</button></form>`,
            })),
        },
        {
            titulo: '🧺 Cosechar',
            items: activos.filter(c => c.cosechar).map(c => ({
                texto: `${esc(c.nombre)} en ${esc(lugar(c))}`,
                accion: `<a href="${D.urls.cosechar.replace('{id}', c.id)}" class="btn btn-light btn-sm py-0 px-2">Anotar</a>`,
            })),
        },
        {
            titulo: '🐛 Revisar problemas',
            items: activos.filter(c => c.problemas > 0).map(c => ({ texto: `${esc(c.nombre)} en ${esc(lugar(c))}: ${c.problemas} sin resolver` })),
        },
        {
            titulo: '❄️ Proteger de la helada esta noche si anuncian frío',
            items: !enPeriodoDeHeladas(hoy) ? [] : activos
                .filter(c => c.especie?.sensible_helada && hoy >= c.inicio && !TIPOS[porClave(estado.ubicaciones[c.id])?.tipo]?.protege_helada)
                .map(c => ({ texto: `${esc(c.nombre)} en ${esc(lugar(c))}` })),
        },
    ];
    return grupos.filter(g => g.items.length);
}

function htmlTareas(conAcciones) {
    const grupos = tareasDeHoy();
    if (!grupos.length) return '<p class="small text-muted mb-0">No hay tareas pendientes para hoy. 🌿</p>';
    return grupos.map(g => `
        <div class="subtitulo">${g.titulo}</div>
        <ul class="lista-plano">${g.items.map(i => `<li><span class="check" aria-hidden="true"></span><div class="flex-grow-1">${i.texto}</div>${conAcciones && i.accion ? i.accion : ''}</li>`).join('')}</ul>`).join('');
}

const renderHoy = () => {
    $('#hoy').innerHTML = `<p class="small text-muted mb-2">${fechaMedia(hoy)}. Imprimí el plano con esta lista para quien está de turno en la huerta.</p>${htmlTareas(true)}`;
};

// Hoja para imprimir: el plano y la lista de tareas para tildar
$('#btnImprimir').addEventListener('click', () => {
    $('#hojaImpresion').innerHTML = `
        <h2>${esc(h.nombre ?? 'Huerto')} · ${fechaMedia(hoy)}</h2>
        ${svg.outerHTML}
        <p class="leyenda">${$('#leyenda').innerHTML}</p>
        <h3>Tareas</h3>
        ${htmlTareas(false)}`;
    window.print();
});

// ---------- 9. Pestaña Terreno: medidas ----------

$('#formTerreno').ancho.value = estado.terreno.ancho;
$('#formTerreno').largo.value = estado.terreno.largo;

$('#formTerreno').addEventListener('input', evento => {
    const valor = parseFloat(evento.target.value);
    if (Number.isNaN(valor) || valor < 2 || valor > 200) return;
    estado.terreno[evento.target.name] = valor;
    // Lo que quedó afuera del terreno se acomoda en el borde
    estado.elementos.forEach(el => { el.x = Math.min(el.x, estado.terreno.ancho); el.y = Math.min(el.y, estado.terreno.largo); });
    marcarCambios();
});

// ---------- 10. Pestañas ----------

function abrirPestana(nombre) {
    document.querySelectorAll('[data-pestana]').forEach(b => b.classList.toggle('activa', b.dataset.pestana === nombre));
    document.querySelectorAll('.pestana').forEach(p => p.classList.toggle('activa', p.id === 'pestana-' + nombre));
}
document.querySelectorAll('[data-pestana]').forEach(b => b.addEventListener('click', () => abrirPestana(b.dataset.pestana)));

// ---------- 11. Guardar ----------

async function guardar() {
    const boton = $('#btnGuardar');
    const texto = $('#estadoGuardado');
    boton.disabled = true;
    texto.textContent = 'Guardando…';
    try {
        const respuesta = await fetch(D.urls.guardar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({
                terreno: estado.terreno,
                elementos: estado.elementos.map(({ clave, id, tipo, nombre, x, y, ancho, largo, rotacion, media_sombra }) =>
                    ({ clave, id, tipo, nombre, x, y, ancho, largo, rotacion, media_sombra })),
                ubicaciones: estado.ubicaciones,
            }),
        });
        const datos = await respuesta.json().catch(() => ({}));
        if (!respuesta.ok || datos.ok === false) throw new Error(datos.error || 'No se pudo guardar. Revisá la conexión e intentá de nuevo.');

        // Las claves temporales de los elementos nuevos ("n1") pasan a ser las del id guardado ("e15")
        const nuevaClave = clave => datos.ids[clave] ? 'e' + datos.ids[clave] : null;
        Object.keys(estado.ubicaciones).forEach(id => { if (estado.ubicaciones[id]) estado.ubicaciones[id] = nuevaClave(estado.ubicaciones[id]); });
        estado.seleccion = estado.seleccion && nuevaClave(estado.seleccion);
        estado.elementos = datos.elementos.map(prepararElemento);
        estado.cambios = false;
        texto.textContent = 'Plano guardado';
        texto.classList.remove('pendiente');
        renderTodo();
    } catch (error) {
        texto.textContent = 'No se pudo guardar';
        alert(error.message);
    } finally {
        boton.disabled = false;
    }
}

$('#btnGuardar').addEventListener('click', guardar);

window.addEventListener('beforeunload', evento => {
    if (estado.cambios) evento.preventDefault();
});

// ---------- 12. Inicio ----------

function renderTodo() {
    dibujar();
    renderSeleccion();
    renderCultivos();
    renderHoy();
}

renderTodo();
if (!estado.elementos.length) $('#estadoGuardado').textContent = 'Empezá agregando canteros desde la paleta.';
