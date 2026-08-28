<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Ruta principal: Carga el panel de alertas
$routes->get('/', 'Huerto::index');

// Rutas para las acciones del sistema
$routes->post('huerto/crear', 'Huerto::crear');
$routes->get('huerto/riego/(:num)', 'Huerto::registrarRiego/$1');
$routes->get('huerto/estado/(:num)', 'Huerto::cambiarEstado/$1');
$routes->get('huerto/eliminar/(:num)', 'Huerto::eliminar/$1');
