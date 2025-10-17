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

