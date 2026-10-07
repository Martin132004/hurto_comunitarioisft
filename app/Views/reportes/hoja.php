<?php
/**
 * Hoja del informe mensual de seguimiento.
 * La usan la vista previa en pantalla y el PDF: solo tablas y CSS simple para que dompdf la dibuje igual.
 */
$n = fn ($valor, $decimales = 1) => number_format((float) $valor, $decimales, ',', '.');
$f = fn ($fecha) => date('d/m/Y', strtotime(is_string($fecha) ? $fecha : $fecha->format('Y-m-d')));
$t = $totales;
$fuentes = \App\Models\HuertoModel::FUENTES_AGUA;
$gravedades = \App\Models\ReporteProblemaModel::GRAVEDADES;
$estados = \App\Models\ReporteProblemaModel::ESTADOS;
// Logo embebido: dompdf no lee archivos fuera de su carpeta y el JPEG no necesita la extensión GD
$logo = 'data:image/jpeg;base64,' . base64_encode((string) @file_get_contents(FCPATH . 'img/agrotech-membrete.jpg'));

// Resumen en texto armado con los totales del período
$resumen = [];
$resumen[] = $cosechas && $t['sin_pesar'] === count($cosechas)
    ? 'Durante el período se registraron ' . count($cosechas) . ' cosecha(s), ninguna pesada.'
    : ($cosechas
    ? 'Durante el período se registraron ' . count($cosechas) . ' cosecha(s) por un total de ' . $n($t['kg'], 2) . ' kg'
        . ($t['sin_pesar'] ? ' (' . $t['sin_pesar'] . ' sin pesar)' : '') . '.'
    : 'Durante el período no se registraron cosechas.');
$resumen[] = $t['programados'] > 0
    ? 'Se registraron ' . $t['registrados'] . ' de los ' . $t['programados'] . ' riegos previstos por el plan (' . $t['cumplimiento'] . ' %), con '
        . $n($t['litros']) . ' L aplicados frente a ' . $n($t['litros_plan']) . ' L planificados.'
    : 'No hubo riegos previstos por el plan en el período.';
$resumen[] = (count($siembras) ? 'Se realizaron ' . count($siembras) . ' siembra(s) o trasplante(s)' : 'No se realizaron siembras')
    . ' y ' . ($t['problemas'] ? 'se reportaron ' . $t['problemas'] . ' problema(s) sanitario(s), ' . $t['resueltos'] . ' resuelto(s) al cierre.' : 'no se reportaron problemas sanitarios.');
?>
<style>
    .hoja { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9pt; color: #1d2a24; line-height: 1.45; }
    .hoja table { width: 100%; border-collapse: collapse; }
    .hoja td, .hoja th { vertical-align: top; }
    .hoja .membrete td { padding-bottom: 6px; border-bottom: 2.5px solid #0f3d2e; vertical-align: bottom; }
    .hoja .marca { font-size: 11pt; font-weight: bold; color: #0f3d2e; }
    .hoja .logo { height: 34px; width: auto; }
    .hoja .marca-sub { font-size: 7.5pt; color: #5b6b63; }
    .hoja .ref { text-align: right; font-size: 7.5pt; color: #5b6b63; }
    .hoja .ref strong { color: #1d2a24; }
    .hoja .titulo { margin-top: 18px; font-size: 17pt; font-weight: bold; color: #0f3d2e; line-height: 1.2; }
    .hoja .subtitulo { font-size: 10.5pt; color: #3b4a42; margin-top: 2px; }
    .hoja .ficha { margin-top: 14px; border-top: 1px solid #c9d5cd; border-bottom: 1px solid #c9d5cd; }
    .hoja .ficha td { padding: 3px 10px 3px 0; font-size: 8.5pt; }
    .hoja .ficha .clave { color: #5b6b63; width: 17%; white-space: nowrap; }
    .hoja .seccion { margin-top: 18px; }
    .hoja .seccion-titulo { font-size: 11pt; font-weight: bold; color: #0f3d2e; border-bottom: 1px solid #9fb3a8; padding-bottom: 2px; margin-bottom: 6px; }
    .hoja .seccion-titulo .nro { color: #6b9a5b; margin-right: 4px; }
    .hoja p { margin: 0 0 5px 0; text-align: justify; }
    .hoja .indicadores td { width: 25%; padding: 0 4px; }
    .hoja .indicadores td:first-child { padding-left: 0; }
    .hoja .indicadores td:last-child { padding-right: 0; }
    .hoja .indicador { border: 1px solid #c9d5cd; border-left: 3px solid #0f3d2e; padding: 6px 8px; }
    .hoja .indicador-valor { font-size: 13pt; font-weight: bold; color: #0f3d2e; line-height: 1.2; }
    .hoja .indicador-label { font-size: 7pt; color: #5b6b63; text-transform: uppercase; letter-spacing: .4px; }
    .hoja .tabla-titulo { font-size: 7.5pt; color: #5b6b63; font-style: italic; margin: 6px 0 3px; }
    .hoja .detalle th { color: #0f3d2e; font-size: 7.5pt; text-align: left; padding: 4px 6px; border-top: 1.5px solid #0f3d2e; border-bottom: 1px solid #0f3d2e; }
    .hoja .detalle td { padding: 3px 6px; border-bottom: 1px solid #e3e9e5; }
    .hoja .detalle .num { text-align: right; white-space: nowrap; }
    .hoja .detalle .total td { font-weight: bold; border-top: 1px solid #0f3d2e; border-bottom: 1.5px solid #0f3d2e; }
    .hoja .vacio { color: #8a978f; font-style: italic; text-align: center; padding: 8px !important; }
    .hoja .nota { color: #5b6b63; font-size: 7.5pt; }
    .hoja .alerta { color: #b02a37; font-weight: bold; }
    .hoja .observaciones { border-left: 3px solid #c9d5cd; padding: 4px 10px; min-height: 36px; }
    .hoja .firma { border-top: 1px solid #1d2a24; text-align: center; padding-top: 4px; font-size: 8pt; }
    .hoja .firma span { display: block; font-size: 7pt; color: #5b6b63; }
</style>

<div class="hoja">
    <!-- Membrete -->
    <table class="membrete">
        <tr>
            <td>
                <img src="<?= $logo ?>" alt="AgroTech" class="logo">
                <div class="marca-sub">Sistema de gestión de huertas</div>
            </td>
            <td class="ref">
                Informe N° <strong><?= esc($numero) ?></strong><br>
                Emitido el <?= $emitido->format('d/m/Y H:i') ?>
            </td>
        </tr>
    </table>

    <div class="titulo">Informe mensual de seguimiento</div>
    <div class="subtitulo"><?= esc($huerto['nombre']) ?> · <?= esc($periodo) ?><?= $parcial ? ' (mes en curso)' : '' ?></div>

    <!-- Ficha del huerto -->
    <table class="ficha">
        <tr>
            <td class="clave">Período:</td>
            <td><?= $f($desde) ?> al <?= $f($hasta) ?></td>
            <td class="clave">Zona:</td>
            <td><?= $zona ? esc($zona['nombre']) : 'Sin configurar' ?></td>
        </tr>
        <tr>
            <td class="clave">Fuente de agua:</td>
            <td><?= esc($fuentes[$huerto['fuente_agua']]['nombre'] ?? '-') ?></td>
            <td class="clave">Método de riego:</td>
            <td><?= esc($metodo) ?></td>
        </tr>
        <tr>
            <td class="clave">Cultivos activos:</td>
            <td><?= $t['activos'] ?> al cierre del período</td>
            <td colspan="2"></td>
        </tr>
        <?php if ($zona): ?>
            <tr>
                <td class="clave">Departamentos:</td>
                <td colspan="3"><?= esc($zona['departamentos']) ?></td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Resumen -->
    <div class="seccion">
        <div class="seccion-titulo"><span class="nro">1.</span> Resumen del período</div>
        <table class="indicadores">
            <tr>
                <td><div class="indicador"><div class="indicador-valor"><?= $n($t['kg'], 1) ?> kg</div><div class="indicador-label">Cosecha total</div></div></td>
                <td><div class="indicador"><div class="indicador-valor"><?= $n($t['litros'], 0) ?> L</div><div class="indicador-label">Agua aplicada</div></div></td>
                <td><div class="indicador"><div class="indicador-valor"><?= $t['cumplimiento'] !== null ? $t['cumplimiento'] . ' %' : '-' ?></div><div class="indicador-label">Cumplimiento del riego</div></div></td>
                <td><div class="indicador"><div class="indicador-valor"><?= $t['litros_por_kg'] !== null ? $n($t['litros_por_kg']) . ' L/kg' : '-' ?></div><div class="indicador-label">Agua por kg cosechado</div></div></td>
            </tr>
        </table>
        <div style="margin-top: 8px;">
            <?php foreach ($resumen as $parrafo): ?>
                <p><?= esc($parrafo) ?></p>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Producción -->
    <div class="seccion">
        <div class="seccion-titulo"><span class="nro">2.</span> Producción</div>
        <div class="tabla-titulo">Tabla 1. Cosechas registradas en el período</div>
        <table class="detalle">
            <tr><th style="width: 14%;">Fecha</th><th>Cultivo</th><th>Detalle</th><th class="num" style="width: 16%;">Kg</th></tr>
            <?php if (! $cosechas): ?>
                <tr><td colspan="4" class="vacio">Sin cosechas registradas en el período.</td></tr>
            <?php endif; ?>
            <?php foreach ($cosechas as $c): ?>
                <tr>
                    <td><?= $f($c['fecha']) ?></td>
                    <td><?= esc(ucfirst($c['nombre_planta'] ?? 'Cultivo eliminado')) ?><?= $c['variedad'] ? ' <span class="nota">(' . esc($c['variedad']) . ')</span>' : '' ?></td>
                    <td><?= $c['final'] ? 'Cosecha final' : 'Cosecha parcial' ?><?= $c['observaciones'] ? ' · ' . esc($c['observaciones']) : '' ?></td>
                    <td class="num"><?= $c['kg'] === null ? '<span class="nota">sin pesar</span>' : $n($c['kg'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($cosechas): ?>
                <tr class="total"><td colspan="3">Total</td><td class="num"><?= $n($t['kg'], 2) ?> kg</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Riego -->
    <div class="seccion">
        <div class="seccion-titulo"><span class="nro">3.</span> Riego</div>
        <div class="tabla-titulo">Tabla 2. Riegos registrados frente al plan, por cultivo</div>
        <table class="detalle">
            <tr>
                <th style="width: 24%;">Cultivo</th>
                <th class="num">Frecuencia</th>
                <th class="num">Riegos registrados</th>
                <th class="num">Riegos según plan</th>
                <th class="num">Litros aplicados</th>
                <th class="num">Litros según plan</th>
            </tr>
            <?php if (! $riego): ?>
                <tr><td colspan="6" class="vacio">Sin cultivos para regar en el período.</td></tr>
            <?php endif; ?>
            <?php foreach ($riego as $r): ?>
                <tr>
                    <td><?= esc(ucfirst($r['cultivo'])) ?><?= $r['variedad'] ? ' <span class="nota">(' . esc($r['variedad']) . ')</span>' : '' ?></td>
                    <td class="num"><?= $r['frecuencia'] ? 'cada ' . $r['frecuencia'] . ' d' : '-' ?></td>
                    <td class="num<?= $r['registrados'] < $r['programados'] / 2 ? ' alerta' : '' ?>"><?= $r['registrados'] ?></td>
                    <td class="num"><?= $r['programados'] ?></td>
                    <td class="num"><?= $n($r['litros']) ?> L</td>
                    <td class="num"><?= $n($r['litros_plan']) ?> L</td>
                </tr>
            <?php endforeach; ?>
            <?php if ($riego): ?>
                <tr class="total">
                    <td colspan="2">Total</td>
                    <td class="num"><?= $t['registrados'] ?></td>
                    <td class="num"><?= $t['programados'] ?></td>
                    <td class="num"><?= $n($t['litros']) ?> L</td>
                    <td class="num"><?= $n($t['litros_plan']) ?> L</td>
                </tr>
            <?php endif; ?>
        </table>
        <div class="nota" style="margin-top: 3px;">Los litros según plan están ajustados al mes y al método de riego. En rojo, cultivos con menos de la mitad de los riegos previstos.</div>
    </div>

    <!-- Siembras -->
    <div class="seccion">
        <div class="seccion-titulo"><span class="nro">4.</span> Siembras y trasplantes</div>
        <div class="tabla-titulo">Tabla 3. Cultivos incorporados en el período</div>
        <table class="detalle">
            <tr><th style="width: 14%;">Fecha</th><th>Cultivo</th><th>Variedad</th><th class="num" style="width: 22%;">Cosecha estimada</th></tr>
            <?php if (! $siembras): ?>
                <tr><td colspan="4" class="vacio">Sin siembras en el período.</td></tr>
            <?php endif; ?>
            <?php foreach ($siembras as $s): ?>
                <tr>
                    <td><?= $f($s['fecha_siembra']) ?></td>
                    <td><?= esc(ucfirst($s['nombre_planta'])) ?></td>
                    <td><?= esc($s['variedad'] ?: '-') ?></td>
                    <td class="num"><?= $f($s['cosecha_estimada']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Problemas -->
    <div class="seccion">
        <div class="seccion-titulo"><span class="nro">5.</span> Sanidad: problemas y plagas</div>
        <div class="tabla-titulo">Tabla 4. Problemas reportados y su estado al cierre</div>
        <table class="detalle">
            <tr><th style="width: 14%;">Fecha</th><th>Cultivo</th><th>Problema</th><th>Gravedad</th><th>Estado</th></tr>
            <?php if (! $problemas): ?>
                <tr><td colspan="5" class="vacio">Sin problemas reportados en el período.</td></tr>
            <?php endif; ?>
            <?php foreach ($problemas as $p): ?>
                <tr>
                    <td><?= $f($p['created_at']) ?></td>
                    <td><?= esc(ucfirst($p['cultivo_nombre'] ?? 'Todo el huerto')) ?></td>
                    <td><?= esc($p['nombre']) ?></td>
                    <td><?= esc($gravedades[$p['gravedad']]['nombre'] ?? '-') ?></td>
                    <td><?= esc($estados[$p['estado']]['nombre'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Observaciones -->
    <div class="seccion">
        <div class="seccion-titulo"><span class="nro">6.</span> Observaciones</div>
        <div class="observaciones"><?= ! empty($observaciones) ? nl2br(esc($observaciones)) : '&nbsp;' ?></div>
    </div>

    <!-- Firmas -->
    <table style="margin-top: 48px; page-break-inside: avoid;">
        <tr>
            <td style="width: 38%;"><div class="firma">Elaborado por<span>Responsable del huerto</span></div></td>
            <td style="width: 24%;"></td>
            <td style="width: 38%;"><div class="firma">Revisado por<span>Técnico/a</span></div></td>
        </tr>
    </table>
</div>
