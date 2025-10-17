<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class NotificacionesController extends BaseController
{
    public function index()
    {
        return view('notificaciones/listar');
    }

    public function crear()
    {
        // Si hay mensaje en sesión, lo pasamos a la vista
        $session = session();
        $msg = $session->getFlashdata('msg');
        return view('notificaciones/crear', ['msg' => $msg]);
    }

    public function guardar()
    {
        $cliente   = $this->request->getPost('cliente');
        $problema  = $this->request->getPost('problema');
        $fechahora = $this->request->getPost('fechahora');
        $estado    = $this->request->getPost('estado');

        // 💾 Guardar en la base de datos
        $db = \Config\Database::connect();
        $builder = $db->table('notificaciones');
        $builder->insert([
            'cliente' => $cliente,
            'problema' => $problema,
            'fechahora' => $fechahora,
            'estado' => $estado
        ]);

        // 🧠 Armar mensaje JSON
        $data = [
            'type'      => 'notificacion',
            'cliente'   => $cliente,
            'problema'  => $problema,
            'fechahora' => $fechahora,
            'estado'    => $estado
        ];

        // 🚀 Enviar al WebSocket (TCP)
        $socket = @stream_socket_client('tcp://127.0.0.1:8081', $errno, $errstr, 2);
        if ($socket) {
            fwrite($socket, json_encode($data));
            fclose($socket);
        } else {
            log_message('error', "❌ No se pudo enviar al WebSocket: $errstr ($errno)");
        }

        // ✅ En lugar de redirigir, recargamos la misma vista con mensaje
        return redirect()
            ->back()
            ->with('msg', '✅ Notificación creada y enviada correctamente.');
    }
}
