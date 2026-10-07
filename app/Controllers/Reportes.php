<?php

namespace App\Controllers;

use App\Libraries\PlanRiego;
use App\Libraries\ReporteMensual;
use App\Models\HuertoModel;
use App\Models\ZonaModel;
use Dompdf\Dompdf;
use Dompdf\Options;

// Reporte mensual del huerto, con vista previa y exportación a PDF
class Reportes extends BaseController
{
    public function index()
    {
        $mes = $this->mesPedido();

        return view('reportes/index', [
            'mes'     => $mes,
            'minimo'  => $this->primerMes(),
            'reporte' => $this->reporte($mes),
        ]);
    }

    public function pdf()
    {
        $mes = $this->mesPedido();
        $datos = $this->reporte($mes);
        $datos['observaciones'] = mb_substr(trim((string) $this->request->getGet('observaciones')), 0, 600);

        $opciones = new Options();
        $opciones->set('defaultFont', 'DejaVu Sans');
        // Sin recursos remotos: el PDF se arma solo con lo que hay en el servidor
        $opciones->set('isRemoteEnabled', false);

        $pdf = new Dompdf($opciones);
        $pdf->loadHtml(view('reportes/pdf', $datos));
        $pdf->setPaper('A4');
        $pdf->render();

        // Número de página en el pie (el total de páginas solo se conoce después de dibujar)
        $pdf->getCanvas()->page_text(500, 811, 'Página {PAGE_NUM} de {PAGE_COUNT}', $pdf->getFontMetrics()->getFont('DejaVu Sans'), 7, [0.54, 0.59, 0.56]);

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="reporte-huerto-' . $mes . '.pdf"')
            ->setBody($pdf->output());
    }

    private function reporte(string $mes): array
    {
        $huerto = (new HuertoModel())->actual();
        $zona = db_connect()->tableExists('zonas') ? (new ZonaModel())->porClave($huerto['zona']) : null;

        return (new ReporteMensual($huerto, $zona, new PlanRiego($huerto, $zona)))->generar($mes);
    }

    // Mes pedido ("2026-09"); si no es válido o es futuro, el mes actual
    private function mesPedido(): string
    {
        $mes = (string) $this->request->getGet('mes');
        $fecha = \DateTime::createFromFormat('!Y-m', $mes);

        if (! $fecha || $fecha->format('Y-m') !== $mes || $mes > date('Y-m') || $mes < $this->primerMes()) {
            return date('Y-m');
        }

        return $mes;
    }

    // Primer mes con datos: el de la siembra más antigua
    private function primerMes(): string
    {
        $primera = db_connect()->table('cultivos')->selectMin('fecha_siembra')->get()->getRow('fecha_siembra');

        return $primera ? min(substr($primera, 0, 7), date('Y-m')) : date('Y-m');
    }
}
