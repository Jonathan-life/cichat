<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificaciones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cliente'     => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'problema'    => [
                'type' => 'TEXT',
            ],
            'fechahora'   => [
                'type' => 'DATETIME',
            ],
            'estado'      => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'pendiente',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('notificaciones');
    }

    public function down()
    {
        $this->forge->dropTable('notificaciones');
    }
}
