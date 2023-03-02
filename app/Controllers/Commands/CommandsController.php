<?php

namespace App\Controllers\Commands;

use App\Controllers\BaseController;

class CommandsController extends BaseController
{
    public function index()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 1) {
            return redirect()->to(base_url('campaign'));
        }
        
        $data = [
            'title' => 'Lista de Comandos',
            'section' => 'comandos',
            'session' => $session,
        ];

        return  view('page/parts/head', $data)
        .   view('page/command/commandBody_view')
        .   view('page/parts/footer');
    }
}
