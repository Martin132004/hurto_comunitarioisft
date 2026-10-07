<?php

namespace App\Controllers;

use App\Models\CultivoModel;
use App\Models\EspecieModel;
use App\Models\HuertoModel;
use App\Models\ProblemaModel;
use App\Models\ReporteProblemaModel;
use App\Models\ZonaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

// Reporte de plagas y problemas del huerto, con respuesta del técnico y alertas por zona
class Problemas extends BaseController
{
    private const CARPETA_FOTOS = WRITEPATH . 'uploads/problemas/';
    private const FOTO_MAX_KB = 8192;
    private const FOTO_TIPOS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public function index()
    {
        $huerto = (new HuertoModel())->actual();
        $zona = (new ZonaModel())->porClave($huerto['zona']);
        $reportes = new ReporteProblemaModel();
        $catalogo = (new ProblemaModel())->catalogo();

        // Por defecto se muestran los que siguen sin resolver
        $filtro = $this->request->getGet('ver') === 'resueltos' ? 'resueltos' : 'pendientes';
        $lista = $reportes->where('huerto_id', $huerto['id'])
            ->where('estado ' . ($filtro === 'resueltos' ? '=' : '!='), 'resuelto')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('problemas/index', [
            'zona'       => $zona,
            'catalogo'   => $catalogo,
            'reportes'   => $lista,
            'filtro'     => $filtro,
            'brotes'     => $reportes->brotesEnZona($huerto['zona']),
            'repetidos'  => $reportes->repetidosEnHuerto($huerto['id']),
            'vigilar'    => $this->riesgosDelMes($catalogo, $zona),
            'tipos'      => ProblemaModel::TIPOS,
            'estados'    => ReporteProblemaModel::ESTADOS,
            'gravedades' => ReporteProblemaModel::GRAVEDADES,
        ]);
    }

    public function nuevo()
    {
        $catalogo = (new ProblemaModel())->catalogo();
        $cultivos = (new CultivoModel())->where('estado !=', 'Cosechado')->orderBy('nombre_planta')->findAll();

        if ($this->request->getMethod() === 'GET') {
            // Para cada cultivo, los problemas del catálogo que lo pueden afectar: la vista filtra las opciones
            $especies = new EspecieModel();
            $posibles = [];
            foreach ($cultivos as $cultivo) {
                $especie = $especies->identificar($cultivo['nombre_planta']);
                $posibles[$cultivo['id']] = array_keys(array_filter($catalogo, fn ($p) => ProblemaModel::afectaA($p, $especie)));
            }

            return view('problemas/nuevo', [
                'catalogo'   => $catalogo,
                'cultivos'   => $cultivos,
                'posibles'   => $posibles,
                'elegido'    => (int) $this->request->getGet('cultivo'),
                'tipos'      => ProblemaModel::TIPOS,
                'gravedades' => ReporteProblemaModel::GRAVEDADES,
            ]);
        }

        $post = fn ($campo) => trim((string) $this->request->getPost($campo));
        $cultivo = $post('cultivo_id') !== '' ? (new CultivoModel())->find((int) $post('cultivo_id')) : null;
        $problema = array_key_exists($post('problema'), $catalogo) ? $post('problema') : null;
        $descripcion = mb_substr($post('descripcion'), 0, 2000);

        $foto = $this->request->getFile('foto');
        $hayFoto = $foto && $foto->getError() !== UPLOAD_ERR_NO_FILE;

        if (! $problema && $descripcion === '' && ! $hayFoto) {
            return redirect()->back()->withInput()->with('error', 'Elegí qué problema es, contá qué ves o subí una foto.');
        }

        $nombreFoto = null;
        if ($hayFoto) {
            $error = $this->validarFoto($foto);
            if ($error) {
                return redirect()->back()->withInput()->with('error', $error);
            }
            $nombreFoto = bin2hex(random_bytes(12)) . '.' . self::FOTO_TIPOS[$foto->getMimeType()];
            $foto->move(self::CARPETA_FOTOS, $nombreFoto);
        }

        $huerto = (new HuertoModel())->actual();
        $reportes = new ReporteProblemaModel();
        $id = $reportes->insert([
            'huerto_id'      => $huerto['id'],
            'zona'           => $huerto['zona'],
            'cultivo_id'     => $cultivo['id'] ?? null,
            'cultivo_nombre' => $cultivo['nombre_planta'] ?? null,
            'problema'       => $problema,
            'descripcion'    => $descripcion !== '' ? $descripcion : null,
            'gravedad'       => array_key_exists($post('gravedad'), ReporteProblemaModel::GRAVEDADES) ? $post('gravedad') : 'pocas',
            'foto'           => $nombreFoto,
            'estado'         => 'abierto',
        ]);

        return redirect()->to('huerto/problemas/' . $id)->with('mensaje', 'Problema reportado. El técnico lo va a ver y te va a responder acá.');
    }

    public function ver($id)
    {
        $reporte = (new ReporteProblemaModel())->find($id);
        if (! $reporte) {
            return redirect()->to('huerto/problemas');
        }

        $catalogo = (new ProblemaModel())->catalogo();
        $zona = (new ZonaModel())->porClave($reporte['zona']);

        // Si no se sabe qué es, sugerimos los problemas que pueden afectar al cultivo, primero los de este mes
        $sugerencias = [];
        if (! $reporte['problema']) {
            $especie = $reporte['cultivo_nombre'] ? (new EspecieModel())->identificar($reporte['cultivo_nombre']) : null;
            $mes = (int) date('n', strtotime($reporte['created_at']));
            $desfase = (int) ($zona['desfase_meses'] ?? 0);
            foreach ($catalogo as $clave => $problema) {
                if (ProblemaModel::afectaA($problema, $especie) || ! $especie) {
                    $sugerencias[$clave] = ProblemaModel::enRiesgo($problema, $mes, $desfase);
                }
            }
            arsort($sugerencias);
        }

        return view('problemas/ver', [
            'reporte'     => $reporte,
            'problema'    => $catalogo[$reporte['problema']] ?? null,
            'catalogo'    => $catalogo,
            'sugerencias' => $sugerencias,
            'zona'        => $zona,
            'brotes'      => (new ReporteProblemaModel())->brotesEnZona($reporte['zona']),
            'tipos'       => ProblemaModel::TIPOS,
            'estados'     => ReporteProblemaModel::ESTADOS,
            'gravedades'  => ReporteProblemaModel::GRAVEDADES,
        ]);
    }

    // responder(): el técnico deja su recomendación y puede corregir o confirmar el diagnóstico
    public function responder($id)
    {
        $reportes = new ReporteProblemaModel();
        $reporte = $reportes->find($id);
        if (! $reporte) {
            return redirect()->to('huerto/problemas');
        }

        $respuesta = trim((string) $this->request->getPost('respuesta'));
        $tecnico = trim((string) $this->request->getPost('respondido_por'));
        if ($respuesta === '' || $tecnico === '') {
            return redirect()->back()->withInput()->with('error', 'Completá tu nombre y la respuesta.');
        }

        $diagnostico = (string) $this->request->getPost('problema');
        $catalogo = (new ProblemaModel())->catalogo();

        $reportes->update($id, [
            'respuesta'      => mb_substr($respuesta, 0, 4000),
            'respondido_por' => mb_substr($tecnico, 0, 100),
            'respondido_at'  => date('Y-m-d H:i:s'),
            'problema'       => array_key_exists($diagnostico, $catalogo) ? $diagnostico : $reporte['problema'],
            'estado'         => $reporte['estado'] === 'resuelto' ? 'resuelto' : 'respondido',
        ]);

        return redirect()->to('huerto/problemas/' . $id)->with('mensaje', 'Respuesta guardada.');
    }

    public function resolver($id)
    {
        $reportes = new ReporteProblemaModel();
        if ($reportes->find($id)) {
            $reportes->update($id, ['estado' => 'resuelto', 'resuelto_at' => date('Y-m-d H:i:s')]);
        }

        return redirect()->to('huerto/problemas/' . $id)->with('mensaje', 'Marcado como resuelto.');
    }

    // Las fotos se guardan fuera de la carpeta pública y se sirven desde acá
    public function foto($id)
    {
        $reporte = (new ReporteProblemaModel())->find($id);
        $ruta = $reporte && $reporte['foto'] ? self::CARPETA_FOTOS . basename($reporte['foto']) : null;

        if (! $ruta || ! is_file($ruta)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setContentType(mime_content_type($ruta))
            ->setHeader('Cache-Control', 'private, max-age=86400')
            ->setBody(file_get_contents($ruta));
    }

    private function validarFoto($foto): ?string
    {
        if (in_array($foto->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return 'La foto es demasiado pesada.';
        }
        if (! $foto->isValid()) {
            return 'No se pudo subir la foto. Probá de nuevo.';
        }
        if (! array_key_exists($foto->getMimeType(), self::FOTO_TIPOS)) {
            return 'La foto tiene que ser JPG, PNG o WEBP.';
        }
        if ($foto->getSizeByUnit('kb') > self::FOTO_MAX_KB) {
            return 'La foto es demasiado pesada (máximo 8 MB).';
        }

        return null;
    }

    // Problemas del catálogo con riesgo este mes para los cultivos en curso: [clave => nombres de cultivos]
    private function riesgosDelMes(array $catalogo, ?array $zona): array
    {
        $cultivos = (new CultivoModel())->select('nombre_planta')->where('estado !=', 'Cosechado')->findAll();
        $especies = new EspecieModel();
        $mes = (int) date('n');
        $desfase = (int) ($zona['desfase_meses'] ?? 0);
        $riesgos = [];

        if (! $cultivos) {
            return [];
        }

        foreach ($catalogo as $clave => $problema) {
            // Los que afectan a todo el huerto no se repiten cultivo por cultivo
            if (in_array('*', $problema['afecta'], true) && ProblemaModel::enRiesgo($problema, $mes, $desfase)) {
                $riesgos[$clave] = ['Todo el huerto'];
            }
        }

        foreach ($cultivos as $cultivo) {
            $especie = $especies->identificar($cultivo['nombre_planta']);
            if (! $especie) {
                continue;
            }
            foreach ($catalogo as $clave => $problema) {
                if (in_array('*', $problema['afecta'], true)) {
                    continue;
                }
                if (ProblemaModel::afectaA($problema, $especie) && ProblemaModel::enRiesgo($problema, $mes, $desfase)) {
                    $riesgos[$clave][$especie['nombre']] = $especie['nombre'];
                }
            }
        }

        return array_map('array_values', $riesgos);
    }
}
