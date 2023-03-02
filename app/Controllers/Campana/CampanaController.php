<?php

namespace App\Controllers\Campana;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\HTTP\Exceptions\HTTPException;
// use DateTimeImmutable;
use InvalidArgumentException;
// use Ramsey\Uuid\Uuid;

class CampanaController extends BaseController
{
    private $url = "https://api.whatzmeapi.com/";
    private $sendMessage = "own/enviar-mensaje-muchos-contactos";
    private $sendArchive = "own/enviar-archivo-muchos-contactos";


    public function index()
    {
        $session = \Config\Services::session();
        // if ($session->get('logged_in') == FALSE) {
        //     return redirect()->to(base_url('/'));
        // }

        $data = [
            'title' => 'Nueva Campaña',
            'section' => 'campaña',
            'session' => $session
        ];

        return  view('page/parts/head', $data)
            .   view('page/campana/newCampana_view')
            .   view('page/parts/footer');
    }

    /**
     * idMensaje=1 es mensaje con archivo y idMensaje=0 es mensaje solo texto
     * @return ResponseInterface 
     * @throws DataException 
     * @throws HTTPException 
     * @throws InvalidArgumentException 
     */
    public function saveCampaign()
    {
        $session = \Config\Services::session();
        // if ($session->get('logged_in') == FALSE) {
        //     return redirect()->to(base_url('/'));
        // }

        $request = \Config\Services::request();
        $message = $request->getPost('message');
        $titulo = $request->getPost('titulo');
        $idGroups = $request->getPost('groups');
        $adjuntoFile = $request->getFile('adjuntoFile');
        $adjuntoImg = $request->getFile('adjuntoImg');

        $arrayGroups = explode(',', $idGroups);
        $contactos = $this->getContactsByGroups($arrayGroups);
        $dateTimeNow = date('Y-m-d H:i:s');
        $date = date('Ymd');
        $urlArchive = $this->getArchiveAndSave($adjuntoFile, $adjuntoImg, $date);
        $tipoMensaje = 1; //mensaje con archivo
        
        if ($urlArchive == null) {
            $tipoMensaje = 0; //mensaje de solo texto
            $urlArchive = [
                'url' => '',
                'name' => '',
            ];
        }

        $campaignEntity = new \App\Entities\CampaignEntity();
        $campaignModel = new \App\Models\CampaignModel();

        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $token = $session->get('tokenApi');

        if ($idUsuario == null || $idEmpresa == null) {
            $returnData = [
                'status' => 400,
                'message' => 'no se pudo obtener el id del usuario o de la empresa inicie sesión de nuevo',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $campaignEntity->titulo = $titulo;
        $campaignEntity->mensaje = $message;
        $campaignEntity->adjunto = $urlArchive['url'];
        $campaignEntity->tipo = $tipoMensaje;
        $campaignEntity->id_empresa = $idEmpresa; //cambiar por el id de la empresa logueada
        $campaignEntity->fecha_hora = $dateTimeNow;
        $campaignEntity->id_grupos = $idGroups;
        $campaignEntity->totalMensajes = count($contactos);
        $campaignEntity->status = "Creado";
        $campaignEntity->created_by = $idUsuario; //cambiar por el id del usuario logueado

        if (count($contactos) == 0) {
            $returnData = [
                'status' => 400,
                'message' => 'no hay contactos para enviar mensajes',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        if (trim($message) == "" && $urlArchive == "") {
            $returnData = [
                'status' => 400,
                'message' => 'debe enviar un archivo o alguna mensaje',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $dataRes = [];

        if ($tipoMensaje == 1) {
            $dataRes = $this->sendWhatsAppWithFiles($campaignEntity, $contactos,$urlArchive['name'],$token);
        } else {
            $dataRes = $this->sendWhatsAppOnlyText($campaignEntity, $contactos,$token);
        }


        if ($dataRes == null) {
            $returnData = [
                'status' => 400,
                'message' => 'No se pudo enviar la Campaña, por favor intente mas tarde',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $campaignEntity->codido = $dataRes->codigo;
        $campaignEntity->UUID = ($dataRes->id != null) ? $dataRes->id : "";
        $campaignEntity->status = "Enviado";

        $saveResult = $campaignModel->save($campaignEntity);

        if (!$saveResult) {
            $returnData = [
                'status' => 400,
                'message' => 'La Campaña se envió pero no se pudo guardar en la base de datos',
                'susses' => false,
                'data' => []
            ];

            return $this->response->setJSON($returnData);
        }

        $returnData = [
            'status' => 200,
            'message' => 'Campaña enviada con éxito',
            'susses' => true,
            'data' => []
        ];

        return $this->response->setJSON($returnData);
        
    }

    public function getListCampaign($page = 1){
        $campaignModel = new \App\Models\CampaignModel();
        $session = \Config\Services::session();
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $role = $session->get('role');
        $currentPage = $page;

        if ($idUsuario == null || $idEmpresa == null) {
            $returnData = [
                'status' => 400,
                'message' => 'no se pudo obtener el id del usuario o de la empresa inicie sesión de nuevo',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        if($role <= 1){
            $campaigns = $campaignModel->where('id_empresa',$idEmpresa)->paginate(10,'default',$currentPage);
        }else{
            $campaigns = $campaignModel->where('id_empresa',$idEmpresa)->where('created_by',$idUsuario)->paginate(10,'default',$currentPage);
        }

        $returnData = [
            'status' => 200,
            'message' => 'Campañas obtenidas con éxito',
            'susses' => true,
            'data' => $campaigns,
            'pager' => $campaignModel->pager->getPageCount()
        ];

        return $this->response->setJSON($returnData);
    }

    private function getContactsByGroups($arrayGroups)
    {
        $contactoModel = new \App\Models\ContactosModel();
        $contactos = $contactoModel->whereIn('id_grupoDifucion', $arrayGroups)->findAll();
        return $contactos;
    }

    private function getArchiveAndSave($adjuntoFile, $adjuntoImg, $dateTimeData)
    {
        //verificar si hay archivo
        if ($fileA = $adjuntoFile) {
            if ($fileA->isValid() && !$fileA->hasMoved()) {
                $newName = time() . '_' . $fileA->getName();
                $fileA->move(WRITEPATH . 'uploads/' . $dateTimeData, $newName);
                return [
                    'url' => 'viewfile/' . $dateTimeData . "/" . $newName,
                    'name' => $fileA->getName()
                ];
            }
        }

        //verificar si hay imagen
        if ($fileI = $adjuntoImg) {
            if ($fileI->isValid() && !$fileI->hasMoved()) {
                $newName = time() . '_' . $fileI->getName();
                $fileI->move(WRITEPATH . 'uploads/' . $dateTimeData, $newName);
                return [
                    'url' => 'viewfile/' . $dateTimeData . "/" . $newName,
                    'name' => $fileI->getName()
                ];
            }
        }

        return null;
    }

    private function generatedPrivateUUID()
    {
        // $dateTime = new DateTimeImmutable('now');
        // //ignore the namespace
        // $uuid = Uuid::uuid7($dateTime);
        // return $uuid->toString();
    }

    private function arrayContactos($contactos)
    {
        $arrayContactos = [];
        foreach ($contactos as $contacto) {
            $arrayContactos[] = $contacto->lada . $contacto->telefono;
        }
        return $arrayContactos;
    }

    private function sendWhatsAppWithFiles($data, $contactos, $name, $token)
    {    
        $urlArchivo = base_url()."/".$data->adjunto;
        $urlSend = $this->url . $this->sendArchive . '?token=' . $token;
        $arrayContacto = $this->arrayContactos($contactos);
        $curl = curl_init();
        $dataSend = array(
            "numeros" =>$arrayContacto,
            "url" => $urlArchivo,
            "nombrearchivo" => $name,
            "textoimagen" => $data->mensaje,
            "nombreCampania" => $data->titulo
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlSend,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dataSend,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              )
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        log_message("error",$response);

        if (!curl_errno($curl)){
            switch ($status){
                case 200:
                    $response = json_decode($response);
                    if ($response->exito){
                        curl_close($curl);
                        return $response;
                    }else{
                        curl_close($curl);
                        return null;
                    }
                default:
                    curl_close($curl);
                    return null;
            }
        }

    }

    private function sendWhatsAppOnlyText($data, $contactos,$token)
    {
        $urlSend = $this->url . $this->sendMessage . '?token=' . $token;
        $arrayContacto = $this->arrayContactos($contactos);
        $dataSend = [
            'numeros' =>$arrayContacto,
            'mensaje' => $data->mensaje,
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlSend,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dataSend,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (!curl_errno($curl)){
            switch ($status){
                case 200:
                    $response = json_decode($response);
                    if ($response->exito){
                        curl_close($curl);
                        return $response;
                    }else{
                        curl_close($curl);
                        return null;
                    }
                default:
                    curl_close($curl);
                    return null;
            }
        }
    }

    public function deleteCampaign()
    {   
        $session = \Config\Services::session();
        $idEmpresa = $session->get('idEmpresa');
        $idUsuario = $session->get('idUser');
        $idCampaign = $this->request->getVar('idCampaign');
        $role = $this->request->getVar('role');

        $campaignModel = new \App\Models\CampaignModel();

        if($role <= 1){
            $campaign = $campaignModel->where('id_empresa',$idEmpresa)->where('id',$idCampaign)->first();
        }else{
            $campaign = $campaignModel->where('id_empresa',$idEmpresa)->where('id',$idCampaign)->where('created_by',$idUsuario)->first();
        }

        if($campaign){
            $campaignModel->delete($idCampaign);
            $returnData = [
                'status' => 200,
                'message' => 'Campaña eliminada con éxito',
                'susses' => true,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }else{
            $returnData = [
                'status' => 400,
                'message' => 'No se encontró la campaña',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }
    }

}
