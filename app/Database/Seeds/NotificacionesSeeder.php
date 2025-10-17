<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificacionesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'cliente'   => 'Cliente A',
                'problema'  => 'No funciona el servidor',
                'fechahora' => '2025-10-17 10:00:00',
                'estado'    => 'pendiente',
            ],
            [
                'cliente'   => 'Cliente B',
                'problema'  => 'Error en la aplicación',
                'fechahora' => '2025-10-16 14:30:00',
                'estado'    => 'resuelto',
            ],
            [
                'cliente'   => 'Cliente C',
                'problema'  => 'Falla en la red',
                'fechahora' => '2025-10-15 08:15:00',
                'estado'    => 'pendiente',
            ],
        ];

        $this->db->table('notificaciones')->insertBatch($data);
    }
}
