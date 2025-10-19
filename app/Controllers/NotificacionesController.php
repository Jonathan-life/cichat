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

        // 🆔 Obtener el ID insertado
        $id = $db->insertID();

        // 🧠 Armar mensaje JSON con ID incluido
        $data = [
            'type'      => 'notificacion',
            'id'        => $id,
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

        return redirect()
            ->back()
            ->with('msg', '✅ Notificación creada y enviada correctamente.');
    }

    public function editar($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('notificaciones');
        $notificacion = $builder->where('id', $id)->get()->getRow();

        if (!$notificacion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Notificación no encontrada');
        }

        return view('notificaciones/editar', ['notificacion' => $notificacion]);
    }

    public function actualizar($id)
    {
        $cliente   = $this->request->getPost('cliente');
        $problema  = $this->request->getPost('problema');
        $fechahora = $this->request->getPost('fechahora');
        $estado    = $this->request->getPost('estado');

        $db = \Config\Database::connect();
        $builder = $db->table('notificaciones');

        $builder->where('id', $id)->update([
            'cliente' => $cliente,
            'problema' => $problema,
            'fechahora' => $fechahora,
            'estado' => $estado
        ]);

        return redirect()->to('/notificaciones')->with('msg', '✅ Notificación actualizada correctamente.');
    }

    // 🔍 Endpoint para el API (listar todas)
    public function listarTodas()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('notificaciones');
        $notificaciones = $builder->orderBy('id', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($notificaciones);
    }
}
