// Huerto 3D: diseño del terreno, cultivos ubicados por cantero, planificación de siembras
// y análisis de sol, viento y agua. Los datos vienen del controlador Plano en #datosPlano.

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { CSS2DRenderer, CSS2DObject } from 'three/addons/renderers/CSS2DRenderer.js';

const D = JSON.parse(document.getElementById('datosPlano').textContent);
const TIPOS = D.tipos;
const ESPECIES = D.especies;
const ZONA = D.zona;
const RIEGO = D.riego;

const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
const MESES_CORTOS = ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
const RUMBOS = ['norte', 'noreste', 'este', 'sureste', 'sur', 'suroeste', 'oeste', 'noroeste'];

// ---------- Utilidades ----------

const $ = selector => document.querySelector(selector);
const esc = texto => String(texto ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const num = (valor, decimales = 1) => Number(valor).toLocaleString('es-AR', { maximumFractionDigits: decimales });
const rad = g => g * Math.PI / 180;
const limitar = (v, min, max) => Math.min(max, Math.max(min, v));
const DIA = 86400000;

function fechaLocal(iso) {
    const [a, m, d] = String(iso).slice(0, 10).split('-').map(Number);
    return new Date(a, m - 1, d);
}
const isoDe = f => `${f.getFullYear()}-${String(f.getMonth() + 1).padStart(2, '0')}-${String(f.getDate()).padStart(2, '0')}`;
const sumarDias = (f, n) => { const r = new Date(f); r.setDate(r.getDate() + Math.round(n)); return r; };
const diasEntre = (desde, hasta) => Math.round((hasta - desde) / DIA);
const fechaCorta = f => f.toLocaleDateString('es-AR', { day: 'numeric', month: 'short' });
const fechaMedia = f => f.toLocaleDateString('es-AR', { day: 'numeric', month: 'short', year: 'numeric' });
const normalizar = t => String(t ?? '').toLowerCase().trim().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/\s+/g, ' ');
const listaNombres = items => items.length <= 1 ? items.join('') : items.slice(0, -1).join(', ') + ' y ' + items[items.length - 1];

// Número pseudoaleatorio estable a partir de un texto (las plantas no "saltan" al redibujar)
function azar(semilla) {
    let h = 2166136261;
    for (const c of String(semilla)) h = Math.imul(h ^ c.charCodeAt(0), 16777619);
    return ((h >>> 0) % 10000) / 10000;
}

// ---------- Especies ----------

const indiceEspecies = new Map();
ESPECIES.forEach(e => [e.nombre, ...e.sinonimos].forEach(n => n && indiceEspecies.set(normalizar(n), e)));

// Reconoce la especie por nombre, sinónimo o plural ("Tomates cherry" -> Tomate), igual que la guía de cultivo
function identificar(texto) {
    const palabras = normalizar(texto).split(' ').filter(Boolean);
    for (let n = palabras.length; n > 0; n--) {
        const parte = palabras.slice(0, n);
        for (const variante of [parte, parte.map(p => p.replace(/s$/, '')), parte.map(p => p.replace(/es$/, ''))]) {
            const especie = indiceEspecies.get(variante.join(' '));
            if (especie) return especie;
        }
    }
    return null;
}

// Forma 3D, tamaño adulto (m) y colores de cada especie; se busca por el comienzo del nombre
const FORMAS = {
    tomate:    { forma: 'tutorada', alto: 1.4, ancho: 0.55, hoja: '#3f8a35', fruto: '#d7301f' },
    pimiento:  { forma: 'tutorada', alto: 0.7, ancho: 0.45, hoja: '#2f7d32', fruto: '#c62828' },
    morron:    { forma: 'tutorada', alto: 0.7, ancho: 0.45, hoja: '#2f7d32', fruto: '#c62828' },
    berenjena: { forma: 'tutorada', alto: 0.8, ancho: 0.5, hoja: '#4e7d3a', fruto: '#4a1f5c' },
    poroto:    { forma: 'tutorada', alto: 1.6, ancho: 0.35, hoja: '#5f9c3a', fruto: '#7aa33c' },
    arveja:    { forma: 'tutorada', alto: 1.1, ancho: 0.3, hoja: '#7cb342', fruto: '#8bc34a' },
    haba:      { forma: 'tutorada', alto: 0.9, ancho: 0.35, hoja: '#6e9c3c', fruto: '#9ccc65' },
    lechuga:   { forma: 'roseta', alto: 0.22, ancho: 0.3, hoja: '#9ccc65' },
    acelga:    { forma: 'roseta', alto: 0.45, ancho: 0.4, hoja: '#3e9142', centro: '#c62828' },
    espinaca:  { forma: 'roseta', alto: 0.2, ancho: 0.25, hoja: '#2e7d32' },
    repollo:   { forma: 'roseta', alto: 0.35, ancho: 0.5, hoja: '#8fb98a', centro: '#cfe3b5' },
    brocoli:   { forma: 'roseta', alto: 0.5, ancho: 0.55, hoja: '#4f7f5a', centro: '#2e5e27' },
    zanahoria: { forma: 'hojas', alto: 0.35, ancho: 0.12, hoja: '#5aa02c' },
    cebolla:   { forma: 'hojas', alto: 0.5, ancho: 0.12, hoja: '#6b9e7a', bulbo: '#e8d9b0' },
    ajo:       { forma: 'hojas', alto: 0.5, ancho: 0.12, hoja: '#7fa88a', bulbo: '#f2ece0' },
    rabanito:  { forma: 'hojas', alto: 0.18, ancho: 0.1, hoja: '#6aa84f', bulbo: '#d81b60' },
    perejil:   { forma: 'arbusto', alto: 0.3, ancho: 0.25, hoja: '#2f8f2f' },
    papa:      { forma: 'arbusto', alto: 0.6, ancho: 0.5, hoja: '#4e8a3a' },
    albahaca:  { forma: 'arbusto', alto: 0.45, ancho: 0.35, hoja: '#43a047' },
    zapallito: { forma: 'rastrera', alto: 0.45, ancho: 0.8, hoja: '#4c8c3a', fruto: '#3c6e2a', tamFruto: 0.09 },
    zapallo:   { forma: 'rastrera', alto: 0.4, ancho: 1.2, hoja: '#4c8c3a', fruto: '#e07b24', tamFruto: 0.18 },
    sandia:    { forma: 'rastrera', alto: 0.35, ancho: 1.2, hoja: '#4f8f3c', fruto: '#1f5f2a', tamFruto: 0.2, alargado: 1.3 },
    melon:     { forma: 'rastrera', alto: 0.35, ancho: 1, hoja: '#5c9a3f', fruto: '#e3cb83', tamFruto: 0.13 },
    pepino:    { forma: 'rastrera', alto: 0.4, ancho: 0.7, hoja: '#4a8d2c', fruto: '#3f6f24', tamFruto: 0.05, alargado: 3 },
    maiz:      { forma: 'maiz', alto: 2.2, ancho: 0.5, hoja: '#6aa23a', fruto: '#f2c94c' },
    choclo:    { forma: 'maiz', alto: 2.2, ancho: 0.5, hoja: '#6aa23a', fruto: '#f2c94c' },
    frutilla:  { forma: 'frutilla', alto: 0.2, ancho: 0.3, hoja: '#3c8d3f', fruto: '#e53935' },
};
const FORMA_GENERICA = { forma: 'arbusto', alto: 0.45, ancho: 0.4, hoja: '#4f9a42' };

function formaDe(nombre, especie) {
    const clave = normalizar(especie?.nombre ?? nombre);
    const encontrada = Object.keys(FORMAS).find(k => clave.startsWith(k));
    return encontrada ? FORMAS[encontrada] : FORMA_GENERICA;
}

const EMOJIS = {
    tomate: '🍅', frutilla: '🍓', sandia: '🍉', pimiento: '🫑', morron: '🫑', aji: '🌶️', zanahoria: '🥕', zapallito: '🥒',
    zapallo: '🎃', melon: '🍈', pepino: '🥒', berenjena: '🍆', cebolla: '🧅', ajo: '🧄', papa: '🥔', lechuga: '🥬',
    acelga: '🥬', espinaca: '🥬', repollo: '🥬', brocoli: '🥦', albahaca: '🌿', perejil: '🌿', arveja: '🫛', haba: '🫛',
    poroto: '🫘', maiz: '🌽', choclo: '🌽', rabanito: '🌱',
};
const emojiDe = nombre => {
    const clave = normalizar(nombre);
    const encontrada = Object.keys(EMOJIS).find(k => clave.startsWith(k));
    return encontrada ? EMOJIS[encontrada] : '🌱';
};

// ---------- Calendario según la zona (misma lógica que la guía de cultivo) ----------

function mesesEnZona(meses, especie) {
    if (!ZONA || !especie.sensible_helada || !Number(ZONA.desfase_meses)) return meses;
    return meses.map(m => (m - 1 + Number(ZONA.desfase_meses)) % 12 + 1);
}

// En un túnel las especies sensibles a la helada se pueden adelantar un mes
function mesesPermitidos(especie, el) {
    const meses = mesesEnZona(especie.meses, especie);
    if (el && TIPOS[el.tipo].protege_helada && especie.sensible_helada) {
        return [...new Set([...meses, ...meses.map(m => (m + 10) % 12 + 1)])];
    }
    return meses;
}

const aMesDia = f => String(f.getMonth() + 1).padStart(2, '0') + '-' + String(f.getDate()).padStart(2, '0');

function enPeriodoDeHeladas(fecha) {
    if (!ZONA) return false;
    const md = aMesDia(fecha);
    return md >= ZONA.primera_helada && md < ZONA.ultima_helada;
}

function proximaPrimeraHelada(desde) {
    const [m, d] = ZONA.primera_helada.split('-').map(Number);
    const helada = new Date(desde.getFullYear(), m - 1, d);
    if (helada <= desde) helada.setFullYear(helada.getFullYear() + 1);
    return helada;
}

const nombresMeses = meses => listaNombres([...meses].sort((a, b) => a - b).map(m => MESES[m - 1]));
const esVerano = fecha => Number(estado.terreno.latitud) < 0 ? [11, 0, 1].includes(fecha.getMonth()) : [5, 6, 7].includes(fecha.getMonth());

// ---------- Estado ----------

const hoy = fechaLocal(D.hoy);
const h = D.huerto;

const CULTIVOS = D.cultivos.map(c => {
    const inicio = fechaLocal(c.fecha);
    return { ...c, inicio, fin: sumarDias(inicio, Math.max(1, c.dias_cosecha)), especie: identificar(c.nombre) };
});

function prepararPlan(p) {
    const inicio = fechaLocal(p.fecha);
    return {
        ...p,
        id: Number(p.id),
        cantero_id: Number(p.cantero_id),
        dias_cosecha: Number(p.dias_cosecha),
        plantas: p.plantas ? Number(p.plantas) : null,
        texto: p.especie,
        nombre: p.especie + (p.variedad ? ' ' + p.variedad : ''),
        especie: identificar(p.especie),
        inicio,
        fin: sumarDias(inicio, Math.max(1, Number(p.dias_cosecha))),
    };
}

function prepararElemento(e) {
    return {
        clave: 'e' + e.id, id: Number(e.id), tipo: e.tipo, nombre: e.nombre,
        x: Number(e.x), z: Number(e.z), ancho: Number(e.ancho), largo: Number(e.largo), alto: Number(e.alto),
        rotacion: Number(e.rotacion), media_sombra: Boolean(Number(e.media_sombra)),
    };
}

const estado = {
    terreno: {
        ancho: Number(h.plano_ancho) || 12,
        largo: Number(h.plano_largo) || 10,
        norte: Number(h.norte_grados) || 0,
        latitud: Number(h.latitud),
        longitud: Number(h.longitud),
        viento: h.viento_desde === null || h.viento_desde === '' ? null : Number(h.viento_desde),
    },
    elementos: D.elementos.map(prepararElemento),
    ubicaciones: Object.fromEntries(D.cultivos.map(c => [c.id, c.cantero_id ? 'e' + c.cantero_id : null])),
    planificadas: D.planificadas.map(prepararPlan),
    seleccion: null,
    capa: 'normal',
    vista: '3d',
    dia: 0,
    hora: 11,
    cambios: false,
    planCantero: null,
    planEditando: null,
    resultados: new Map(),
};
let contadorClaves = 0;

const fechaVista = () => sumarDias(hoy, estado.dia);
const porClave = clave => estado.elementos.find(e => e.clave === clave);
const plantables = () => estado.elementos.filter(e => TIPOS[e.tipo].plantable);
const area = el => el.tipo === 'maceta' ? Math.PI * (Math.min(el.ancho, el.largo) / 2) ** 2 : el.ancho * el.largo;

// Cultivos y siembras planificadas que ocupan un cantero en una fecha
function ocupante(origen, ref, fecha) {
    const especie = ref.especie;
    return {
        origen, ref, especie,
        clave: origen + ref.id,
        nombre: ref.nombre,
        inicio: ref.inicio,
        fin: ref.fin,
        progreso: (fecha - ref.inicio) / Math.max(DIA, ref.fin - ref.inicio),
        litros: origen === 'cultivo' ? ref.litros : (especie?.litros ?? 0),
        frecuencia: origen === 'cultivo' ? ref.frecuencia : (especie?.frecuencia ?? 0),
        distancia: especie?.distancia_cm || 30,
    };
}

function ocupantes(el, fecha) {
    const lista = [];
    CULTIVOS.forEach(c => {
        if (c.cosechado || estado.ubicaciones[c.id] !== el.clave) return;
        // Un cultivo atrasado sigue en el cantero hasta que se cosecha
        const fin = c.fin < hoy ? hoy : c.fin;
        if (fecha >= c.inicio && fecha <= fin) lista.push(ocupante('cultivo', c, fecha));
    });
    estado.planificadas.forEach(p => {
        if ('e' + p.cantero_id === el.clave && fecha >= p.inicio && fecha <= p.fin) lista.push(ocupante('plan', p, fecha));
    });
    return lista;
}

// Todo lo que pasó o va a pasar por un cantero (para la rotación y el calendario)
function historial(el) {
    return [
        ...CULTIVOS.filter(c => estado.ubicaciones[c.id] === el.clave).map(c => ({ origen: 'cultivo', ref: c, nombre: c.nombre, especie: c.especie, inicio: c.inicio, fin: c.cosechado || c.fin >= hoy ? c.fin : hoy })),
        ...estado.planificadas.filter(p => 'e' + p.cantero_id === el.clave).map(p => ({ origen: 'plan', ref: p, nombre: p.nombre, especie: p.especie, inicio: p.inicio, fin: p.fin })),
    ].sort((a, b) => a.inicio - b.inicio);
}

// ---------- Sol ----------

// Posición del sol para una fecha y hora del reloj (altura y acimut desde el norte, en radianes)
function posicionSol(fecha, horaReloj) {
    const lat = rad(estado.terreno.latitud);
    const inicioAnio = new Date(fecha.getFullYear(), 0, 0);
    const n = Math.floor((fecha - inicioAnio) / DIA);
    const declinacion = rad(23.44) * Math.sin(2 * Math.PI * (284 + n) / 365);
    const b = 2 * Math.PI * (n - 81) / 364;
    const ecuacionTiempo = 9.87 * Math.sin(2 * b) - 7.53 * Math.cos(b) - 1.5 * Math.sin(b);
    const husoHorario = -fecha.getTimezoneOffset() / 60;
    const horaSolar = horaReloj + estado.terreno.longitud / 15 - husoHorario + ecuacionTiempo / 60;
    const angulo = rad(15 * (horaSolar - 12));

    const alt = Math.asin(Math.sin(lat) * Math.sin(declinacion) + Math.cos(lat) * Math.cos(declinacion) * Math.cos(angulo));
    const cosAz = (Math.sin(declinacion) - Math.sin(alt) * Math.sin(lat)) / (Math.cos(alt) * Math.cos(lat));
    let az = Math.acos(limitar(cosAz, -1, 1));
    if (angulo > 0) az = 2 * Math.PI - az;
    return { alt, az };
}

// Dirección horizontal en el mundo para un rumbo (grados desde el norte, sentido horario)
function direccion(rumbo) {
    const a = rad(estado.terreno.norte + rumbo);
    return new THREE.Vector3(Math.sin(a), 0, -Math.cos(a));
}

function vectorSol(pos) {
    const d = direccion(pos.az * 180 / Math.PI);
    return new THREE.Vector3(d.x * Math.cos(pos.alt), Math.sin(pos.alt), d.z * Math.cos(pos.alt)).normalize();
}

const rumboTexto = grados => RUMBOS[Math.round(((grados % 360) + 360) % 360 / 45) % 8];

function horasDeLuz(fecha) {
    let salida = null, puesta = null;
    for (let t = 3; t <= 23; t += 0.05) {
        const arriba = posicionSol(fecha, t).alt > 0;
        if (arriba && salida === null) salida = t;
        if (!arriba && salida !== null && puesta === null) puesta = t;
    }
    return { salida, puesta };
}
const horaTexto = t => `${Math.floor(t)}:${String(Math.round((t % 1) * 60) % 60).padStart(2, '0')}`;

// ---------- Escena ----------

const contenedor = $('#escena');
let renderer;
try {
    renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
} catch (e) {
    $('#cargando').innerHTML = '<i class="bi bi-exclamation-triangle fs-3 text-warning"></i>Tu navegador no puede mostrar gráficos 3D.';
    throw e;
}
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;
renderer.outputColorSpace = THREE.SRGBColorSpace;
contenedor.appendChild(renderer.domElement);

const etiquetas = new CSS2DRenderer();
Object.assign(etiquetas.domElement.style, { position: 'absolute', top: '0', left: '0', pointerEvents: 'none' });
contenedor.appendChild(etiquetas.domElement);

const escena = new THREE.Scene();
escena.background = new THREE.Color('#cfe3ee');
escena.fog = new THREE.Fog('#cfe3ee', 70, 220);

const camara = new THREE.PerspectiveCamera(45, 1, 0.1, 600);
const camaraPlanta = new THREE.OrthographicCamera(-10, 10, 10, -10, 0.1, 600);

const controles = new OrbitControls(camara, renderer.domElement);
controles.enableDamping = true;
controles.maxPolarAngle = Math.PI / 2 - 0.04;
controles.minDistance = 2;
controles.maxDistance = 180;

const controlesPlanta = new OrbitControls(camaraPlanta, renderer.domElement);
controlesPlanta.enableRotate = false;
controlesPlanta.screenSpacePanning = true;
controlesPlanta.mouseButtons = { LEFT: THREE.MOUSE.PAN, MIDDLE: THREE.MOUSE.DOLLY, RIGHT: THREE.MOUSE.PAN };
controlesPlanta.touches = { ONE: THREE.TOUCH.PAN, TWO: THREE.TOUCH.DOLLY_PAN };
controlesPlanta.enabled = false;

const camaraActiva = () => estado.vista === '3d' ? camara : camaraPlanta;
const controlesActivos = () => estado.vista === '3d' ? controles : controlesPlanta;

const cielo = new THREE.HemisphereLight('#e3f1ff', '#8a7a5a', 1.1);
const sol = new THREE.DirectionalLight('#fff3dc', 2.6);
sol.castShadow = true;
sol.shadow.mapSize.set(2048, 2048);
sol.shadow.bias = -0.0004;
sol.shadow.normalBias = 0.03;
escena.add(cielo, sol, sol.target);

const grupoTerreno = new THREE.Group();
const grupoElementos = new THREE.Group();
const grupoCapa = new THREE.Group();
escena.add(grupoTerreno, grupoElementos, grupoCapa);

// Materiales compartidos
const materiales = new Map();
function mat(color, extra = {}) {
    const clave = color + JSON.stringify(extra);
    if (!materiales.has(clave)) materiales.set(clave, new THREE.MeshStandardMaterial({ color, roughness: 0.85, ...extra }));
    return materiales.get(clave);
}
const M = {
    madera: mat('#9b6a3c'),
    tierra: mat('#5a3d26'),
    terracota: mat('#c0643a'),
    plastico: mat('#eaf6ff', { transparent: true, opacity: 0.28, side: THREE.DoubleSide, depthWrite: false, roughness: 0.2 }),
    aro: mat('#d9dde0', { metalness: 0.4, roughness: 0.4 }),
    tronco: mat('#6d4c33'),
    copa: mat('#4f8a3a', { flatShading: true }),
    alamo: mat('#5f9a3f', { flatShading: true }),
    muro: mat('#cdb89c'),
    techo: mat('#8d8f91', { metalness: 0.3 }),
    tanque: mat('#2f4858', { roughness: 0.5 }),
    tapa: mat('#3d5a6c', { roughness: 0.5 }),
    compost: mat('#4a3424'),
    agua: mat('#4aa3d8', { transparent: true, opacity: 0.85, roughness: 0.1 }),
    hormigon: mat('#b8b2a6'),
    camino: mat('#d6c9a8'),
    red: mat('#24502b', { transparent: true, opacity: 0.55, side: THREE.DoubleSide, depthWrite: false }),
    poste: mat('#8a8a8a'),
};

function caja(ancho, alto, largo, material, x = 0, y = 0, z = 0) {
    const malla = new THREE.Mesh(new THREE.BoxGeometry(ancho, alto, largo), material);
    malla.position.set(x, y + alto / 2, z);
    return malla;
}

// ---------- Terreno ----------

function texturaTerreno() {
    const lienzo = document.createElement('canvas');
    lienzo.width = lienzo.height = 256;
    const ctx = lienzo.getContext('2d');
    ctx.fillStyle = '#a9b57c';
    ctx.fillRect(0, 0, 256, 256);
    const tonos = ['#9aae6c', '#b4c286', '#8fa35f', '#bbb28c', '#a39a74', '#c2b993'];
    for (let i = 0; i < 2600; i++) {
        ctx.fillStyle = tonos[i % tonos.length];
        const t = 1 + (i % 3);
        ctx.fillRect(azar('x' + i) * 256, azar('y' + i) * 256, t, t);
    }
    const textura = new THREE.CanvasTexture(lienzo);
    textura.wrapS = textura.wrapT = THREE.RepeatWrapping;
    textura.colorSpace = THREE.SRGBColorSpace;
    return textura;
}
const TEXTURA_TERRENO = texturaTerreno();

function textoPlano(texto, color = '#c0392b') {
    const lienzo = document.createElement('canvas');
    lienzo.width = lienzo.height = 128;
    const ctx = lienzo.getContext('2d');
    ctx.fillStyle = color;
    ctx.font = 'bold 96px Outfit, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(texto, 64, 70);
    const textura = new THREE.CanvasTexture(lienzo);
    textura.colorSpace = THREE.SRGBColorSpace;
    const malla = new THREE.Mesh(new THREE.PlaneGeometry(1, 1), new THREE.MeshBasicMaterial({ map: textura, transparent: true, depthWrite: false }));
    malla.rotation.x = -Math.PI / 2;
    return malla;
}

let grilla = null;

function construirTerreno() {
    grupoTerreno.children.slice().forEach(hijo => { grupoTerreno.remove(hijo); hijo.traverse(o => o.geometry?.dispose()); });
    const { ancho, largo } = estado.terreno;

    const alrededor = new THREE.Mesh(new THREE.PlaneGeometry(600, 600), mat('#d9cdb0'));
    alrededor.rotation.x = -Math.PI / 2;
    alrededor.position.y = -0.02;
    alrededor.receiveShadow = true;

    TEXTURA_TERRENO.repeat.set(ancho / 3, largo / 3);
    const suelo = new THREE.Mesh(new THREE.PlaneGeometry(ancho, largo), new THREE.MeshStandardMaterial({ map: TEXTURA_TERRENO, roughness: 1 }));
    suelo.rotation.x = -Math.PI / 2;
    suelo.receiveShadow = true;
    grupoTerreno.add(alrededor, suelo);

    // Grilla de 1 m
    const puntos = [];
    for (let x = 0; x <= ancho + 0.001; x += 1) puntos.push(x - ancho / 2, 0.006, -largo / 2, x - ancho / 2, 0.006, largo / 2);
    for (let z = 0; z <= largo + 0.001; z += 1) puntos.push(-ancho / 2, 0.006, z - largo / 2, ancho / 2, 0.006, z - largo / 2);
    const geometria = new THREE.BufferGeometry();
    geometria.setAttribute('position', new THREE.Float32BufferAttribute(puntos, 3));
    grilla = new THREE.LineSegments(geometria, new THREE.LineBasicMaterial({ color: '#ffffff', transparent: true, opacity: 0.28 }));
    grilla.visible = $('#btnGrilla').classList.contains('activo');
    grilla.raycast = () => {};
    grupoTerreno.add(grilla);

    // Alambrado: postes cada 2 m y dos hilos
    const perimetro = [];
    const esquinas = [[-ancho / 2, -largo / 2], [ancho / 2, -largo / 2], [ancho / 2, largo / 2], [-ancho / 2, largo / 2]];
    esquinas.forEach(([x1, z1], i) => {
        const [x2, z2] = esquinas[(i + 1) % 4];
        const tramos = Math.max(1, Math.round(Math.hypot(x2 - x1, z2 - z1) / 2));
        for (let t = 0; t < tramos; t++) perimetro.push([x1 + (x2 - x1) * t / tramos, z1 + (z2 - z1) * t / tramos]);
    });
    const postes = new THREE.InstancedMesh(new THREE.CylinderGeometry(0.035, 0.04, 0.9, 6), M.madera, perimetro.length);
    perimetro.forEach(([x, z], i) => postes.setMatrixAt(i, new THREE.Matrix4().makeTranslation(x, 0.45, z)));
    postes.castShadow = true;
    grupoTerreno.add(postes);
    [0.4, 0.8].forEach(y => {
        const hilo = new THREE.LineLoop(
            new THREE.BufferGeometry().setFromPoints(esquinas.map(([x, z]) => new THREE.Vector3(x, y, z))),
            new THREE.LineBasicMaterial({ color: '#6b6b6b' })
        );
        hilo.raycast = () => {};
        grupoTerreno.add(hilo);
    });

    // Flecha del norte, afuera del terreno
    const norte = direccion(0);
    const distancia = Math.abs(norte.x) * ancho / 2 + Math.abs(norte.z) * largo / 2 + 1.8;
    const brujula = new THREE.Group();
    const forma = new THREE.Shape([new THREE.Vector2(0, 0.55), new THREE.Vector2(0.3, -0.25), new THREE.Vector2(0, -0.1), new THREE.Vector2(-0.3, -0.25)]);
    const flecha = new THREE.Mesh(new THREE.ShapeGeometry(forma), new THREE.MeshBasicMaterial({ color: '#c0392b', side: THREE.DoubleSide }));
    flecha.rotation.x = -Math.PI / 2;
    const letra = textoPlano('N');
    letra.position.z = -1;
    letra.scale.setScalar(0.8);
    brujula.add(flecha, letra);
    brujula.scale.setScalar(1.6);
    brujula.position.set(norte.x * distancia, 0.03, norte.z * distancia);
    brujula.rotation.y = -rad(estado.terreno.norte);
    grupoTerreno.add(brujula);

    actualizarSol();
}

// ---------- Elementos ----------

const objetos = new Map(); // clave -> { grupo, suelo, etiqueta, plantas }

function construirElemento(el) {
    const grupo = new THREE.Group();
    grupo.userData.clave = el.clave;
    const A = el.ancho, L = el.largo, H = el.alto;
    const sueloMat = M.tierra.clone();
    let suelo = null;

    switch (el.tipo) {
        case 'cantero': {
            const t = 0.05;
            suelo = caja(A - 2 * t, H - 0.03, L - 2 * t, sueloMat);
            grupo.add(suelo,
                caja(A, H, t, M.madera, 0, 0, -L / 2 + t / 2), caja(A, H, t, M.madera, 0, 0, L / 2 - t / 2),
                caja(t, H, L - 2 * t, M.madera, -A / 2 + t / 2), caja(t, H, L - 2 * t, M.madera, A / 2 - t / 2));
            break;
        }
        case 'tablon':
            suelo = caja(A, H, L, sueloMat);
            grupo.add(suelo);
            break;
        case 'maceta': {
            const r = Math.min(A, L) / 2;
            const maceta = new THREE.Mesh(new THREE.CylinderGeometry(r, r * 0.8, H, 20), M.terracota);
            maceta.position.y = H / 2;
            suelo = new THREE.Mesh(new THREE.CylinderGeometry(r * 0.92, r * 0.92, 0.02, 20), sueloMat);
            suelo.position.y = H - 0.04;
            grupo.add(maceta, suelo);
            break;
        }
        case 'tunel': {
            suelo = caja(A - 0.3, 0.12, L - 0.2, sueloMat);
            const geometria = new THREE.CylinderGeometry(A / 2, A / 2, L, 28, 1, true, Math.PI / 2, Math.PI);
            geometria.rotateX(Math.PI / 2);
            const techo = new THREE.Mesh(geometria, M.plastico);
            techo.scale.y = H / (A / 2);
            techo.userData.sinSombra = true;
            grupo.add(suelo, techo);
            const aros = Math.max(2, Math.round(L / 1.25) + 1);
            for (let i = 0; i < aros; i++) {
                const aro = new THREE.Mesh(new THREE.TorusGeometry(A / 2, 0.012, 4, 24, Math.PI), M.aro);
                aro.scale.y = H / (A / 2);
                aro.position.z = -L / 2 + i * L / (aros - 1);
                grupo.add(aro);
            }
            break;
        }
        case 'arbol': {
            grupo.add(caja(0.22, H * 0.5, 0.22, M.tronco));
            const r = A / 2;
            [[0, 0, 0, 0.8], [r * 0.35, -r * 0.2, r * 0.2, 0.6], [-r * 0.3, -r * 0.15, -r * 0.25, 0.62], [r * 0.1, r * 0.25, -r * 0.1, 0.5]].forEach(([x, y, z, s]) => {
                const copa = new THREE.Mesh(new THREE.IcosahedronGeometry(r * s, 1), M.copa);
                copa.position.set(x, H - r * 0.7 + y, z);
                copa.scale.y = 0.85;
                grupo.add(copa);
            });
            break;
        }
        case 'cortina': {
            // Árboles con las copas tocándose, como una cortina cerrada
            const radio = Math.max(A / 2, 0.35);
            const cantidad = Math.max(1, Math.round(L / (radio * 1.5)));
            for (let i = 0; i < cantidad; i++) {
                const z = -L / 2 + (i + 0.5) * L / cantidad;
                grupo.add(caja(0.12, H * 0.2, 0.12, M.tronco, 0, 0, z));
                const copa = new THREE.Mesh(new THREE.ConeGeometry(radio, H * 0.85, 7), M.alamo);
                copa.position.set(0, H * 0.15 + H * 0.85 / 2, z);
                grupo.add(copa);
            }
            break;
        }
        case 'pared':
            grupo.add(caja(A, H, L, M.muro));
            if (A >= 2) grupo.add(caja(A + 0.25, 0.08, L + 0.25, M.techo, 0, H));
            break;
        case 'tanque': {
            const r = Math.min(A, L) / 2;
            const tanque = new THREE.Mesh(new THREE.CylinderGeometry(r, r, H, 24), M.tanque);
            tanque.position.y = H / 2;
            const tapa = new THREE.Mesh(new THREE.CylinderGeometry(r * 0.35, r * 0.35, 0.08, 16), M.tapa);
            tapa.position.y = H + 0.04;
            grupo.add(tanque, tapa);
            break;
        }
        case 'compostera': {
            grupo.add(caja(A - 0.1, H * 0.6, L - 0.1, M.compost));
            for (let i = 0; i < 3; i++) {
                const y = i * H / 3 + 0.02;
                const alto = H / 3 - 0.06;
                grupo.add(caja(A, alto, 0.03, M.madera, 0, y, -L / 2), caja(A, alto, 0.03, M.madera, 0, y, L / 2),
                    caja(0.03, alto, L, M.madera, -A / 2, y), caja(0.03, alto, L, M.madera, A / 2, y));
            }
            break;
        }
        case 'acequia':
            grupo.add(caja(0.08, 0.12, L, M.hormigon, -A / 2 + 0.04), caja(0.08, 0.12, L, M.hormigon, A / 2 - 0.04),
                caja(A - 0.16, 0.06, L, M.agua));
            break;
        case 'camino':
            grupo.add(caja(A, Math.max(H, 0.02), L, M.camino));
            break;
    }

    // Media sombra sobre el cantero
    if (TIPOS[el.tipo].plantable && el.media_sombra && el.tipo !== 'tunel') {
        const alto = 1.9;
        [[-1, -1], [1, -1], [1, 1], [-1, 1]].forEach(([sx, sz]) => {
            const poste = new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.03, alto, 6), M.poste);
            poste.position.set(sx * A / 2, alto / 2, sz * L / 2);
            grupo.add(poste);
        });
        const red = new THREE.Mesh(new THREE.PlaneGeometry(A + 0.1, L + 0.1), M.red);
        red.rotation.x = -Math.PI / 2;
        red.position.y = alto;
        red.userData.sinSombra = true;
        grupo.add(red);
    }

    grupo.traverse(o => {
        if (o.isMesh) {
            o.castShadow = !o.userData.sinSombra;
            o.receiveShadow = true;
        }
    });

    // Etiqueta con el nombre y los avisos
    const div = document.createElement('div');
    div.className = 'etiqueta-3d';
    const etiqueta = new CSS2DObject(div);
    etiqueta.position.y = (el.tipo === 'tunel' ? H : Math.max(H, 0.3)) + (TIPOS[el.tipo].plantable ? 0.55 : 0.3);
    etiqueta.raycast = () => {};
    grupo.add(etiqueta);

    return { grupo, suelo, sueloColor: sueloMat.color.clone(), etiqueta, div, plantas: null };
}

function posicionar(el) {
    const obj = objetos.get(el.clave);
    if (!obj) return;
    obj.grupo.position.set(el.x - estado.terreno.ancho / 2, 0, el.z - estado.terreno.largo / 2);
    obj.grupo.rotation.y = -rad(el.rotacion);
    obj.grupo.updateMatrixWorld(true);
}

function quitarObjeto(clave) {
    const obj = objetos.get(clave);
    if (!obj) return;
    grupoElementos.remove(obj.grupo);
    obj.grupo.traverse(o => {
        if (o.isInstancedMesh) o.dispose();
        else if (o.geometry && !o.userData.geometriaCompartida) o.geometry.dispose();
    });
    obj.suelo?.material.dispose();
    obj.etiqueta.element.remove();
    objetos.delete(clave);
}

function reconstruirElemento(el) {
    quitarObjeto(el.clave);
    const obj = construirElemento(el);
    objetos.set(el.clave, obj);
    grupoElementos.add(obj.grupo);
    posicionar(el);
    construirPlantas(el);
    if (estado.seleccion === el.clave) marcarSeleccion();
}

function reconstruirTodo() {
    [...objetos.keys()].forEach(quitarObjeto);
    estado.elementos.forEach(reconstruirElemento);
    marcarSeleccion();
}

// ---------- Plantas ----------

const GEO = {
    esfera: new THREE.IcosahedronGeometry(1, 1),
    cono: new THREE.ConeGeometry(1, 1, 5),
    cilindro: new THREE.CylinderGeometry(1, 1, 1, 6),
    caja: new THREE.BoxGeometry(1, 1, 1),
};
const materialesPlanta = new Map();
function matPlanta(color) {
    if (!materialesPlanta.has(color)) materialesPlanta.set(color, new THREE.MeshStandardMaterial({ color, roughness: 0.75, flatShading: true }));
    return materialesPlanta.get(color);
}
const secar = color => '#' + new THREE.Color(color).lerp(new THREE.Color('#b8a64a'), 0.5).getHexString();

// Junta las piezas de todas las plantas de un cantero en pocas mallas instanciadas
class Colector {
    constructor() { this.piezas = new Map(); }
    agregar(geo, material, matriz) {
        const clave = geo.uuid + material.uuid;
        if (!this.piezas.has(clave)) this.piezas.set(clave, { geo, material, matrices: [] });
        this.piezas.get(clave).matrices.push(matriz);
    }
    construir() {
        const grupo = new THREE.Group();
        this.piezas.forEach(({ geo, material, matrices }) => {
            const malla = new THREE.InstancedMesh(geo, material, matrices.length);
            matrices.forEach((m, i) => malla.setMatrixAt(i, m));
            malla.castShadow = true;
            malla.userData.geometriaCompartida = true;
            grupo.add(malla);
        });
        return grupo;
    }
}

const _q = new THREE.Quaternion();
const _e = new THREE.Euler();
function pieza(col, base, geo, color, [x, y, z], [sx, sy, sz], [rx, ry, rz] = [0, 0, 0]) {
    const local = new THREE.Matrix4().compose(new THREE.Vector3(x, y, z), _q.setFromEuler(_e.set(rx, ry, rz)), new THREE.Vector3(sx, sy, sz));
    col.agregar(geo, matPlanta(color), base.clone().multiply(local));
}

function dibujarPlanta(col, base, f, progreso, ancho, seca, semilla) {
    const g = 0.15 + 0.85 * limitar(progreso, 0, 1);
    const H = f.alto * g;
    const W = Math.min(f.ancho, ancho) * g;
    const hoja = seca ? secar(f.hoja) : f.hoja;
    const r = i => azar(semilla + ':' + i);

    switch (f.forma) {
        case 'tutorada': {
            if (progreso > 0.15) pieza(col, base, GEO.cilindro, '#a07a4f', [0.04, H * 0.55, 0], [0.01, H * 1.1, 0.01]);
            [[0.3, 0.5], [0.58, 0.45], [0.84, 0.32]].forEach(([y, s], i) =>
                pieza(col, base, GEO.esfera, hoja, [(r(i) - 0.5) * W * 0.2, H * y, (r(i + 9) - 0.5) * W * 0.2], [W * s, H * 0.22, W * s]));
            if (progreso > 0.55 && f.fruto) {
                const tam = 0.025 + 0.025 * limitar(progreso, 0, 1);
                for (let i = 0; i < 5; i++) {
                    const a = r(i + 20) * Math.PI * 2;
                    pieza(col, base, GEO.esfera, f.fruto, [Math.cos(a) * W * 0.42, H * (0.3 + r(i + 30) * 0.45), Math.sin(a) * W * 0.42], [tam, tam * 1.1, tam]);
                }
            }
            break;
        }
        case 'roseta': {
            const hojas = 7;
            for (let i = 0; i < hojas; i++) {
                const a = i / hojas * Math.PI * 2 + r(i);
                pieza(col, base, GEO.esfera, hoja, [Math.cos(a) * W * 0.22, H * 0.4, Math.sin(a) * W * 0.22], [W * 0.26, H * 0.45, W * 0.13], [0, -a, 0.5]);
            }
            pieza(col, base, GEO.esfera, f.centro ?? hoja, [0, H * 0.45, 0], [W * 0.2, H * 0.45, W * 0.2]);
            break;
        }
        case 'hojas': {
            for (let i = 0; i < 6; i++) {
                pieza(col, base, GEO.cono, hoja, [(r(i) - 0.5) * W * 0.4, H / 2, (r(i + 7) - 0.5) * W * 0.4], [0.012 + W * 0.05, H, 0.012 + W * 0.05], [(r(i + 3) - 0.5) * 0.5, 0, (r(i + 5) - 0.5) * 0.5]);
            }
            if (f.bulbo && progreso > 0.5) pieza(col, base, GEO.esfera, f.bulbo, [0, 0.02, 0], [0.035 * g, 0.03 * g, 0.035 * g]);
            break;
        }
        case 'rastrera': {
            for (let i = 0; i < 6; i++) {
                const a = i / 6 * Math.PI * 2 + r(i);
                const d = i === 0 ? 0 : W * 0.32;
                pieza(col, base, GEO.esfera, hoja, [Math.cos(a) * d, H * 0.4, Math.sin(a) * d], [W * 0.24, H * 0.45, W * 0.24]);
            }
            if (progreso > 0.5 && f.fruto) {
                const t = f.tamFruto * (0.4 + 0.6 * limitar(progreso, 0, 1));
                const a = r(40) * Math.PI * 2;
                pieza(col, base, GEO.esfera, f.fruto, [Math.cos(a) * W * 0.45, t * 0.8, Math.sin(a) * W * 0.45], [t * (f.alargado ?? 1), t * 0.85, t], [0, a, 0]);
            }
            break;
        }
        case 'maiz': {
            pieza(col, base, GEO.cilindro, '#8bb34a', [0, H / 2, 0], [0.02, H, 0.02]);
            for (let i = 0; i < 6; i++) {
                const a = i * 1.05 + r(i);
                pieza(col, base, GEO.caja, hoja, [Math.cos(a) * W * 0.22, H * (0.25 + i * 0.1), Math.sin(a) * W * 0.22], [W * 0.5, 0.01, 0.05], [0, -a, -0.45]);
            }
            if (progreso > 0.75) pieza(col, base, GEO.cono, '#c9a25a', [0, H + 0.08, 0], [0.03, 0.18, 0.03]);
            if (progreso > 0.6) pieza(col, base, GEO.cilindro, f.fruto, [0.05, H * 0.55, 0], [0.03, 0.17, 0.03], [0, 0, -0.35]);
            break;
        }
        case 'frutilla': {
            for (let i = 0; i < 3; i++) {
                const a = i * 2.1 + r(i);
                pieza(col, base, GEO.esfera, hoja, [Math.cos(a) * W * 0.15, H * 0.5, Math.sin(a) * W * 0.15], [W * 0.25, H * 0.5, W * 0.25]);
            }
            if (progreso > 0.4) {
                for (let i = 0; i < 3; i++) {
                    const a = r(i + 10) * Math.PI * 2;
                    pieza(col, base, GEO.cono, f.fruto, [Math.cos(a) * W * 0.4, 0.025, Math.sin(a) * W * 0.4], [0.018, 0.035, 0.018], [Math.PI, 0, 0]);
                }
            }
            break;
        }
        default: {
            [[0, 0.5, 0.42], [0.18, 0.4, 0.3], [-0.15, 0.42, 0.32], [0.02, 0.72, 0.28]].forEach(([dx, y, s], i) =>
                pieza(col, base, GEO.esfera, hoja, [dx * W, H * y, (r(i) - 0.5) * W * 0.3], [W * s, H * s * 0.9, W * s]));
        }
    }
}

// Zona donde se planta dentro de cada tipo de elemento (medidas locales y altura del suelo)
function zonaPlantable(el) {
    switch (el.tipo) {
        case 'cantero': return { ancho: el.ancho - 0.14, largo: el.largo - 0.14, y: el.alto - 0.03 };
        case 'maceta': { const lado = Math.min(el.ancho, el.largo) * 0.62; return { ancho: lado, largo: lado, y: el.alto - 0.03 }; }
        case 'tunel': return { ancho: el.ancho - 0.4, largo: el.largo - 0.3, y: 0.12 };
        default: return { ancho: el.ancho - 0.05, largo: el.largo - 0.05, y: el.alto };
    }
}

// Cada cultivo ocupa una franja del cantero, con plantas a la distancia de su especie
function construirPlantas(el) {
    const obj = objetos.get(el.clave);
    if (!obj) return;
    if (obj.plantas) {
        obj.grupo.remove(obj.plantas);
        obj.plantas.children.forEach(m => m.dispose());
        obj.plantas = null;
    }
    if (!TIPOS[el.tipo].plantable) return;

    const lista = ocupantes(el, fechaVista());
    if (!lista.length) return;

    const zona = zonaPlantable(el);
    const col = new Colector();
    const franja = zona.largo / lista.length;

    lista.forEach((o, i) => {
        const z0 = -zona.largo / 2 + franja * i;
        let paso = Math.max(o.distancia / 100, 0.12);
        let columnas, filas;
        const contar = () => {
            columnas = Math.max(1, Math.floor(zona.ancho / paso));
            filas = Math.max(1, Math.floor(franja / paso));
        };
        contar();
        // Pocas plantas visibles por franja para que el plano siga siendo liviano
        while (columnas * filas > 40) { paso *= 1.15; contar(); }

        const forma = formaDe(o.nombre, o.especie);
        const seca = o.origen === 'cultivo' && o.ref.regar && estado.dia === 0;
        for (let fila = 0; fila < filas; fila++) {
            for (let c = 0; c < columnas; c++) {
                const semilla = `${o.clave}-${fila}-${c}`;
                const x = -zona.ancho / 2 + (c + 0.5) * zona.ancho / columnas + (azar(semilla + 'x') - 0.5) * paso * 0.15;
                const z = z0 + (fila + 0.5) * franja / filas + (azar(semilla + 'z') - 0.5) * paso * 0.15;
                const base = new THREE.Matrix4().compose(
                    new THREE.Vector3(x, zona.y, z),
                    new THREE.Quaternion().setFromAxisAngle(new THREE.Vector3(0, 1, 0), azar(semilla) * Math.PI * 2),
                    new THREE.Vector3(1, 1, 1)
                );
                dibujarPlanta(col, base, forma, o.progreso, Math.min(zona.ancho / columnas, franja / filas) * 1.1, seca, semilla);
            }
        }
    });

    obj.plantas = col.construir();
    obj.grupo.add(obj.plantas);
}

const construirTodasLasPlantas = () => estado.elementos.forEach(construirPlantas);

// ---------- Selección ----------

let contornoSeleccion = null;

function marcarSeleccion() {
    if (contornoSeleccion) {
        contornoSeleccion.parent?.remove(contornoSeleccion);
        contornoSeleccion.geometry.dispose();
        contornoSeleccion = null;
    }
    objetos.forEach((obj, clave) => obj.div.classList.toggle('seleccionada', clave === estado.seleccion));
    const el = porClave(estado.seleccion);
    const obj = el && objetos.get(el.clave);
    if (!obj) return;
    const a = el.ancho / 2 + 0.1, l = el.largo / 2 + 0.1;
    contornoSeleccion = new THREE.LineLoop(
        new THREE.BufferGeometry().setFromPoints([[-a, -l], [a, -l], [a, l], [-a, l]].map(([x, z]) => new THREE.Vector3(x, 0.04, z))),
        new THREE.LineBasicMaterial({ color: '#8cc63f' })
    );
    contornoSeleccion.raycast = () => {};
    obj.grupo.add(contornoSeleccion);
}

function seleccionar(clave) {
    estado.seleccion = clave;
    const el = porClave(clave);
    if (el && TIPOS[el.tipo].plantable) estado.planCantero = clave;
    marcarSeleccion();
    renderSeleccion();
    renderPlanificar();
}

// ---------- Cambios ----------

function marcarCambios() {
    estado.cambios = true;
    const texto = $('#estadoGuardado');
    texto.textContent = 'Hay cambios sin guardar';
    texto.classList.add('pendiente');
    programarAnalisis();
}

function nuevaClave() {
    return 'n' + (++contadorClaves);
}

// Busca un lugar libre en el terreno para un elemento nuevo
function lugarLibre(ancho, largo) {
    const { ancho: T, largo: L } = estado.terreno;
    const choca = (x, z) => estado.elementos.some(e =>
        Math.abs(e.x - x) < (e.ancho + ancho) / 2 + 0.3 && Math.abs(e.z - z) < (e.largo + largo) / 2 + 0.3);
    for (let z = largo / 2 + 0.5; z <= L - largo / 2; z += 0.5) {
        for (let x = ancho / 2 + 0.5; x <= T - ancho / 2; x += 0.5) {
            if (!choca(x, z)) return { x, z };
        }
    }
    return { x: T / 2, z: L / 2 };
}

function agregarElemento(tipo, datos = {}) {
    const [ancho, largo, alto] = TIPOS[tipo].medidas;
    const iguales = estado.elementos.filter(e => e.tipo === tipo).length;
    const el = {
        clave: nuevaClave(), id: null, tipo,
        nombre: `${TIPOS[tipo].nombre.split(' ')[0]} ${iguales + 1}`,
        ancho, largo, alto, rotacion: 0, media_sombra: false,
        ...lugarLibre(ancho, largo),
        ...datos,
    };
    estado.elementos.push(el);
    reconstruirElemento(el);
    return el;
}

function eliminarElemento(clave) {
    const el = porClave(clave);
    if (!el) return;
    const afectados = el.id ? estado.planificadas.filter(p => p.cantero_id === el.id).length : 0;
    if (afectados && !confirm(`"${el.nombre}" tiene ${afectados} siembra(s) planificada(s). Si lo borrás y guardás, se borran también. ¿Seguir?`)) return;

    estado.elementos = estado.elementos.filter(e => e.clave !== clave);
    Object.keys(estado.ubicaciones).forEach(id => { if (estado.ubicaciones[id] === clave) estado.ubicaciones[id] = null; });
    quitarObjeto(clave);
    if (estado.seleccion === clave) estado.seleccion = null;
    if (estado.planCantero === clave) estado.planCantero = null;
    marcarSeleccion();
    marcarCambios();
    renderTodo();
}

// ---------- Análisis ----------

const raycaster = new THREE.Raycaster();

function mallasObstaculo() {
    return estado.elementos.filter(e => TIPOS[e.tipo].obstaculo).map(e => objetos.get(e.clave)?.grupo).filter(Boolean);
}

function puntosMuestra(el, altura, desplazamientos) {
    const obj = objetos.get(el.clave);
    return desplazamientos.map(([fx, fz]) => obj.grupo.localToWorld(new THREE.Vector3(fx * el.ancho, altura, fz * el.largo)));
}

// Horas de sol directo que recibe un cantero en una fecha (los árboles, muros y tanques dan sombra)
const cacheSol = new Map();
let versionGeometria = 0;

function horasSol(el, fecha) {
    const clave = `${el.clave}|${isoDe(fecha)}|${versionGeometria}`;
    if (cacheSol.has(clave)) return cacheSol.get(clave);

    const obstaculos = mallasObstaculo();
    const puntos = puntosMuestra(el, el.alto + 0.25, [[0, 0], [-0.3, -0.3], [0.3, -0.3], [0.3, 0.3], [-0.3, 0.3]]);
    const paso = 0.5;
    let horas = 0;
    for (let t = 4; t < 22; t += paso) {
        const pos = posicionSol(fecha, t + paso / 2);
        if (pos.alt < rad(4)) continue;
        const dir = vectorSol(pos);
        let iluminados = 0;
        for (const p of puntos) {
            raycaster.set(p, dir);
            raycaster.far = 100;
            if (!obstaculos.length || !raycaster.intersectObjects(obstaculos, true).length) iluminados++;
        }
        horas += paso * iluminados / puntos.length;
    }
    cacheSol.set(clave, horas);
    return horas;
}

// Parte del cantero protegida del viento (0 a 1): un reparo protege hasta unas 8 veces su altura
function reparoViento(el) {
    if (estado.terreno.viento === null) return null;
    const obstaculos = mallasObstaculo();
    if (!obstaculos.length) return 0;
    const dir = direccion(estado.terreno.viento);
    const puntos = puntosMuestra(el, 1, [[0, 0], [0, -0.35], [0, 0.35]]);
    let reparados = 0;
    for (const p of puntos) {
        raycaster.set(p, dir);
        raycaster.far = 80;
        const golpe = raycaster.intersectObjects(obstaculos, true)[0];
        if (!golpe) continue;
        let o = golpe.object;
        while (o && !o.userData.clave) o = o.parent;
        const alto = porClave(o?.userData.clave)?.alto ?? 0;
        if (golpe.distance <= 8 * Math.max(0.5, alto - 1)) reparados++;
    }
    return reparados / puntos.length;
}

function aguaSemanal(lista, fecha) {
    const coef = RIEGO.coeficientes[fecha.getMonth()] ?? 1;
    return lista.reduce((total, o) => total + (o.frecuencia > 0 ? o.litros * coef / RIEGO.eficiencia * 7 / o.frecuencia : 0), 0);
}

// Qué tan bien le viene el sol a una especie: devuelve [tipo, texto] o null si está bien
function evaluarSol(especie, nombre, horas, fecha, mediaSombra) {
    if (!especie) return null;
    const exposicion = normalizar(especie.exposicion);
    const h = num(horas);
    const verano = esVerano(fecha);
    if (exposicion.startsWith('sol pleno')) {
        if (horas < 4) return ['grave', `<b>${esc(nombre)}</b> recibe ${h} h de sol y necesita sol pleno (6 h o más).`];
        if (horas < 6) return ['alerta', `<b>${esc(nombre)}</b> recibe ${h} h de sol; con sol pleno (6 h o más) produce mejor.`];
        if (exposicion.includes('media sombra') && verano && horas >= 9 && !mediaSombra) {
            return ['info', `<b>${esc(nombre)}</b>: en pleno verano agradece media sombra en las horas de más calor (recibe ${h} h).`];
        }
        return null;
    }
    if (exposicion.includes('media sombra en verano')) {
        if (verano && horas >= 8 && !mediaSombra) return ['alerta', `<b>${esc(nombre)}</b> recibe ${h} h de sol en verano: ponele media sombra o ubicala donde le dé sombra a la tarde.`];
        if (!verano && horas < 4) return ['alerta', `<b>${esc(nombre)}</b> recibe solo ${h} h de sol; en esta época necesita más.`];
        return null;
    }
    if (exposicion.startsWith('sol o media sombra') && horas < 3) return ['alerta', `<b>${esc(nombre)}</b> recibe solo ${h} h de sol.`];
    if (exposicion.startsWith('sol de invierno') && !verano && horas < 5) return ['alerta', `<b>${esc(nombre)}</b> recibe ${h} h de sol; en invierno necesita el mayor sol posible.`];
    return null;
}

// Plantas de más de un metro: con viento fuerte se quiebran o se vuelcan
const esAlta = o => formaDe(o.nombre, o.especie).alto >= 1;

function analizar() {
    escena.updateMatrixWorld(true);
    const fecha = fechaVista();
    const resultados = new Map();
    const avisos = [];
    const agregar = (tipo, html, clave = null) => avisos.push({ tipo, html, clave });

    plantables().forEach(el => {
        const lista = ocupantes(el, fecha);
        const horas = horasSol(el, fecha);
        const reparo = reparoViento(el);
        const agua = aguaSemanal(lista, fecha);
        resultados.set(el.clave, { horas, reparo, agua, lista });

        lista.forEach(o => {
            const sol = evaluarSol(o.especie, `${o.nombre} (${el.nombre})`, horas, fecha, el.media_sombra);
            if (sol) agregar(sol[0], sol[1], el.clave);

            if (o.especie?.sensible_helada && enPeriodoDeHeladas(fecha) && !TIPOS[el.tipo].protege_helada) {
                agregar('grave', `<b>Helada:</b> ${esc(o.nombre)} en ${esc(el.nombre)} no soporta heladas y para esta fecha todavía puede helar. Cubrilo de noche o pasalo a un túnel.`, el.clave);
            }
            if (reparo !== null && reparo < 0.5 && esAlta(o) && el.tipo !== 'tunel') {
                agregar('alerta', `<b>Viento:</b> ${esc(o.nombre)} en ${esc(el.nombre)} es una planta alta sin reparo. Atala bien al tutor o poné una cortina rompeviento del lado del ${rumboTexto(estado.terreno.viento)}.`, el.clave);
            }
        });

        // Vecinos que no se llevan bien en el mismo cantero
        lista.forEach((a, i) => lista.slice(i + 1).forEach(b => {
            if (!a.especie || !b.especie) return;
            const malos = a.especie.malos_vecinos.includes(b.especie.nombre) || b.especie.malos_vecinos.includes(a.especie.nombre);
            if (malos) agregar('alerta', `<b>Vecinos:</b> ${esc(a.nombre)} y ${esc(b.nombre)} no conviene que compartan ${esc(el.nombre)}.`, el.clave);
        }));

        if (lista.length && horas > 0 && el.media_sombra && !esVerano(fecha) && lista.every(o => normalizar(o.especie?.exposicion).startsWith('sol pleno'))) {
            agregar('info', `${esc(el.nombre)} tiene media sombra y en esta época sus cultivos aprovechan el sol pleno: podés sacarla.`, el.clave);
        }
    });

    // Elementos encimados (salvo macetas dentro de un túnel)
    const extension = e => {
        const giro = rad(e.rotacion);
        return [Math.abs(Math.cos(giro)) * e.ancho + Math.abs(Math.sin(giro)) * e.largo, Math.abs(Math.sin(giro)) * e.ancho + Math.abs(Math.cos(giro)) * e.largo];
    };
    estado.elementos.forEach((a, i) => estado.elementos.slice(i + 1).forEach(b => {
        if ([a.tipo, b.tipo].includes('tunel') && [a.tipo, b.tipo].includes('maceta')) return;
        const [aa, al] = extension(a), [ba, bl] = extension(b);
        if (Math.abs(a.x - b.x) < (aa + ba) / 2 - 0.05 && Math.abs(a.z - b.z) < (al + bl) / 2 - 0.05) {
            agregar('alerta', `${esc(a.nombre)} y ${esc(b.nombre)} están encimados en el plano.`, a.clave);
        }
    }));

    const sinUbicar = CULTIVOS.filter(c => !c.cosechado && !porClave(estado.ubicaciones[c.id]));
    if (sinUbicar.length && plantables().length) {
        agregar('info', `${sinUbicar.length === 1 ? 'Hay 1 cultivo' : `Hay ${sinUbicar.length} cultivos`} sin ubicar en el plano (${esc(listaNombres(sinUbicar.map(c => c.nombre)))}). Ubicalos en la pestaña Cultivos.`);
    }

    // Agua guardada entre turnos de canal
    const aguaTotal = [...resultados.values()].reduce((t, r) => t + r.agua, 0);
    if (RIEGO.usa_turno && RIEGO.entre_turnos > 1 && aguaTotal > 0) {
        const necesaria = aguaTotal / 7 * (RIEGO.entre_turnos - 1);
        if (necesaria > RIEGO.reservorio) {
            agregar('alerta', `<b>Agua entre turnos:</b> para regar lo plantado en esta fecha hacen falta unos ${num(necesaria, 0)} L entre un turno y otro, y podés guardar ${num(RIEGO.reservorio, 0)} L.`);
        }
    }

    estado.resultados = resultados;
    estado.avisos = avisos;
    estado.aguaTotal = aguaTotal;
}

let temporizadorAnalisis = null;
function programarAnalisis() {
    versionGeometria++;
    clearTimeout(temporizadorAnalisis);
    temporizadorAnalisis = setTimeout(() => {
        analizar();
        pintarCapa();
        renderAnalisis();
        actualizarEtiquetas();
    }, 150);
}

// ---------- Capas ----------

function rampa(colores, t) {
    t = limitar(t, 0, 1) * (colores.length - 1);
    const i = Math.min(colores.length - 2, Math.floor(t));
    return new THREE.Color(colores[i]).lerp(new THREE.Color(colores[i + 1]), t - i);
}
const RAMPA_SOL = ['#36507f', '#6fa8d6', '#f4d35e', '#f59e3b', '#d6452b'];
const RAMPA_AGUA = ['#e3f0fb', '#7fb6e3', '#1769aa', '#0b3d66'];
const RAMPA_VIENTO = ['#e0613a', '#f4c542', '#4caf50'];

let flechasViento = null;

function pintarCapa() {
    const maxAgua = Math.max(1, ...[...estado.resultados.values()].map(r => r.agua));
    plantables().forEach(el => {
        const obj = objetos.get(el.clave);
        const r = estado.resultados.get(el.clave);
        if (!obj?.suelo) return;
        let color = obj.sueloColor;
        if (r && estado.capa === 'sol') color = rampa(RAMPA_SOL, r.horas / 14);
        if (r && estado.capa === 'agua') color = r.agua > 0 ? rampa(RAMPA_AGUA, r.agua / maxAgua) : new THREE.Color('#d9d4c7');
        if (r && estado.capa === 'viento' && r.reparo !== null) color = rampa(RAMPA_VIENTO, r.reparo);
        obj.suelo.material.color.copy(color);
    });

    if (flechasViento) {
        grupoCapa.remove(flechasViento);
        flechasViento.traverse(o => o.geometry?.dispose());
        flechasViento = null;
    }
    if (estado.capa === 'viento' && estado.terreno.viento !== null) {
        flechasViento = new THREE.Group();
        // Punta hacia +y: al acostarla queda apuntando hacia arriba del plano
        const forma = new THREE.Shape([[-0.25, -0.9], [0.25, -0.9], [0.25, 0.1], [0.6, 0.1], [0, 1], [-0.6, 0.1], [-0.25, 0.1]].map(([x, y]) => new THREE.Vector2(x, y)));
        const material = new THREE.MeshBasicMaterial({ color: '#2b7bbd', transparent: true, opacity: 0.55, side: THREE.DoubleSide, depthWrite: false });
        const lateral = direccion(estado.terreno.viento + 90);
        const separacion = Math.max(estado.terreno.ancho, estado.terreno.largo) / 3;
        [-1, 0, 1].forEach(i => {
            const flecha = new THREE.Mesh(new THREE.ShapeGeometry(forma), material);
            flecha.rotation.x = -Math.PI / 2;
            flecha.scale.setScalar(1.3);
            const contenedorFlecha = new THREE.Group();
            contenedorFlecha.add(flecha);
            // La flecha apunta hacia donde va el viento
            contenedorFlecha.rotation.y = -rad(estado.terreno.norte + estado.terreno.viento + 180);
            contenedorFlecha.position.set(lateral.x * separacion * i, 2.6, lateral.z * separacion * i);
            flechasViento.add(contenedorFlecha);
        });
        flechasViento.traverse(o => { o.raycast = () => {}; });
        grupoCapa.add(flechasViento);
    }
    renderLeyenda();
}

function actualizarEtiquetas() {
    const hoyVisible = estado.dia === 0;
    estado.elementos.forEach(el => {
        const obj = objetos.get(el.clave);
        if (!obj) return;
        const r = estado.resultados.get(el.clave);
        let extra = '';
        if (r && estado.capa === 'sol') extra = `☀️ ${num(r.horas)} h`;
        else if (r && estado.capa === 'agua') extra = `💧 ${num(r.agua, 0)} L/sem`;
        else if (r && estado.capa === 'viento' && r.reparo !== null) extra = r.reparo >= 0.67 ? '🛡️ Reparado' : r.reparo > 0 ? '〰️ Parcial' : '💨 Expuesto';
        else if (r && estado.capa === 'normal') {
            const iconos = [];
            if (hoyVisible) {
                if (r.lista.some(o => o.origen === 'cultivo' && o.ref.regar)) iconos.push('<span title="Regar hoy">💧</span>');
                if (r.lista.some(o => o.origen === 'cultivo' && o.ref.cosechar)) iconos.push('<span title="Para cosechar">🧺</span>');
                if (r.lista.some(o => o.origen === 'cultivo' && o.ref.problemas > 0)) iconos.push('<span title="Problema sin resolver">🐛</span>');
            }
            if (r.lista.some(o => o.origen === 'plan')) iconos.push('<span title="Siembra planificada">🗓️</span>');
            extra = iconos.length ? `<span class="avisos">${iconos.join('')}</span>` : '';
        } else if (el.tipo === 'tanque' && RIEGO.reservorio > 0) {
            extra = `${num(RIEGO.reservorio, 0)} L`;
        }
        obj.div.innerHTML = `${esc(el.nombre)}${extra ? ' · ' + extra : ''}`;
    });
}

function renderLeyenda() {
    const leyenda = $('#leyenda');
    const fecha = fechaCorta(fechaVista());
    const degradado = colores => `linear-gradient(90deg, ${colores.join(', ')})`;
    if (estado.capa === 'sol') {
        leyenda.innerHTML = `Horas de sol directo el ${fecha}<div class="escala" style="background:${degradado(RAMPA_SOL)}"></div><div class="d-flex justify-content-between"><span>0 h</span><span>7 h</span><span>14 h</span></div>`;
    } else if (estado.capa === 'agua') {
        leyenda.innerHTML = `Litros por semana (${MESES[fechaVista().getMonth()]}, ${esc(RIEGO.metodo.toLowerCase())})<div class="escala" style="background:${degradado(RAMPA_AGUA)}"></div><div class="d-flex justify-content-between"><span>poco</span><span>mucho</span></div>`;
    } else if (estado.capa === 'viento') {
        leyenda.innerHTML = estado.terreno.viento === null
            ? 'Elegí de dónde viene el viento fuerte en la pestaña <b>Terreno</b>.'
            : `Viento del ${rumboTexto(estado.terreno.viento)}<div class="escala" style="background:${degradado(RAMPA_VIENTO)}"></div><div class="d-flex justify-content-between"><span>expuesto</span><span>reparado</span></div>`;
    } else {
        leyenda.innerHTML = `Cultivos al ${fecha} · 💧 regar · 🧺 cosechar · 🐛 problema · 🗓️ planificado`;
    }
}

// ---------- Sol en la escena ----------

function actualizarSol() {
    const fecha = fechaVista();
    const pos = posicionSol(fecha, estado.hora);
    const tam = Math.max(estado.terreno.ancho, estado.terreno.largo) * 0.8 + 6;
    Object.assign(sol.shadow.camera, { left: -tam, right: tam, top: tam, bottom: -tam, near: 1, far: 200 });
    sol.shadow.camera.updateProjectionMatrix();

    const altura = Math.sin(Math.max(pos.alt, 0));
    if (pos.alt > 0) {
        sol.position.copy(vectorSol(pos).multiplyScalar(80));
        sol.intensity = 0.6 + 2.4 * Math.pow(altura, 0.5);
        sol.color.set(pos.alt < rad(12) ? '#ffc58a' : '#fff3dc');
    } else {
        sol.intensity = 0;
    }
    cielo.intensity = 0.35 + 0.85 * Math.pow(altura, 0.4);
    const fondo = pos.alt > rad(8) ? new THREE.Color('#cfe3ee') : pos.alt > 0 ? new THREE.Color('#f2c9a0') : new THREE.Color('#33415c');
    escena.background.copy(fondo);
    escena.fog.color.copy(fondo);

    const luz = horasDeLuz(fecha);
    const grados = Math.round(pos.alt * 180 / Math.PI);
    $('#infoSol').innerHTML = pos.alt > 0
        ? `☀️ ${horaTexto(estado.hora)} · ${grados}° de altura · desde el ${rumboTexto(pos.az * 180 / Math.PI)}<br>Luz de ${horaTexto(luz.salida ?? 0)} a ${horaTexto(luz.puesta ?? 0)}`
        : `🌙 ${horaTexto(estado.hora)} · de noche<br>Luz de ${horaTexto(luz.salida ?? 0)} a ${horaTexto(luz.puesta ?? 0)}`;
}

// ---------- Vista y cámara ----------

function centrarVista() {
    const { ancho, largo } = estado.terreno;
    const r = Math.max(ancho, largo);
    camara.position.set(r * 0.55, r * 0.8, r * 1.05);
    controles.target.set(0, 0, 0);
    controles.update();
    camaraPlanta.position.set(0, 100, 0.001);
    camaraPlanta.zoom = 1;
    controlesPlanta.target.set(0, 0, 0);
    ajustarTamano();
    controlesPlanta.update();
}

function ajustarTamano() {
    const ancho = contenedor.clientWidth, alto = contenedor.clientHeight;
    renderer.setSize(ancho, alto);
    etiquetas.setSize(ancho, alto);
    camara.aspect = ancho / alto;
    camara.updateProjectionMatrix();
    const mitad = Math.max(estado.terreno.largo, estado.terreno.ancho * alto / ancho) / 2 + 1.5;
    Object.assign(camaraPlanta, { left: -mitad * ancho / alto, right: mitad * ancho / alto, top: mitad, bottom: -mitad });
    camaraPlanta.updateProjectionMatrix();
}
new ResizeObserver(ajustarTamano).observe(contenedor);

function cambiarVista(vista) {
    estado.vista = vista;
    controles.enabled = vista === '3d';
    controlesPlanta.enabled = vista === 'planta';
    document.querySelectorAll('[data-vista]').forEach(b => b.classList.toggle('activo', b.dataset.vista === vista));
}

// ---------- Mover elementos con el mouse ----------

const puntero = new THREE.Vector2();
const planoSuelo = new THREE.Plane(new THREE.Vector3(0, 1, 0), 0);
let arrastre = null;
let inicioClic = null;

function rayoDesde(evento) {
    const r = renderer.domElement.getBoundingClientRect();
    puntero.set((evento.clientX - r.left) / r.width * 2 - 1, -(evento.clientY - r.top) / r.height * 2 + 1);
    raycaster.setFromCamera(puntero, camaraActiva());
    raycaster.far = Infinity;
}

function elementoBajo(evento) {
    rayoDesde(evento);
    const golpe = raycaster.intersectObjects(grupoElementos.children, true)[0];
    let o = golpe?.object;
    while (o && !o.userData.clave) o = o.parent;
    return o ? o.userData.clave : null;
}

function puntoSuelo(evento) {
    rayoDesde(evento);
    return raycaster.ray.intersectPlane(planoSuelo, new THREE.Vector3());
}

renderer.domElement.addEventListener('pointerdown', evento => {
    if (evento.button !== 0) return;
    inicioClic = { x: evento.clientX, y: evento.clientY };
    const clave = elementoBajo(evento);
    if (!clave) return;
    if (estado.seleccion !== clave) seleccionar(clave);
    const el = porClave(clave);
    const p = puntoSuelo(evento);
    if (!p) return;
    arrastre = { clave, dx: el.x - estado.terreno.ancho / 2 - p.x, dz: el.z - estado.terreno.largo / 2 - p.z, movido: false };
    controlesActivos().enabled = false;
    renderer.domElement.setPointerCapture(evento.pointerId);
}, { capture: true });

renderer.domElement.addEventListener('pointermove', evento => {
    if (!arrastre) {
        if (evento.buttons === 0) renderer.domElement.style.cursor = elementoBajo(evento) ? 'grab' : '';
        return;
    }
    const p = puntoSuelo(evento);
    if (!p) return;
    const el = porClave(arrastre.clave);
    const paso = $('#btnGrilla').classList.contains('activo') ? 0.1 : 0.05;
    const redondear = v => Math.round(v / paso) * paso;
    el.x = limitar(redondear(p.x + arrastre.dx + estado.terreno.ancho / 2), 0, estado.terreno.ancho);
    el.z = limitar(redondear(p.z + arrastre.dz + estado.terreno.largo / 2), 0, estado.terreno.largo);
    arrastre.movido = true;
    contenedor.classList.add('moviendo');
    posicionar(el);
});

renderer.domElement.addEventListener('pointerup', evento => {
    if (arrastre) {
        if (arrastre.movido) {
            marcarCambios();
            renderSeleccion();
        }
        arrastre = null;
        contenedor.classList.remove('moviendo');
        controlesActivos().enabled = true;
        return;
    }
    // Clic en un lugar vacío (sin arrastrar la cámara): se suelta la selección
    if (inicioClic && Math.hypot(evento.clientX - inicioClic.x, evento.clientY - inicioClic.y) < 4 && !elementoBajo(evento)) {
        seleccionar(null);
    }
});

document.addEventListener('keydown', evento => {
    if (['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
    const el = porClave(estado.seleccion);
    if (!el) return;
    if (evento.key === 'r' || evento.key === 'R') {
        el.rotacion = ((el.rotacion + (evento.shiftKey ? -15 : 15)) % 360 + 360) % 360;
        posicionar(el);
    } else if (evento.key === 'Delete' || evento.key === 'Supr') {
        eliminarElemento(el.clave);
        return;
    } else if (evento.key === 'Escape') {
        seleccionar(null);
        return;
    } else if (evento.key.startsWith('Arrow')) {
        const d = evento.shiftKey ? 0.5 : 0.1;
        if (evento.key === 'ArrowLeft') el.x = limitar(el.x - d, 0, estado.terreno.ancho);
        if (evento.key === 'ArrowRight') el.x = limitar(el.x + d, 0, estado.terreno.ancho);
        if (evento.key === 'ArrowUp') el.z = limitar(el.z - d, 0, estado.terreno.largo);
        if (evento.key === 'ArrowDown') el.z = limitar(el.z + d, 0, estado.terreno.largo);
        posicionar(el);
    } else {
        return;
    }
    evento.preventDefault();
    marcarCambios();
    renderSeleccion();
});

// ---------- Panel: Diseño ----------

function renderPaleta() {
    $('#paleta').innerHTML = Object.entries(TIPOS).map(([tipo, t]) =>
        `<button type="button" data-agregar="${tipo}" title="Agregar ${esc(t.nombre.toLowerCase())}"><i class="bi ${t.icono}"></i>${esc(t.nombre)}</button>`).join('');
}

$('#paleta').addEventListener('click', evento => {
    const boton = evento.target.closest('[data-agregar]');
    if (!boton) return;
    const el = agregarElemento(boton.dataset.agregar);
    seleccionar(el.clave);
    marcarCambios();
    renderTodo();
});

function renderSeleccion() {
    const caja = $('#seleccion');
    const el = porClave(estado.seleccion);
    if (!el) {
        const superficie = plantables().reduce((t, e) => t + area(e), 0);
        caja.innerHTML = `<p class="small text-muted mb-0">Hacé clic en un elemento del plano para editarlo.${estado.elementos.length ? ` Tenés ${estado.elementos.length} elemento(s) y ${num(superficie)} m² para cultivar.` : ''}</p>`;
        renderPlantillas();
        return;
    }
    $('#plantillas').innerHTML = '';
    const t = TIPOS[el.tipo];
    const campoNum = (campo, etiqueta, min, max, paso) => `
        <div class="col-4">
            <label class="form-label">${etiqueta}</label>
            <input type="number" class="form-control" data-campo="${campo}" value="${+el[campo].toFixed(2)}" min="${min}" max="${max}" step="${paso}">
        </div>`;

    let ocupacion = '';
    if (t.plantable) {
        const lista = ocupantes(el, fechaVista());
        ocupacion = `
            <div class="subtitulo">Plantado al ${fechaCorta(fechaVista())}</div>
            ${lista.length ? `<ul class="lista-plano">${lista.map(o => `
                <li><span class="emoji">${emojiDe(o.nombre)}</span>
                    <div class="flex-grow-1"><div class="fw-semibold">${esc(o.nombre)}${o.origen === 'plan' ? ' <span class="badge text-bg-light">planificado</span>' : ''}</div>
                    <div class="detalle">${Math.round(limitar(o.progreso, 0, 1) * 100)} % del ciclo · cosecha ${fechaCorta(o.fin)}</div></div>
                </li>`).join('')}</ul>` : '<p class="small text-muted mb-2">Vacío en esta fecha.</p>'}
            <div class="d-flex flex-wrap gap-2 mt-2">
                <button type="button" class="btn btn-light btn-sm" data-accion="planificar"><i class="bi bi-calendar2-plus me-1"></i>Planificar</button>
                ${el.id ? `<a class="btn btn-light btn-sm" href="${D.urls.nuevo.replace('{id}', el.id)}"><i class="bi bi-plus-lg me-1"></i>Cargar cultivo acá</a>` : ''}
            </div>`;
    }

    caja.innerHTML = `
        <div class="tarjeta-sel">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi ${t.icono} text-success"></i>
                <span class="small text-muted">${esc(t.nombre)}${t.plantable ? ` · ${num(area(el))} m²` : ''}</span>
            </div>
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" data-campo="nombre" value="${esc(el.nombre)}" maxlength="60">
                </div>
                ${campoNum('ancho', 'Ancho (m)', 0.2, 50, 0.1)}
                ${campoNum('largo', 'Largo (m)', 0.2, 100, 0.1)}
                ${campoNum('alto', 'Alto (m)', 0.02, 30, 0.05)}
                ${campoNum('x', 'X (m)', 0, estado.terreno.ancho, 0.1)}
                ${campoNum('z', 'Y (m)', 0, estado.terreno.largo, 0.1)}
                ${campoNum('rotacion', 'Giro (°)', 0, 359, 15)}
                ${t.plantable && el.tipo !== 'tunel' ? `
                <div class="col-12">
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" id="chkMediaSombra" data-campo="media_sombra" ${el.media_sombra ? 'checked' : ''}>
                        <label class="form-check-label small" for="chkMediaSombra">Con media sombra</label>
                    </div>
                </div>` : ''}
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-light btn-sm" data-accion="girar" title="Girar 90°"><i class="bi bi-arrow-clockwise"></i></button>
                <button type="button" class="btn btn-light btn-sm" data-accion="duplicar"><i class="bi bi-copy me-1"></i>Duplicar</button>
                <button type="button" class="btn btn-light btn-sm text-danger ms-auto" data-accion="eliminar"><i class="bi bi-trash me-1"></i>Borrar</button>
            </div>
            ${ocupacion}
        </div>`;
}

$('#seleccion').addEventListener('input', evento => {
    const campo = evento.target.dataset.campo;
    const el = porClave(estado.seleccion);
    if (!campo || !el) return;
    if (campo === 'nombre') {
        el.nombre = evento.target.value;
        actualizarEtiquetas();
        renderCultivos();
        renderPlanificar();
    } else if (campo === 'media_sombra') {
        el.media_sombra = evento.target.checked;
        reconstruirElemento(el);
    } else {
        const valor = parseFloat(evento.target.value);
        if (Number.isNaN(valor)) return;
        const limites = { ancho: [0.2, 50], largo: [0.2, 100], alto: [0.02, 30], x: [0, estado.terreno.ancho], z: [0, estado.terreno.largo], rotacion: [-360, 720] };
        el[campo] = limitar(valor, ...limites[campo]);
        if (campo === 'rotacion') el.rotacion = ((el.rotacion % 360) + 360) % 360;
        ['x', 'z', 'rotacion'].includes(campo) ? posicionar(el) : reconstruirElemento(el);
    }
    marcarCambios();
});

$('#seleccion').addEventListener('click', evento => {
    const accion = evento.target.closest('[data-accion]')?.dataset.accion;
    const el = porClave(estado.seleccion);
    if (!accion || !el) return;
    if (accion === 'girar') {
        el.rotacion = (el.rotacion + 90) % 360;
        posicionar(el);
        renderSeleccion();
        marcarCambios();
    } else if (accion === 'duplicar') {
        const { clave, id, ...datos } = el;
        const copia = agregarElemento(el.tipo, { ...datos, nombre: el.nombre + ' (copia)', ...lugarLibre(el.ancho, el.largo) });
        seleccionar(copia.clave);
        marcarCambios();
        renderTodo();
    } else if (accion === 'eliminar') {
        eliminarElemento(el.clave);
    } else if (accion === 'planificar') {
        estado.planCantero = el.clave;
        abrirPestana('planificar');
        renderPlanificar();
    }
});

// Diseños de partida para un plano vacío
function renderPlantillas() {
    $('#plantillas').innerHTML = estado.elementos.length ? '' : `
        <div class="subtitulo">Empezar con un diseño</div>
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-light btn-sm text-start" data-plantilla="familiar"><i class="bi bi-house-heart me-2 text-success"></i>Huerta familiar: 3 canteros, tanque y compostera</button>
            <button type="button" class="btn btn-light btn-sm text-start" data-plantilla="comunitaria"><i class="bi bi-people me-2 text-success"></i>Huerta comunitaria: tablones, túnel, cortina y acequia</button>
        </div>`;
}

$('#plantillas').addEventListener('click', evento => {
    const plantilla = evento.target.closest('[data-plantilla]')?.dataset.plantilla;
    if (!plantilla) return;
    const T = estado.terreno;

    if (plantilla === 'familiar') {
        Object.assign(T, { ancho: 8, largo: 6 });
        construirTerreno();
        [1.5, 3.5, 5.5].forEach((x, i) => agregarElemento('cantero', { nombre: `Cantero ${i + 1}`, x, z: 3 }));
        agregarElemento('tanque', { x: 7.3, z: 1.2 });
        agregarElemento('compostera', { x: 7.3, z: 4.8 });
    } else {
        Object.assign(T, { ancho: 14, largo: 12 });
        construirTerreno();
        // La cortina va del lado de donde viene el viento (si no se eligió, del oeste)
        const viento = direccion(T.viento ?? 270);
        const lados = [
            { normal: [0, -1], datos: { x: T.ancho / 2, z: 0.4, largo: T.ancho - 1, rotacion: 90 } },
            { normal: [1, 0], datos: { x: T.ancho - 0.4, z: T.largo / 2, largo: T.largo - 1 } },
            { normal: [0, 1], datos: { x: T.ancho / 2, z: T.largo - 0.4, largo: T.ancho - 1, rotacion: 90 } },
            { normal: [-1, 0], datos: { x: 0.4, z: T.largo / 2, largo: T.largo - 1 } },
        ];
        const lado = lados.reduce((mejor, l) => (l.normal[0] * viento.x + l.normal[1] * viento.z) > (mejor.normal[0] * viento.x + mejor.normal[1] * viento.z) ? l : mejor);
        agregarElemento('cortina', { nombre: 'Cortina de álamos', ...lado.datos });

        for (let i = 0; i < 6; i++) {
            agregarElemento('tablon', { nombre: `Tablón ${i + 1}`, x: 2.5 + (i % 3) * 2, z: i < 3 ? 3.5 : 8.5 });
        }
        agregarElemento('tunel', { nombre: 'Túnel', x: 10, z: 6 });
        agregarElemento('acequia', { x: T.ancho / 2, z: T.largo - 1.2, largo: T.ancho - 2.5, rotacion: 90 });
        agregarElemento('tanque', { x: 12.5, z: 2 });
        agregarElemento('compostera', { x: 12.5, z: 9.5 });
        agregarElemento('camino', { x: 4.5, z: 6, largo: 6.5, rotacion: 90 });
    }
    rellenarTerreno();
    centrarVista();
    marcarCambios();
    renderTodo();
});

// ---------- Panel: Cultivos ----------

function opcionesCanteros(seleccionado) {
    return `<option value="">Sin ubicar</option>` + plantables().map(e =>
        `<option value="${e.clave}" ${e.clave === seleccionado ? 'selected' : ''}>${esc(e.nombre)}</option>`).join('');
}

function renderCultivos() {
    const activos = CULTIVOS.filter(c => !c.cosechado);
    const lista = $('#listaCultivos');
    if (!activos.length) {
        lista.innerHTML = `<li class="text-muted">No hay cultivos en curso. <a href="${D.urls.nuevo.replace('?cantero={id}', '')}">Cargá uno</a>.</li>`;
        return;
    }
    if (!plantables().length) {
        lista.innerHTML = '<li class="text-muted">Primero agregá un cantero, tablón o maceta en la pestaña Diseño.</li>';
        return;
    }
    lista.innerHTML = activos.map(c => {
        const dias = diasEntre(c.inicio, hoy);
        const acciones = [
            c.regar ? `<a href="${D.urls.regar.replace('{id}', c.id)}" class="badge text-bg-primary text-decoration-none">💧 Regar</a>` : '',
            c.cosechar ? `<a href="${D.urls.cosechar.replace('{id}', c.id)}" class="badge text-bg-warning text-decoration-none">🧺 Cosechar</a>` : '',
            c.problemas ? `<span class="badge text-bg-danger">🐛 ${c.problemas}</span>` : '',
        ].join(' ');
        return `
            <li>
                <span class="emoji">${emojiDe(c.nombre)}</span>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold text-truncate">${esc(c.nombre)}${c.variedad ? ` <span class="text-muted fw-normal">${esc(c.variedad)}</span>` : ''}</div>
                    <div class="detalle">Día ${Math.max(0, dias)} de ${c.dias_cosecha} ${acciones}</div>
                </div>
                <select class="form-select" data-cultivo="${c.id}" aria-label="Cantero de ${esc(c.nombre)}">${opcionesCanteros(estado.ubicaciones[c.id])}</select>
            </li>`;
    }).join('');
}

$('#listaCultivos').addEventListener('change', evento => {
    const id = evento.target.dataset.cultivo;
    if (!id) return;
    const anterior = estado.ubicaciones[id];
    estado.ubicaciones[id] = evento.target.value || null;
    [anterior, estado.ubicaciones[id]].forEach(clave => { const el = porClave(clave); if (el) construirPlantas(el); });
    marcarCambios();
    renderPlanificar();
});

// ---------- Panel: Planificar ----------

const inicioCalendario = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
const finCalendario = new Date(hoy.getFullYear() + 1, hoy.getMonth(), 1);
const posicionCalendario = f => limitar((f - inicioCalendario) / (finCalendario - inicioCalendario) * 100, 0, 100);

function capacidad(el, especie, compartido) {
    const distancia = (especie?.distancia_cm || 30) / 100;
    return Math.max(1, Math.floor(area(el) / compartido / (distancia * distancia)));
}

// Siguiente fecha en la que la especie se puede sembrar en ese cantero
function proximaFechaBuena(especie, el, desde) {
    const meses = mesesPermitidos(especie, el);
    for (let i = 0; i < 13; i++) {
        const f = i === 0 ? new Date(desde) : new Date(desde.getFullYear(), desde.getMonth() + i, 1);
        if (meses.includes(f.getMonth() + 1)) return f;
    }
    return null;
}

// Revisión de una siembra planificada: temporada, heladas, rotación, vecinos, espacio y sol
function evaluarPlan(el, datos) {
    const avisos = [];
    const agregar = (tipo, html) => avisos.push({ tipo, html });
    const especie = identificar(datos.especie);
    if (!datos.especie.trim()) return avisos;
    if (!especie) {
        agregar('info', `No tenemos «${esc(datos.especie)}» en el catálogo: se planifica igual, pero sin revisión.`);
        return avisos;
    }
    if (!datos.fecha) return avisos;

    const inicio = fechaLocal(datos.fecha);
    const fin = sumarDias(inicio, datos.dias || especie.dias_cosecha);
    const mes = inicio.getMonth() + 1;
    const meses = mesesPermitidos(especie, el);
    const protegido = TIPOS[el.tipo].protege_helada;

    if (meses.includes(mes)) {
        agregar('ok', `Buena época: ${esc(especie.nombre)} se siembra en ${nombresMeses(meses)}${protegido && especie.sensible_helada ? ' (en túnel se puede adelantar)' : ''}.`);
    } else {
        const proxima = proximaFechaBuena(especie, el, inicio);
        agregar('alerta', `Fuera de época: ${esc(especie.nombre)} se siembra en ${nombresMeses(meses)}.${proxima ? ` <a href="#" data-usar-fecha="${isoDe(proxima)}">Usar ${fechaCorta(proxima)}</a>` : ''}`);
    }

    if (especie.meses_almacigo?.length && normalizar(especie.tipo_siembra).startsWith('almacigo')) {
        const almacigo = sumarDias(inicio, -45);
        agregar('info', `Va con almácigo: sembralo en bandeja cerca del ${fechaCorta(almacigo)} para trasplantar en esta fecha.`);
    }

    if (ZONA && especie.sensible_helada && !protegido) {
        if (enPeriodoDeHeladas(inicio)) agregar('grave', `Riesgo de helada: en ${esc(ZONA.nombre)} la última helada suele ser a mediados de ${MESES[Number(ZONA.ultima_helada.slice(0, 2)) - 1]}. Sembrá más tarde o en un túnel.`);
        else if (fin > proximaPrimeraHelada(inicio)) agregar('alerta', `La cosecha (${fechaCorta(fin)}) cae después de la primera helada de la zona. Sembrá antes o usá una variedad más corta.`);
    }

    // Rotación: lo último que hubo en el cantero antes de esta siembra
    const otros = historial(el).filter(o => !(o.origen === 'plan' && o.ref.id === estado.planEditando));
    const anterior = otros.filter(o => o.inicio < inicio).sort((a, b) => b.fin - a.fin)[0];
    if (anterior?.especie && anterior.especie.familia === especie.familia) {
        agregar('alerta', `Rotación: antes hay ${esc(anterior.nombre)}, de la misma familia (${esc(especie.familia)}). Conviene alternar familias para cortar plagas y no cansar el suelo.`);
    } else if (anterior?.especie) {
        agregar('ok', `Rotación: viene después de ${esc(anterior.nombre)} (${esc(anterior.especie.familia)}), otra familia.`);
    }
    if (normalizar(especie.familia).startsWith('fabaceas')) agregar('info', 'Las legumbres dejan nitrógeno en el suelo: lo aprovecha el cultivo que venga después.');

    // Vecinos y espacio con lo que comparte el cantero en esas fechas
    const simultaneos = otros.filter(o => o.inicio < fin && o.fin > inicio);
    simultaneos.forEach(o => {
        if (!o.especie) return;
        if (especie.malos_vecinos.includes(o.especie.nombre) || o.especie.malos_vecinos.includes(especie.nombre)) {
            agregar('grave', `${esc(o.nombre)} está en el cantero en esas fechas y no es buen vecino de ${esc(especie.nombre)}.`);
        } else if (especie.buenos_vecinos.includes(o.especie.nombre)) {
            agregar('ok', `Buen vecino: ${esc(o.nombre)} comparte el cantero.`);
        }
    });
    const entran = capacidad(el, especie, simultaneos.length + 1);
    if (datos.plantas > entran) {
        agregar('alerta', `Espacio: ${simultaneos.length ? `compartiendo el cantero con ${simultaneos.length} cultivo(s), ` : ''}entran unas ${entran} plantas a ${especie.distancia_cm} cm.`);
    } else {
        agregar('info', `Espacio: entran unas ${entran} plantas a ${especie.distancia_cm} cm${simultaneos.length ? ' en la parte libre' : ''}.`);
    }

    escena.updateMatrixWorld(true);
    const mitad = sumarDias(inicio, (datos.dias || especie.dias_cosecha) / 2);
    const sol = evaluarSol(especie, especie.nombre, horasSol(el, mitad), mitad, el.media_sombra);
    if (sol) agregar(sol[0], sol[1] + ` <span class="opacity-75">(a mitad del ciclo)</span>`);
    else agregar('ok', `Sol: el cantero recibe ${num(horasSol(el, mitad))} h a mitad del ciclo, le alcanza.`);

    return avisos;
}

// Especies que conviene sembrar en un cantero a partir de una fecha
function sugerencias(el, fecha) {
    const otros = historial(el);
    const anterior = otros.filter(o => o.inicio <= fecha).sort((a, b) => b.fin - a.fin)[0];
    escena.updateMatrixWorld(true);
    return ESPECIES.map(especie => {
        if (!mesesPermitidos(especie, el).includes(fecha.getMonth() + 1)) return null;
        const fin = sumarDias(fecha, especie.dias_cosecha);
        let puntos = 0;
        const motivos = [];
        if (anterior?.especie?.familia === especie.familia) puntos -= 6;
        else if (anterior?.especie) { puntos += 2; motivos.push('rota bien'); }
        const simultaneos = otros.filter(o => o.inicio < fin && o.fin > fecha && o.especie);
        if (simultaneos.some(o => especie.malos_vecinos.includes(o.especie.nombre) || o.especie.malos_vecinos.includes(especie.nombre))) return null;
        if (simultaneos.some(o => especie.buenos_vecinos.includes(o.especie.nombre))) { puntos += 3; motivos.push('buen vecino'); }
        if (ZONA && especie.sensible_helada && !TIPOS[el.tipo].protege_helada && (enPeriodoDeHeladas(fecha) || fin > proximaPrimeraHelada(fecha))) puntos -= 4;
        const mitad = sumarDias(fecha, especie.dias_cosecha / 2);
        const sol = evaluarSol(especie, especie.nombre, horasSol(el, mitad), mitad, el.media_sombra);
        if (sol && sol[0] !== 'info') puntos -= sol[0] === 'grave' ? 6 : 3;
        else motivos.push('buen sol');
        if (normalizar(especie.familia).startsWith('fabaceas')) { puntos += 1; motivos.push('aporta nitrógeno'); }
        return puntos >= 0 ? { especie, puntos, motivos } : null;
    }).filter(Boolean).sort((a, b) => b.puntos - a.puntos).slice(0, 8);
}

function datosFormPlan() {
    const form = $('#formPlan');
    if (!form) return null;
    return {
        especie: form.especie.value,
        variedad: form.variedad.value,
        fecha: form.fecha.value,
        dias: Number(form.dias_cosecha.value) || 0,
        plantas: Number(form.plantas.value) || 0,
        notas: form.notas.value,
    };
}

function renderAvisosPlan() {
    const el = porClave(estado.planCantero);
    const datos = datosFormPlan();
    if (!el || !datos) return;
    $('#avisosPlan').innerHTML = evaluarPlan(el, datos).map(a => aviso(a.tipo, a.html)).join('');
}

const ICONOS_AVISO = { ok: 'bi-check-circle-fill', info: 'bi-info-circle-fill', alerta: 'bi-exclamation-triangle-fill', grave: 'bi-x-octagon-fill' };
const aviso = (tipo, html, clave = null) => `<div class="aviso ${tipo}" ${clave ? `data-ir="${clave}" role="button"` : ''}><i class="bi ${ICONOS_AVISO[tipo]}"></i><div>${html}</div></div>`;

function renderPlanificar() {
    const caja = $('#planificar');
    const lista = plantables();
    if (!lista.length) {
        caja.innerHTML = '<p class="small text-muted">Primero agregá un cantero, tablón, maceta o túnel en la pestaña Diseño.</p>' + renderPendientes();
        return;
    }
    if (!porClave(estado.planCantero)) estado.planCantero = lista[0].clave;
    const el = porClave(estado.planCantero);
    const editando = estado.planificadas.find(p => p.id === estado.planEditando);

    // Calendario de 12 meses del cantero
    const filas = historial(el).filter(o => o.fin > inicioCalendario && o.inicio < finCalendario);
    const meses = Array.from({ length: 12 }, (_, i) => MESES_CORTOS[(inicioCalendario.getMonth() + i) % 12]);
    const gantt = `
        <div class="gantt">
            <div class="gantt-meses">${meses.map(m => `<span>${m}</span>`).join('')}</div>
            ${filas.length ? filas.map(o => {
                const izq = posicionCalendario(o.inicio), der = posicionCalendario(o.fin);
                return `<div class="gantt-fila" title="${esc(o.nombre)}: ${fechaCorta(o.inicio)} a ${fechaCorta(o.fin)}">
                    <div class="gantt-barra ${o.origen === 'plan' ? 'plan' : 'actual'}" style="left:${izq}%;width:${Math.max(1, der - izq)}%">${emojiDe(o.nombre)} ${esc(o.nombre)}</div>
                    <div class="gantt-hoy" style="left:${posicionCalendario(hoy)}%"></div></div>`;
            }).join('') : `<div class="gantt-fila"><div class="gantt-hoy" style="left:${posicionCalendario(hoy)}%"></div></div><p class="small text-muted mb-0">Sin cultivos ni siembras en los próximos 12 meses.</p>`}
        </div>`;

    // Familias que pasaron por el cantero (lo más reciente primero)
    const familias = [...new Set(historial(el).filter(o => o.inicio <= hoy && o.especie).sort((a, b) => b.inicio - a.inicio).map(o => o.especie.familia))].slice(0, 3);

    const fechaSugerida = editando ? editando.fecha : isoDe(sumarDias(hoy, 1));
    const ideas = sugerencias(el, fechaLocal(fechaSugerida));

    caja.innerHTML = `
        <label class="form-label small">Cantero</label>
        <select class="form-select mb-3" id="planCantero">${plantables().map(e => `<option value="${e.clave}" ${e.clave === el.clave ? 'selected' : ''}>${esc(e.nombre)} · ${num(area(e))} m²</option>`).join('')}</select>

        <div class="subtitulo">Próximos 12 meses</div>
        ${gantt}
        ${familias.length ? `<p class="small text-muted mt-2 mb-0"><i class="bi bi-arrow-repeat me-1"></i>Últimas familias: ${esc(familias.join(', '))}</p>` : ''}

        <div class="subtitulo">${editando ? 'Modificar siembra' : 'Planificar una siembra'}</div>
        ${ideas.length ? `<div class="small text-muted mb-1">Ideas para este cantero:</div><div class="d-flex flex-wrap gap-1 mb-2" id="ideas">${ideas.map(i =>
            `<button type="button" class="chip border-0" data-idea="${esc(i.especie.nombre)}" title="${esc(i.motivos.join(', '))}">${emojiDe(i.especie.nombre)} ${esc(i.especie.nombre)}</button>`).join('')}</div>` : ''}
        <form id="formPlan" class="row g-2" autocomplete="off">
            <div class="col-7"><label class="form-label small">Especie</label><input class="form-control" name="especie" list="listaEspeciesPlan" required value="${esc(editando?.texto ?? '')}"></div>
            <div class="col-5"><label class="form-label small">Variedad</label><input class="form-control" name="variedad" value="${esc(editando?.variedad ?? '')}"></div>
            <div class="col-7"><label class="form-label small">Fecha de siembra</label><input type="date" class="form-control" name="fecha" required value="${fechaSugerida}"></div>
            <div class="col-5"><label class="form-label small">Días a cosecha</label><input type="number" class="form-control" name="dias_cosecha" min="1" max="400" required value="${editando?.dias_cosecha ?? ''}"></div>
            <div class="col-5"><label class="form-label small">Plantas</label><input type="number" class="form-control" name="plantas" min="0" max="5000" value="${editando?.plantas ?? ''}"></div>
            <div class="col-7"><label class="form-label small">Notas</label><input class="form-control" name="notas" maxlength="255" value="${esc(editando?.notas ?? '')}"></div>
            <div class="col-12" id="avisosPlan"></div>
            <div class="col-12 d-flex gap-2">
                ${editando ? '<button type="button" class="btn btn-light btn-sm" id="cancelarPlan">Cancelar</button>' : ''}
                <button type="submit" class="btn btn-huerto btn-sm ms-auto"><i class="bi bi-calendar2-check me-1"></i>${editando ? 'Guardar cambios' : 'Planificar'}</button>
            </div>
        </form>
        <datalist id="listaEspeciesPlan">${ESPECIES.map(e => `<option value="${esc(e.nombre)}">`).join('')}</datalist>
        ${renderPendientes()}`;
    renderAvisosPlan();
}

function renderPendientes() {
    if (!estado.planificadas.length) return '<div class="subtitulo">Siembras planificadas</div><p class="small text-muted mb-0">Todavía no planificaste siembras.</p>';
    const limite = sumarDias(hoy, 7);
    return `<div class="subtitulo">Siembras planificadas</div><ul class="lista-plano">${[...estado.planificadas].sort((a, b) => a.inicio - b.inicio).map(p => {
        const cantero = porClave('e' + p.cantero_id);
        const atrasada = p.inicio < hoy;
        return `<li>
            <span class="emoji">${emojiDe(p.nombre)}</span>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-truncate">${esc(p.nombre)}${p.plantas ? ` <span class="text-muted fw-normal">× ${p.plantas}</span>` : ''}</div>
                <div class="detalle ${atrasada ? 'text-danger' : ''}">${fechaMedia(p.inicio)} · ${esc(cantero?.nombre ?? 'cantero borrado')}${atrasada ? ' · atrasada' : ''}</div>
            </div>
            <div class="d-flex gap-1">
                ${p.inicio <= limite ? `<a class="btn btn-huerto btn-sm py-1 px-2" href="${D.urls.sembrar.replace('{id}', p.id)}" title="Cargar como cultivo">Sembrar</a>` : ''}
                <button type="button" class="btn btn-light btn-sm py-1 px-2" data-ver-plan="${p.id}" title="Ver en el plano"><i class="bi bi-eye"></i></button>
                <button type="button" class="btn btn-light btn-sm py-1 px-2" data-editar-plan="${p.id}" title="Modificar"><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-light btn-sm py-1 px-2 text-danger" data-borrar-plan="${p.id}" title="Borrar"><i class="bi bi-trash"></i></button>
            </div>
        </li>`;
    }).join('')}</ul>`;
}

$('#planificar').addEventListener('change', evento => {
    if (evento.target.id === 'planCantero') {
        estado.planCantero = evento.target.value;
        estado.planEditando = null;
        renderPlanificar();
    }
});

$('#planificar').addEventListener('input', evento => {
    const form = evento.target.form;
    if (!form || form.id !== 'formPlan') return;
    // Al reconocer la especie se completan los días y una cantidad de plantas que entra
    if (evento.target.name === 'especie') {
        const especie = identificar(form.especie.value);
        if (especie) {
            form.dias_cosecha.value = especie.dias_cosecha;
            const el = porClave(estado.planCantero);
            const inicio = fechaLocal(form.fecha.value || isoDe(hoy));
            const fin = sumarDias(inicio, especie.dias_cosecha);
            const compartido = historial(el).filter(o => o.inicio < fin && o.fin > inicio).length + 1;
            form.plantas.value = capacidad(el, especie, compartido);
        }
    }
    renderAvisosPlan();
});

$('#planificar').addEventListener('click', async evento => {
    const objetivo = evento.target.closest('[data-idea], [data-usar-fecha], [data-ver-plan], [data-editar-plan], [data-borrar-plan], #cancelarPlan');
    if (!objetivo) return;
    const form = $('#formPlan');

    if (objetivo.dataset.idea) {
        form.especie.value = objetivo.dataset.idea;
        form.especie.dispatchEvent(new Event('input', { bubbles: true }));
    } else if (objetivo.dataset.usarFecha) {
        evento.preventDefault();
        form.fecha.value = objetivo.dataset.usarFecha;
        renderAvisosPlan();
    } else if (objetivo.dataset.verPlan) {
        const p = estado.planificadas.find(x => x.id === Number(objetivo.dataset.verPlan));
        if (!p) return;
        irAFecha(sumarDias(p.inicio, p.dias_cosecha * 0.7));
        const el = porClave('e' + p.cantero_id);
        if (el) seleccionar(el.clave);
    } else if (objetivo.dataset.editarPlan) {
        const p = estado.planificadas.find(x => x.id === Number(objetivo.dataset.editarPlan));
        if (!p) return;
        estado.planEditando = p.id;
        estado.planCantero = 'e' + p.cantero_id;
        renderPlanificar();
    } else if (objetivo.id === 'cancelarPlan') {
        estado.planEditando = null;
        renderPlanificar();
    } else if (objetivo.dataset.borrarPlan) {
        const id = Number(objetivo.dataset.borrarPlan);
        if (!confirm('¿Borrar esta siembra planificada?')) return;
        await enviar(D.urls.eliminar.replace('{id}', id), {});
        estado.planificadas = estado.planificadas.filter(p => p.id !== id);
        if (estado.planEditando === id) estado.planEditando = null;
        despuesDePlanificar();
    }
});

$('#planificar').addEventListener('submit', async evento => {
    evento.preventDefault();
    const datos = datosFormPlan();
    const boton = evento.target.querySelector('[type=submit]');
    boton.disabled = true;
    try {
        // El cantero tiene que estar guardado para poder planificar en él
        let el = porClave(estado.planCantero);
        if (!el.id || estado.cambios) {
            await guardar();
            el = porClave(estado.planCantero);
        }
        const respuesta = await enviar(D.urls.planificar, {
            id: estado.planEditando,
            cantero_id: el.id,
            especie: datos.especie,
            variedad: datos.variedad,
            fecha: datos.fecha,
            dias_cosecha: datos.dias,
            plantas: datos.plantas,
            notas: datos.notas,
        });
        const plan = prepararPlan(respuesta.plan);
        estado.planificadas = estado.planificadas.filter(p => p.id !== plan.id).concat(plan);
        estado.planEditando = null;
        despuesDePlanificar();
    } catch (error) {
        alert(error.message);
    } finally {
        boton.disabled = false;
    }
});

function despuesDePlanificar() {
    construirTodasLasPlantas();
    renderPlanificar();
    programarAnalisis();
}

// ---------- Panel: Análisis ----------

function renderAnalisis() {
    const fecha = fechaVista();
    const lista = plantables();
    if (!lista.length) {
        $('#analisis').innerHTML = '<p class="small text-muted">Agregá canteros para ver el análisis de sol, viento y agua.</p>';
        return;
    }
    const superficie = lista.reduce((t, e) => t + area(e), 0);
    const ocupada = lista.reduce((t, e) => t + (estado.resultados.get(e.clave)?.lista.length ? area(e) : 0), 0);
    const solMedio = lista.reduce((t, e) => t + (estado.resultados.get(e.clave)?.horas ?? 0), 0) / lista.length;
    const orden = { grave: 0, alerta: 1, info: 2, ok: 3 };
    const avisos = [...(estado.avisos ?? [])].sort((a, b) => orden[a.tipo] - orden[b.tipo]);

    const dato = (valor, texto) => `<div class="col-6"><div class="tarjeta-sel py-2 px-3"><div class="fw-semibold fs-5" style="font-family:Outfit">${valor}</div><div class="small text-muted">${texto}</div></div></div>`;

    $('#analisis').innerHTML = `
        <p class="small text-muted mb-2">Para el <b>${fechaMedia(fecha)}</b>. Mové la fecha debajo del plano para ver otro momento.</p>
        <div class="row g-2 mb-2">
            ${dato(`${num(superficie)} m²`, 'para cultivar')}
            ${dato(`${Math.round(ocupada / superficie * 100)} %`, 'ocupado')}
            ${dato(`${num(estado.aguaTotal ?? 0, 0)} L`, 'de agua por semana')}
            ${dato(`${num(solMedio)} h`, 'de sol promedio')}
        </div>
        ${RIEGO.usa_turno && RIEGO.reservorio > 0 ? (() => {
            const necesaria = (estado.aguaTotal ?? 0) / 7 * Math.max(0, RIEGO.entre_turnos - 1);
            return `<div class="small mt-2">Agua entre turnos: ${num(necesaria, 0)} de ${num(RIEGO.reservorio, 0)} L guardados
                <div class="medidor"><span style="width:${limitar(necesaria / RIEGO.reservorio * 100, 0, 100)}%;${necesaria > RIEGO.reservorio ? 'background:var(--alerta-600)' : ''}"></span></div></div>`;
        })() : ''}

        <div class="subtitulo">Avisos</div>
        ${avisos.length ? avisos.map(a => aviso(a.tipo, a.html, a.clave)).join('') : aviso('ok', 'Todo en orden para esta fecha.')}

        <div class="subtitulo">Por cantero</div>
        <div class="table-responsive">
            <table class="table table-sm small align-middle mb-0">
                <thead><tr><th>Cantero</th><th class="text-end">Sol</th><th class="text-end">Agua</th><th class="text-end">Viento</th></tr></thead>
                <tbody>${lista.map(e => {
                    const r = estado.resultados.get(e.clave);
                    const viento = r?.reparo === null || r?.reparo === undefined ? '—' : r.reparo >= 0.67 ? 'Reparado' : r.reparo > 0 ? 'Parcial' : 'Expuesto';
                    return `<tr data-ir="${e.clave}" role="button"><td>${esc(e.nombre)}${e.media_sombra ? ' <i class="bi bi-grid-3x3-gap text-muted" title="Con media sombra"></i>' : ''}</td><td class="text-end">${num(r?.horas ?? 0)} h</td><td class="text-end">${num(r?.agua ?? 0, 0)} L</td><td class="text-end">${viento}</td></tr>`;
                }).join('')}</tbody>
            </table>
        </div>
        <p class="small text-muted mt-2 mb-0">Sol: horas de sol directo en el día, con las sombras de árboles, muros, cortinas y tanques. Agua: litros por semana ajustados al mes y a tu método de riego.</p>`;
}

$('#analisis').addEventListener('click', evento => {
    const clave = evento.target.closest('[data-ir]')?.dataset.ir;
    if (clave) seleccionar(clave);
});

// ---------- Panel: Terreno ----------

function rellenarTerreno() {
    const form = $('#formTerreno');
    const T = estado.terreno;
    form.ancho.value = T.ancho;
    form.largo.value = T.largo;
    form.norte.value = T.norte;
    form.viento.value = T.viento === null ? '' : String(T.viento);
    form.latitud.value = T.latitud;
    form.longitud.value = T.longitud;
    describirNorte();
}

function describirNorte() {
    const n = estado.terreno.norte;
    $('#agujaNorte').style.transform = `rotate(${n}deg)`;
    const lados = ['arriba', 'la derecha', 'abajo', 'la izquierda'];
    const exacto = n % 90 === 0;
    $('#textoNorte').textContent = exacto
        ? `El norte queda hacia ${lados[n / 90]} del plano.`
        : `El norte queda girado ${n}° desde arriba del plano, en sentido horario.`;
}

$('#formTerreno').addEventListener('input', evento => {
    const form = evento.currentTarget;
    const T = estado.terreno;
    const nombre = evento.target.name;
    if (nombre === 'ancho' || nombre === 'largo') {
        const valor = parseFloat(evento.target.value);
        if (Number.isNaN(valor) || valor < 2 || valor > 200) return;
        T[nombre] = valor;
        // Lo que quedó afuera del terreno se acomoda en el borde
        estado.elementos.forEach(el => { el.x = Math.min(el.x, T.ancho); el.z = Math.min(el.z, T.largo); });
        construirTerreno();
        estado.elementos.forEach(posicionar);
        ajustarTamano();
    } else if (nombre === 'norte') {
        T.norte = Number(form.norte.value);
        describirNorte();
        construirTerreno();
    } else if (nombre === 'viento') {
        T.viento = form.viento.value === '' ? null : Number(form.viento.value);
    } else if (nombre === 'latitud' || nombre === 'longitud') {
        const valor = parseFloat(evento.target.value);
        if (Number.isNaN(valor)) return;
        T[nombre] = valor;
        actualizarSol();
    }
    cacheSol.clear();
    marcarCambios();
});

$('#btnUbicacion').addEventListener('click', () => {
    if (!navigator.geolocation) return alert('Tu navegador no permite obtener la ubicación.');
    navigator.geolocation.getCurrentPosition(pos => {
        estado.terreno.latitud = Number(pos.coords.latitude.toFixed(4));
        estado.terreno.longitud = Number(pos.coords.longitude.toFixed(4));
        rellenarTerreno();
        cacheSol.clear();
        actualizarSol();
        marcarCambios();
    }, () => alert('No se pudo obtener la ubicación.'));
});

// ---------- Tiempo ----------

function actualizarFecha() {
    const fecha = fechaVista();
    $('#salidaFecha').textContent = estado.dia === 0 ? `Hoy, ${fechaCorta(fecha)}` : fechaMedia(fecha);
    construirTodasLasPlantas();
    actualizarSol();
    renderSeleccion();
    programarAnalisis();
}

function irAFecha(fecha) {
    estado.dia = limitar(diasEntre(hoy, fecha), 0, 365);
    $('#rangoFecha').value = estado.dia;
    actualizarFecha();
}

let fechaPendiente = false;
$('#rangoFecha').addEventListener('input', evento => {
    estado.dia = Number(evento.target.value);
    if (fechaPendiente) return;
    fechaPendiente = true;
    requestAnimationFrame(() => { fechaPendiente = false; actualizarFecha(); });
});

$('#rangoHora').addEventListener('input', evento => {
    estado.hora = Number(evento.target.value);
    $('#salidaHora').textContent = horaTexto(estado.hora) + ' h';
    actualizarSol();
});

// ---------- Barra de la escena ----------

document.querySelectorAll('[data-vista]').forEach(b => b.addEventListener('click', () => cambiarVista(b.dataset.vista)));
document.querySelectorAll('[data-capa]').forEach(b => b.addEventListener('click', () => {
    estado.capa = b.dataset.capa;
    document.querySelectorAll('[data-capa]').forEach(x => x.classList.toggle('activo', x === b));
    pintarCapa();
    actualizarEtiquetas();
}));

$('#btnGrilla').addEventListener('click', e => {
    e.currentTarget.classList.toggle('activo');
    if (grilla) grilla.visible = e.currentTarget.classList.contains('activo');
});
$('#btnEtiquetas').addEventListener('click', e => {
    e.currentTarget.classList.toggle('activo');
    contenedor.classList.toggle('etiquetas-ocultas', !e.currentTarget.classList.contains('activo'));
});
$('#btnSombras').addEventListener('click', e => {
    e.currentTarget.classList.toggle('activo');
    sol.castShadow = e.currentTarget.classList.contains('activo');
});
$('#btnCentrar').addEventListener('click', centrarVista);

$('#btnImagen').addEventListener('click', () => {
    renderer.render(escena, camaraActiva());
    const enlace = document.createElement('a');
    enlace.download = `huerto-3d-${isoDe(fechaVista())}.png`;
    enlace.href = renderer.domElement.toDataURL('image/png');
    enlace.click();
});

// ---------- Pestañas ----------

function abrirPestana(nombre) {
    document.querySelectorAll('[data-pestana]').forEach(b => b.classList.toggle('activa', b.dataset.pestana === nombre));
    document.querySelectorAll('.pestana').forEach(p => p.classList.toggle('activa', p.id === 'pestana-' + nombre));
}
document.querySelectorAll('[data-pestana]').forEach(b => b.addEventListener('click', () => abrirPestana(b.dataset.pestana)));

// ---------- Guardar ----------

async function enviar(url, cuerpo) {
    const respuesta = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(cuerpo),
    });
    const datos = await respuesta.json().catch(() => ({}));
    if (!respuesta.ok || datos.ok === false) throw new Error(datos.error || 'No se pudo guardar. Revisá la conexión e intentá de nuevo.');
    return datos;
}

async function guardar() {
    const boton = $('#btnGuardar');
    const texto = $('#estadoGuardado');
    boton.disabled = true;
    texto.textContent = 'Guardando…';
    try {
        const respuesta = await enviar(D.urls.guardar, {
            terreno: estado.terreno,
            elementos: estado.elementos.map(({ clave, id, tipo, nombre, x, z, ancho, largo, alto, rotacion, media_sombra }) =>
                ({ clave, id, tipo, nombre, x, z, ancho, largo, alto, rotacion, media_sombra })),
            ubicaciones: estado.ubicaciones,
        });

        // Las claves temporales pasan a ser las del id guardado
        const nuevaClaveDe = clave => respuesta.ids[clave] ? 'e' + respuesta.ids[clave] : null;
        Object.keys(estado.ubicaciones).forEach(id => {
            if (estado.ubicaciones[id]) estado.ubicaciones[id] = nuevaClaveDe(estado.ubicaciones[id]);
        });
        estado.seleccion = estado.seleccion && nuevaClaveDe(estado.seleccion);
        estado.planCantero = estado.planCantero && nuevaClaveDe(estado.planCantero);
        estado.elementos = respuesta.elementos.map(prepararElemento);
        const existentes = new Set(estado.elementos.map(e => e.id));
        estado.planificadas = estado.planificadas.filter(p => existentes.has(p.cantero_id));
        estado.cambios = false;

        reconstruirTodo();
        renderTodo();
        programarAnalisis();
        texto.textContent = 'Plano guardado';
        texto.classList.remove('pendiente');
    } catch (error) {
        texto.textContent = 'No se pudo guardar';
        throw error;
    } finally {
        boton.disabled = false;
    }
}

$('#btnGuardar').addEventListener('click', () => guardar().catch(error => alert(error.message)));

window.addEventListener('beforeunload', evento => {
    if (estado.cambios) {
        evento.preventDefault();
        evento.returnValue = '';
    }
});

// ---------- Inicio ----------

function renderTodo() {
    renderSeleccion();
    renderCultivos();
    renderPlanificar();
    actualizarEtiquetas();
}

renderPaleta();
rellenarTerreno();
construirTerreno();
estado.elementos.forEach(reconstruirElemento);
centrarVista();
$('#salidaHora').textContent = horaTexto(estado.hora) + ' h';
actualizarFecha();
renderTodo();
$('#estadoGuardado').textContent = estado.elementos.length ? 'Plano guardado' : 'Empezá agregando elementos o un diseño de partida';
$('#cargando').remove();

renderer.setAnimationLoop(() => {
    controlesActivos().update();
    renderer.render(escena, camaraActiva());
    etiquetas.render(escena, camaraActiva());
});
