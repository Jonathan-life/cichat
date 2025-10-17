<?php
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server as ReactServer;
use React\Socket\ConnectionInterface as ReactConn;

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Libraries/Notificaciones.php';

$loop = Factory::create();
$notiApp = new App\Libraries\Notificaciones(); // ✅ Namespace corregido

// Servidor WebSocket (para navegadores)
$webSock = new ReactServer('0.0.0.0:8080', $loop);
$webServer = new IoServer(new HttpServer(new WsServer($notiApp)), $webSock, $loop);

// Servidor TCP (para backend)
$tcpServer = new ReactServer('127.0.0.1:8081', $loop);
$tcpServer->on('connection', function (ReactConn $conn) use ($notiApp) {
    $conn->on('data', function ($data) use ($notiApp) {
        echo "📨 Mensaje recibido del backend: $data\n";
        foreach ($notiApp->clients as $client) {
            $client->send($data);
        }
    });
});

echo "✅ Servidor Ratchet corriendo:\n";
echo "   WebSocket -> ws://localhost:8080\n";
echo "   TCP interno -> tcp://127.0.0.1:8081\n";

$loop->run();
