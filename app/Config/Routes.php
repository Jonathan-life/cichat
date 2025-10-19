<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//Desplegar la vista
$routes->get('websocket', 'WebSocketController::index');


$routes->get('/notificaciones', 'NotificacionesController::index');


$routes->get('/notificaciones/crear', 'NotificacionesController::crear');
$routes->post('/notificaciones/guardar', 'NotificacionesController::guardar');

$routes->get('/notificaciones/editar/(:num)', 'NotificacionesController::editar/$1');
$routes->post('/notificaciones/actualizar/(:num)', 'NotificacionesController::actualizar/$1');


$routes->get('/notificaciones/eliminar/(:num)', 'NotificacionesController::eliminar/$1');


$routes->get('/notificaciones/listar', 'NotificacionesController::listar');
$routes->get('/api/notificaciones', 'NotificacionesController::listarTodas');


