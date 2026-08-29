<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Ruta principal: Carga el panel de alertas
// Ruta principal: Carga el panel de alertas
$routes->get('/', 'Huerto::index');

// Rutas para el formulario (GET para ver la página, POST para guardar los datos)
$routes->get('huerto/crear', 'Huerto::crear');
$routes->post('huerto/crear', 'Huerto::crear');

// Rutas para las acciones de los botones en las tarjetas
$routes->get('huerto/riego/(:num)', 'Huerto::registrarRiego/$1');
$routes->get('huerto/estado/(:num)', 'Huerto::cambiarEstado/$1');
$routes->get('huerto/eliminar/(:num)', 'Huerto::eliminar/$1');
