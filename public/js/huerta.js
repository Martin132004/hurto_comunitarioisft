// Funciones compartidas por la guía de cultivo (huerto/crear):
// textos, fechas, reconocimiento de especies y calendario de heladas de la zona.

export const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
export const DIA = 86400000;

// ---------- Textos ----------

export const esc = texto => String(texto ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
export const num = (valor, decimales = 1) => Number(valor).toLocaleString('es-AR', { maximumFractionDigits: decimales });

// "Tomátes  Cherry" -> "tomates cherry"
export const normalizar = texto => String(texto ?? '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/\s+/g, ' ').trim();

// ['a', 'b', 'c'] -> "a, b y c"
export const listaNombres = items => items.length <= 1 ? items.join('') : items.slice(0, -1).join(', ') + ' y ' + items[items.length - 1];

// ---------- Fechas (siempre en hora local, sin corrimientos por zona horaria) ----------

export function fechaLocal(iso) {
    const [a, m, d] = String(iso).slice(0, 10).split('-').map(Number);
    return new Date(a, m - 1, d);
}
export const sumarDias = (f, n) => { const r = new Date(f); r.setDate(r.getDate() + Math.round(n)); return r; };
export const diasEntre = (desde, hasta) => Math.round((hasta - desde) / DIA);
export const aMesDia = f => String(f.getMonth() + 1).padStart(2, '0') + '-' + String(f.getDate()).padStart(2, '0');

// ---------- Especies ----------

// Devuelve una función que reconoce la especie por nombre, sinónimo o plural ("Tomates cherry" -> Tomate)
export function crearIdentificador(especies) {
    const indice = new Map();
    especies.forEach(e => [e.nombre, ...e.sinonimos].forEach(n => n && indice.set(normalizar(n), e)));

    return texto => {
        const palabras = normalizar(texto).split(' ').filter(Boolean);
        // Si no encontramos "tomate cherry grande", probamos "tomate cherry" y después "tomate"
        for (let n = palabras.length; n > 0; n--) {
            const parte = palabras.slice(0, n);
            // Tal cual y en singular ("tomates" -> "tomate", "morrones" -> "morron")
            for (const variante of [parte, parte.map(p => p.replace(/s$/, '')), parte.map(p => p.replace(/es$/, ''))]) {
                const especie = indice.get(variante.join(' '));
                if (especie) return especie;
            }
        }
        return null;
    };
}

// ---------- Calendario de la zona ----------

export function calendarioDeZona(zona) {
    return {
        // En las zonas frías, las especies sensibles a la helada se siembran más tarde
        mesesEnZona(meses, especie) {
            if (!zona || !especie.sensible_helada || !Number(zona.desfase_meses)) return meses;
            return meses.map(m => (m - 1 + Number(zona.desfase_meses)) % 12 + 1);
        },
        // El período de heladas va de la primera helada (otoño) a la última (primavera)
        enPeriodoDeHeladas(fecha) {
            if (!zona) return false;
            const md = aMesDia(fecha);
            return md >= zona.primera_helada && md < zona.ultima_helada;
        },
        proximaPrimeraHelada(desde) {
            const [m, d] = zona.primera_helada.split('-').map(Number);
            const helada = new Date(desde.getFullYear(), m - 1, d);
            if (helada <= desde) helada.setFullYear(helada.getFullYear() + 1);
            return helada;
        },
    };
}
