<?php

namespace App\Controllers;
use App\Controllers\BaseController;

class WebSocketController extends BaseController{

  //Desplegar la vista
  public function index() {
    return view('websocket/chat');
  }

  //API para obtener información del servidor (ONLINE / OFFLINE)
  public function serverStatus(){
    return $this->response->setJSON([
      'status'    => 'active',
      'server'    => 'ws://localhost:8080',
      'timestamp' => time()
    ]);
  }

}