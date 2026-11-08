<?php

namespace App\Controllers;

use App\Libraries\PlanRiego;
use App\Models\HuertoModel;
use App\Models\ZonaModel;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // Huerto y zona del pedido: se leen de la base una sola vez
    private ?array $huerto = null;
    private ?array $zona = null;
    private bool $zonaLeida = false;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    protected function huerto(): array
    {
        return $this->huerto ??= (new HuertoModel())->actual();
    }

    // Zona configurada para el huerto, o null si todavía no se eligió
    protected function zona(): ?array
    {
        if (! $this->zonaLeida) {
            $this->zona = db_connect()->tableExists('zonas') ? (new ZonaModel())->porClave($this->huerto()['zona']) : null;
            $this->zonaLeida = true;
        }

        return $this->zona;
    }

    protected function planRiego(): PlanRiego
    {
        return new PlanRiego($this->huerto(), $this->zona());
    }

    // Vuelve al formulario con lo que se había escrito y el motivo del error
    protected function volverConError(string $mensaje): RedirectResponse
    {
        return redirect()->back()->withInput()->with('error', $mensaje);
    }

    // Si falta alguna tabla, vuelve al panel con el aviso; si están todas, devuelve null
    protected function sinMigrar(string ...$tablas): ?RedirectResponse
    {
        foreach ($tablas as $tabla) {
            if (! db_connect()->tableExists($tabla)) {
                return redirect()->to('/')->with('error', 'Falta correr las migraciones: php spark migrate');
            }
        }

        return null;
    }

    // Para _remap(): ejecuta la acción pedida solo si es un método público del controlador
    protected function ejecutar(string $metodo, array $parametros)
    {
        if (! method_exists($this, $metodo) || $metodo === '_remap' || ! (new \ReflectionMethod($this, $metodo))->isPublic()) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->{$metodo}(...$parametros);
    }
}
