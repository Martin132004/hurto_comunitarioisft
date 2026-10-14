<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

// Solo deja pasar a quien inició sesión; el resto va al login
class Autenticado implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('usuario_id')) {
            return null;
        }

        // Pedidos por fetch (guardar el plano): respuesta JSON en lugar de una redirección
        if ($request->hasHeader('X-Requested-With')) {
            return service('response')->setStatusCode(401)->setJSON([
                'ok'    => false,
                'error' => 'La sesión se cerró. Volvé a entrar para guardar.',
            ]);
        }

        return redirect()->to('login');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
