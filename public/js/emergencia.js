// Casillas del plan de emergencia y de la reparación: cada cambio se guarda al momento
(function () {
    const caja = document.querySelector('[data-pasos]');
    if (!caja) return;

    const casillas = [...caja.querySelectorAll('input[data-clave]')];
    const contador = caja.querySelector('[data-hechos]');
    const barra = caja.querySelector('[data-barra]');

    function actualizarProgreso() {
        if (!barra) return;
        const hechos = casillas.filter(c => c.checked).length;
        contador.textContent = hechos;
        barra.setAttribute('aria-valuenow', hechos);
        barra.firstElementChild.style.width = (casillas.length ? Math.round(hechos / casillas.length * 100) : 0) + '%';
    }

    casillas.forEach(casilla => {
        // Último estado que el servidor confirmó: si un guardado falla, se vuelve a este
        let confirmado = casilla.checked;

        casilla.addEventListener('change', async () => {
            const datos = new FormData();
            datos.append('clave', casilla.dataset.clave);
            datos.append('hecho', casilla.checked ? '1' : '0');
            // Bloqueada mientras guarda: no se pueden superponer dos cambios de la misma casilla
            casilla.disabled = true;
            casilla.closest('.em-paso')?.setAttribute('aria-busy', 'true');
            actualizarProgreso();

            try {
                const respuesta = await fetch(caja.dataset.url, {
                    method: 'POST',
                    body: datos,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const cuerpo = await respuesta.json().catch(() => ({}));
                if (!respuesta.ok || cuerpo.ok !== true) throw new Error(cuerpo.error || 'No se pudo guardar el paso.');
                confirmado = casilla.checked;
            } catch (error) {
                casilla.checked = confirmado;
                actualizarProgreso();
                alert(error.message === 'Failed to fetch' ? 'Sin conexión: no se pudo guardar el paso. Probá de nuevo.' : error.message);
            } finally {
                casilla.disabled = false;
                casilla.closest('.em-paso')?.removeAttribute('aria-busy');
            }
        });
    });
})();
