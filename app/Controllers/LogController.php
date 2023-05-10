<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\LogErrorModel;
use App\Models\GrupoDifucionModel;


class LogController extends BaseController{

    public function index()
    {
        $session = \Config\Services::session();

        $data = [
            'title' => 'Registro de errores',
            'section' => 'log',
            'session' => $session,
        ];

        return  view('page/parts/head', $data)
        .   view('page/log/log_view')
        .   view('page/parts/footer');
    }

    public function getListLog($page = 1)
    {
        $session = \Config\Services::session();
        $role = $session->get('role');
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $currentPage = $page;

        $logModel = new LogErrorModel();
        $grupoDifucion = new GrupoDifucionModel();
        $campaignModel = new CampaignModel();

        $logs = [];


        if ($role <= 1) {
                $logs = $logModel->where('idEmpresa', $idEmpresa)->orderBy('id', 'DESC')->paginate(10,'default',$currentPage);
            } else {
                
                $difuciones = $grupoDifucion->select('id')->where('idEmpresa', $idEmpresa)->where('created_by', $idUsuario)->findAll();
                $idDifuciones = [];
                foreach ($difuciones as $difucion) {
                    array_push($idDifuciones, $difucion['id']);
                }
                $campaigns = $campaignModel->select('id')->where('idEmpresa', $idEmpresa)->where('created_by', $idUsuario)->findAll();
                $idCampaigns = [];
                foreach ($campaigns as $campaign) {
                    array_push($idCampaigns, $campaign['id']);
                }
                $logs = $logModel->where('idEmpresa', $idEmpresa)->where('tipoOrigen', 1)->whereIn('origenText', $idDifuciones)->orWhere('tipoOrigen', 2)->whereIn('origenText', $idCampaigns)->orderBy('id', 'DESC')->paginate(10,'default',$currentPage);
            }
    
        $returnData = [
                'status' => 200,
                'message' => 'Lista de logs',
                'susses' => true,
                'data' => $logs,
                'pager' => $logModel->pager->getPageCount(),
        ];
        

        return $this->response->setJSON($returnData);
    }

    public function viewLogInfo($index)
    {
            $session = \Config\Services::session();
            $role = $session->get('role');
            $idUsuario = $session->get('idUser');
            $idEmpresa = $session->get('idEmpresa');

            $logModel = new LogErrorModel();
            $grupoDifucion = new GrupoDifucionModel();
            $campaignModel = new CampaignModel();

            $log = $logModel->where('id', $index)->first();

            $data = [
                'fecha' => $log->fecha,
                'mensaje' => $log->mensaje,
                'tipoError' => $log->tipoError
            ];

            if ($log->tipoOrigen == 1 ){
                if ($role <= 1) {
                    $campaign = $campaignModel->where('idEmpresa', $idEmpresa)->withDeleted()->find($log->origenText);
                } else {
                    $campaign = $campaignModel->where('idEmpresa', $idEmpresa)->withDeleted()->where('created_by', $idUsuario)->find($log->origenText);
                }

                if(!$campaign){
                    $response = [
                        'status' => 404,
                        'message' => 'No se encontro la campaña',
                        'susses' => false,
                    ];
                    return $this->response->setJSON($response);
                }

                $data['origen'] = 'Campaña: ' . $campaign->titulo;

                $response = [
                    'status' => 200,
                    'message' => 'Información de log',
                    'susses' => true,
                    'data' => $data,
                ];

                return $this->response->setJSON($response);
            }

            if ($log->tipoOrigen == 2 ){
                if ($role <= 1) {
                    $difucion = $grupoDifucion->where('idEmpresa', $idEmpresa)->withDeleted()->find($log->origenText);
                } else {
                    $difucion = $grupoDifucion->where('idEmpresa', $idEmpresa)->withDeleted()->where('created_by', $idUsuario)->find($log->origenText);
                }

                if(!$difucion){
                    $response = [
                        'status' => 404,
                        'message' => 'No se encontro el grupo de difusión',
                        'susses' => false,
                    ];
                    return $this->response->setJSON($response);
                }

                $data['origen'] = 'Grupo de difusión: ' . $difucion->nombre;

                $response = [
                    'status' => 200,
                    'message' => 'Información de log',
                    'susses' => true,
                    'data' => $data,
                ];

                return $this->response->setJSON($response);
            }

    }
}


?>