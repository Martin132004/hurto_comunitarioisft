<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session('usuario_id')) {
            return redirect()->to('/');
        }

        if ($this->request->is('get')) {
            return view('auth/login');
        }

        // Hasta 5 intentos por minuto desde la misma conexión
        if (! service('throttler')->check('login-' . md5($this->request->getIPAddress()), 5, MINUTE)) {
            return redirect()->back()->withInput()->with('error', 'Demasiados intentos. Esperá un minuto y volvé a probar.');
        }

        $usuario  = trim((string) $this->request->getPost('usuario'));
        $password = (string) $this->request->getPost('password');

        if ($usuario === '' || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Completá el usuario y la contraseña.');
        }

        $modelo = new UsuarioModel();
        $fila = $modelo->verificar($usuario, $password);

        if (! $fila) {
            return redirect()->back()->withInput()->with('error', 'Usuario o contraseña incorrectos.');
        }

        // Nueva sesión para evitar que se reutilice un id anterior
        session()->regenerate(true);
        session()->set([
            'usuario_id'     => (int) $fila['id'],
            'usuario_nombre' => $fila['nombre'],
        ]);
        session()->setFlashdata('bienvenida', true);

        $modelo->update($fila['id'], ['ultimo_acceso' => date('Y-m-d H:i:s')]);

        return redirect()->to('/');
    }

    public function salir()
    {
        session()->destroy();

        return redirect()->to('login');
    }
}
