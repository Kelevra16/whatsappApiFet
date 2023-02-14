<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $session = \Config\Services::session();
        if ($session->get('logged_in') == FALSE) {
            return redirect()->to(base_url('/'));
        }

        $data = [
            'title' => 'Campañas',
            'section' => 'campaña',
            'session' => $session,
            'mensaje' => $this->obtenerMensajeHora(),
        ];

        return  view('page/parts/head', $data)
            .   view('page/campana/campanaBody_view')
            .   view('page/parts/footer');
    }



    function obtenerMensajeHora() {
        date_default_timezone_set('America/Mexico_City');
        $horaActual = date("G");
      
        if ($horaActual >= 6 && $horaActual < 12) {
          return "Buenos días";
        } else if ($horaActual >= 12 && $horaActual < 18) {
          return "Buenas tardes";
        } else if ($horaActual >= 18 || $horaActual < 6) {
          return "Buenas noches";
        }
      }
      
}
