<?php

namespace App\Libraries;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Notificaciones implements MessageComponentInterface
{
    public $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "🚀 Servidor de Notificaciones iniciado\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "🟢 Nueva conexión ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "📩 Mensaje recibido: $msg\n";
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "🔴 Conexión cerrada ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "⚠️ Error: {$e->getMessage()}\n";
        $conn->close();
    }
}
