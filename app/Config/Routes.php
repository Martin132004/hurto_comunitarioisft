<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Ruta principal: Carga el panel de alertas
$routes->get('/', 'Huerto::index');

// Rutas para el formulario (GET para ver la página, POST para guardar los datos)
$routes->get('huerto/crear', 'Huerto::crear');
$routes->post('huerto/crear', 'Huerto::crear');

// Datos del huerto (zona agroclimática)
$routes->get('huerto/configuracion', 'Huerto::configuracion');
$routes->post('huerto/configuracion', 'Huerto::configuracion');

// Rutas para las acciones de los botones en las tarjetas
$routes->post('huerto/riego/(:num)', 'Huerto::registrarRiego/$1');
$routes->get('huerto/regando/(:num)', 'Huerto::regando/$1');
// Cosecha con kg (la ruta vieja de cambiar estado muestra el mismo formulario)
$routes->get('huerto/cosechar/(:num)', 'Huerto::cosechar/$1');
$routes->post('huerto/cosechar/(:num)', 'Huerto::cosechar/$1');
$routes->get('huerto/estado/(:num)', 'Huerto::cosechar/$1');
$routes->post('huerto/eliminar/(:num)', 'Huerto::eliminar/$1');

// Reporte de problemas y plagas
$routes->get('huerto/problemas', 'Problemas::index');
$routes->get('huerto/problemas/nuevo', 'Problemas::nuevo');
$routes->post('huerto/problemas/nuevo', 'Problemas::nuevo');
$routes->get('huerto/problemas/(:num)', 'Problemas::ver/$1');
$routes->get('huerto/problemas/(:num)/foto', 'Problemas::foto/$1');
$routes->post('huerto/problemas/(:num)/responder', 'Problemas::responder/$1');
$routes->post('huerto/problemas/(:num)/resolver', 'Problemas::resolver/$1');

// Reportes mensuales
$routes->get('huerto/reportes', 'Reportes::index');
$routes->get('huerto/reportes/pdf', 'Reportes::pdf');

// Plano 3D del huerto y planificación de siembras
$routes->get('huerto/plano', 'Plano::index');
$routes->post('huerto/plano/guardar', 'Plano::guardar');
$routes->post('huerto/plano/planificar', 'Plano::planificar');
$routes->post('huerto/plano/planificar/(:num)/eliminar', 'Plano::eliminarPlan/$1');
