<?php

namespace App\Controllers\Campaign;

use App\Controllers\BaseController;
use App\Controllers\ApiWhatsApp\ApiWhatsAppController;
use App\Models\MessageQueueModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\HTTP\Exceptions\HTTPException;
// use DateTimeImmutable;
use InvalidArgumentException;
// use Ramsey\Uuid\Uuid;

class CampaignController extends BaseController
{

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
            .   view('page/campaign/newCampaign_view')
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
        $tipoMensaje = "";
        
        if ($urlArchive === null) {
            $tipoMensaje = "TEXTO"; //mensaje de solo texto
            $urlArchive = [
                'url' => '',
                'name' => '',
                'type' => ''
            ];
        }else{
            $tipoMensaje = $urlArchive['type']; //mensaje con archivo
        }

        $campaignEntity = new \App\Entities\CampaignEntity();
        $campaignModel = new \App\Models\CampaignModel();

        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $token = $session->get('tokenApi');

        if ($idUsuario == null || $idEmpresa == null) {
            $returnData = [
                'status' => 400,
                'message' => 'no se pudo comprobar la sesión, inicie sesión de nuevo',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $totalContact = count($contactos);
        $messageQueueModel = new MessageQueueModel();
        $total = $messageQueueModel->select("SUM(CASE WHEN status = 'CREADO' THEN 1 ELSE 0 END) + SUM(CASE WHEN status = 'PROGRAMADO' AND DATE(scheduledAt) <= CURDATE() THEN 1 ELSE 0 END) + SUM(CASE WHEN status NOT IN ('CREADO', 'PROGRAMADO') AND DATE(sentAt) = CURDATE() THEN 1 ELSE 0 END) AS Total")->first();
        $totalLimite = $total->Total + $totalContact;

        if ($totalLimite > 6000) {
            $returnData = [
                'status' => 400,
                'message' => 'Has alcanzado el máximo numero de mensajes por dia',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        if ($totalContact == 0) {
            $returnData = [
                'status' => 400,
                'message' => 'No hay contactos para enviar mensajes',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        if (trim($message) == "" && $urlArchive == "") {
            $returnData = [
                'status' => 400,
                'message' => 'Debes agregar un archivo o alguna mensaje',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $campaignEntity->titulo = $titulo;
        $campaignEntity->mensaje = $message;
        $campaignEntity->adjunto = $urlArchive['url'];
        $campaignEntity->messageType = $tipoMensaje;
        $campaignEntity->idEmpresa = $idEmpresa;
        $campaignEntity->idGrupos = $idGroups;
        $campaignEntity->totalMensajes = $totalContact;
        $campaignEntity->status = "PENDIENTE";
        $campaignEntity->codigo = 0;
        $campaignEntity->created_by = $idUsuario;
        $campaignEntity->dateSend = $dateTimeNow;

        $saveResult = $campaignModel->save($campaignEntity);

        if (!$saveResult) {
            $returnData = [
                'status' => 400,
                'message' => 'No se pudo guardar la campaña, intente de nuevo mas tarde',
                'susses' => false,
                'data' => []
            ];

            return $this->response->setJSON($returnData);
        }

        $idCampaign = $campaignModel->insertID();

        $dataRes = [];

        if ($tipoMensaje != "TEXTO") {
            $dataRes = $this->sendWhatsAppWithFiles($campaignEntity, $contactos,$idCampaign,$tipoMensaje);
        } else {
            $dataRes = $this->sendWhatsAppOnlyText($campaignEntity, $contactos,$idCampaign,$tipoMensaje);
        }


        if ($dataRes['error'] === true) {
            $campaignModel->where('id', $idCampaign)->set(['status' => 'ERROR'])->update();
            $campaignModel->delete($idCampaign,true);

            $returnData = [
                'status' => 400,
                'message' => $dataRes['message'],
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $returnData = [
            'status' => 200,
            'message' => $dataRes['message'],
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
            $campaigns = $campaignModel->where('idEmpresa',$idEmpresa)->orderBy('id', 'desc')->paginate(10,'default',$currentPage);
        }else{
            $campaigns = $campaignModel->where('idEmpresa',$idEmpresa)->where('created_by',$idUsuario)->orderBy('id', 'desc')->paginate(10,'default',$currentPage);
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
        $contactos = $contactoModel->whereIn('idGrupoDifucion', $arrayGroups)->findAll();
        return $contactos;
    }

    private function getArchiveAndSave($adjuntoFile, $adjuntoImg, $dateTimeData)
    {
        //verificar si hay archivo
        if ($fileA = $adjuntoFile) {
            if ($fileA->isValid() && !$fileA->hasMoved()) {
                $newName = time() . '_' . htmlentities($fileA->getName());
                $fileA->move(WRITEPATH . 'uploads/' . $dateTimeData, $newName);
                return [
                    'url' => 'viewfile/' . $dateTimeData . "/" . $newName,
                    'name' => $fileA->getName(),
                    'type' => 'ARCHIVO'
                ];
            }
        }

        //verificar si hay imagen
        if ($fileI = $adjuntoImg) {
            if ($fileI->isValid() && !$fileI->hasMoved()) {
                $newName = time() . '_' . htmlentities($fileI->getName());
                $fileI->move(WRITEPATH . 'uploads/' . $dateTimeData, $newName);
                return [
                    'url' => 'viewfile/' . $dateTimeData . "/" . $newName,
                    'name' => $fileI->getName(),
                    'type' => 'IMAGEN'
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

    /**
     * 
     * @param mixed $campaign 
     * @param mixed $contactos 
     * @param int $idCampaign 
     * @param string $tipoMensaje 
     * @return mixed 
     * @throws HTTPException 
     * @throws InvalidArgumentException 
     */
    private function sendWhatsAppWithFiles($campaign, $contactos, $idCampaign,$tipoMensaje)
    {    
        $urlArchivo = base_url()."/".$campaign->adjunto;
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        $messageQueueModel = new \App\Models\MessageQueueModel();
        $data = [];
        $total = 0;
        $error = 0;

        try {
            foreach ($contactos as $contacto) {

                try {
                    $swissNumberStr = '+' . $contacto->lada . $contacto->telefono;
                    $swissNumberProto = $phoneUtil->parse($swissNumberStr);
                    $isValid = $phoneUtil->isValidNumber($swissNumberProto);
                    if (!$isValid) {
                        $error++;
                        log_message('error', 'Error al enviar mensaje a: ' . $contacto->telefono . ' id: ' . $contacto->id);
                        $contactoModel = new \App\Models\ContactosModel();
                        $grupoDifucionModel = new \App\Models\GrupoDifucionModel();
                        $grupoDifucion = $grupoDifucionModel->find($contacto->idGrupoDifucion);
                        $grupoDifucion->totalContactos = $grupoDifucion->totalContactos - 1;
                        $grupoDifucionModel->save($grupoDifucion);
                        $contactoModel->delete($contacto->id);
                        continue;
                    }
                   
                    $region = $phoneUtil->getRegionCodeForNumber($swissNumberProto);

                    if ($region == 'MX') {
                        if ($contacto->lada != '521') {
                            $contactoModel = new \App\Models\ContactosModel();
                            $contacto->lada = '521';
                            $contactoModel->save($contacto);
                        }
                    }

                } catch (\libphonenumber\NumberParseException $e) {
                    $error++;
                    log_message('error', $e->getMessage());
                    $contactoModel = new \App\Models\ContactosModel();
                    $grupoDifucionModel = new \App\Models\GrupoDifucionModel();
                    $grupoDifucion = $grupoDifucionModel->find($contacto->idGrupoDifucion);
                    $grupoDifucion->totalContactos = $grupoDifucion->totalContactos - 1;
                    $grupoDifucionModel->save($grupoDifucion);
                    $contactoModel->delete($contacto->id);
                    continue;
                }

                $dataSend = [
                        'numero' =>$contacto->lada . $contacto->telefono,
                        'url' => $urlArchivo,
                        'textoimagen' => $campaign->mensaje
                ];
    
                $dataSendStr = json_encode($dataSend,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
                $meQueEntity = new \App\Entities\MessageQueueEntity();
                $meQueEntity->idContact = $contacto->id;
                $meQueEntity->phone = $contacto->telefono;
                $meQueEntity->idEmpresa = $campaign->idEmpresa;
                $meQueEntity->idCampaign = $idCampaign;
                $meQueEntity->messageJson = $dataSendStr;
                $meQueEntity->msgId = 0;
                $meQueEntity->status = "CREADO";
                $meQueEntity->sentAt = NULL;
                $meQueEntity->sheduledAt = NULL;
                $meQueEntity->created_by = $campaign->created_by;
                $meQueEntity->retryCount = 0;
                $meQueEntity->lastError = NULL;
                $meQueEntity->messageType = $tipoMensaje;
    
                $data[] = $meQueEntity;
    
                if(count($data) == 500){
                    $insertFiles = $messageQueueModel->insertBatch($data);
                    if (!$insertFiles) {
                        $error += count($data);
                        //log de error al insertar
                    }else{
                        $total += $insertFiles;
                        if (count($data) != $insertFiles) {
                            $error += count($data) - $insertFiles;
                            //log de error al insertar
                        }
                    }
                    $data = [];
                }
                
            }
    
            if (count($data) > 0) {
                $insertFiles = $messageQueueModel->insertBatch($data);
                if (!$insertFiles) {
                    $error += count($data);
                    //log de error al insertar
                }else{
                    $total += $insertFiles;
                    if (count($data) != $insertFiles) {
                        $error += count($data) - $insertFiles;
                        //log de error al insertar
                    }
                }
                $data = [];
            }


            if($error > 0 && $error < count($contactos)){
                return [
                    'message' => "Se agregaron $total mensajes a la cola de envió, pero $error no se pudieron guardar",
                    'susses' => true,
                    'error' => true
                ];
            }elseif($error == 0 && $total == count($contactos)){
                return [
                    'message' => "Se agregaron $total mensajes a la cola de envió",
                    'susses' => true,
                    'error' => false
                ];
            }else{
                $messageQueueModel->where('idCampaign',$idCampaign)->delete();
                return [
                    'message' => "Hubo un error al agregar los mensajes a la cola de envió",
                    'susses' => false,
                    'error' => true
                ];
            }

        } catch (\Throwable $th) {
            $messageQueueModel->where('idCampaign',$idCampaign)->delete();
            log_message('error', $th->getMessage());
            return [
                'message' => "ocurrió un error al agregar a la cola de envió",
                'susses' => false,
                'error' => true
            ];
        }


    }

    /**
     * 
     * @param mixed $campaign 
     * @param mixed $contactos 
     * @param int $idCampaign 
     * @param string $tipoMensaje 
     * @return mixed
     */
    private function sendWhatsAppOnlyText($campaign, $contactos,$idCampaign,$tipoMensaje)
    {
        $messageQueueModel = new \App\Models\MessageQueueModel();
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $data = [];
        $total = 0;
        $error = 0;

        try {
            foreach ($contactos as $contacto) {

                try {
                    $swissNumberStr = '+' . $contacto->lada . $contacto->telefono;
                    $swissNumberProto = $phoneUtil->parse($swissNumberStr);
                    $isValid = $phoneUtil->isValidNumber($swissNumberProto);
                    if (!$isValid) {
                        $error++;
                        $contactoModel = new \App\Models\ContactosModel();
                        $grupoDifucionModel = new \App\Models\GrupoDifucionModel();
                        $grupoDifucion = $grupoDifucionModel->find($contacto->idGrupoDifucion);
                        $grupoDifucion->totalContactos = $grupoDifucion->totalContactos - 1;
                        $grupoDifucionModel->save($grupoDifucion);
                        $contactoModel->delete($contacto->id);
                        continue;
                    }
                   
                    $region = $phoneUtil->getRegionCodeForNumber($swissNumberProto);

                    if ($region == 'MX') {
                        if ($contacto->lada != '521') {
                            $contactoModel = new \App\Models\ContactosModel();
                            $contacto->lada = '521';
                            $contactoModel->save($contacto);
                        }
                    }

                } catch (\libphonenumber\NumberParseException $e) {
                    $error++;
                    $contactoModel = new \App\Models\ContactosModel();
                    $grupoDifucionModel = new \App\Models\GrupoDifucionModel();
                    $grupoDifucion = $grupoDifucionModel->find($contacto->idGrupoDifucion);
                    $grupoDifucion->totalContactos = $grupoDifucion->totalContactos - 1;
                    $grupoDifucionModel->save($grupoDifucion);
                    $contactoModel->delete($contacto->id);
                    continue;
                }


            $dataSend = [
                    'numero' =>$contacto->lada . $contacto->telefono,
                    'mensaje' => $campaign->mensaje,
            ];

            $dataSendStr =  json_encode($dataSend,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $meQueEntity = new \App\Entities\MessageQueueEntity();
            $meQueEntity->idContact = $contacto->id;
            $meQueEntity->phone = $contacto->telefono;
            $meQueEntity->idEmpresa = $campaign->idEmpresa;
            $meQueEntity->idCampaign = $idCampaign;
            $meQueEntity->messageJson = $dataSendStr;
            $meQueEntity->msgId = 0;
            $meQueEntity->status = "CREADO";
            $meQueEntity->sentAt = NULL;
            $meQueEntity->sheduledAt = NULL;
            $meQueEntity->created_by = $campaign->created_by;
            $meQueEntity->retryCount = 0;
            $meQueEntity->lastError = NULL;
            $meQueEntity->messageType = $tipoMensaje;

            $data[] = $meQueEntity;

            if(count($data) == 500){
                $insertFiles = $messageQueueModel->insertBatch($data);
                if (!$insertFiles) {
                    $error += count($data);
                    //log de error al insertar
                }else{
                    $total += $insertFiles;
                    if (count($data) != $insertFiles) {
                        $error += count($data) - $insertFiles;
                        //log de error al insertar
                    }
                }
                $data = [];
            }
        }

        if (count($data) > 0) {
            $insertFiles = $messageQueueModel->insertBatch($data);
            if (!$insertFiles) {
                $error += count($data);
                //log de error al insertar
            }else{
                $total += $insertFiles;
                if (count($data) != $insertFiles) {
                    $error += count($data) - $insertFiles;
                    //log de error al insertar
                }
            }
            $data = [];
        }

        if($error > 0 && $error < count($contactos)){
            return [
                'message' => "Se agregaron $total mensajes a la cola de envió, pero $error no se pudieron guardar",
                'susses' => true,
                'error' => true
            ];
        }elseif($error == 0 && $total == count($contactos)){
            return [
                'message' => "Se agregaron $total mensajes a la cola de envió",
                'susses' => true,
                'error' => false
            ];
        }else{
            $messageQueueModel->where('idCampaign',$idCampaign)->delete();
            return [
                'message' => "Hubo un error al agregar los mensajes a la cola de envió",
                'susses' => false,
                'error' => true
            ];
        }

        } catch (\Throwable $th) {
            $messageQueueModel->where('idCampaign',$idCampaign)->delete();
            log_message('error', $th->getMessage());
            return [
                'message' => "ocurrió un error al agregar a la cola de envió",
                'susses' => false,
                'error' => true
            ];
        }

    }

    private function saveMessageQueueUni($idCampaign,$phone,$tipoMensaje,$idContacto,$idEmpresa,$created_by,$dataSendStr){
        $messageQueueModel = new \App\Models\MessageQueueModel();
        $meQueEntity = new \App\Entities\MessageQueueEntity();

        $meQueEntity->idContact = $idContacto;
        $meQueEntity->phone = $phone;
        $meQueEntity->idEmpresa = $idEmpresa;
        $meQueEntity->idCampaign = $idCampaign;
        $meQueEntity->messageJson = $dataSendStr;
        $meQueEntity->msgId = 0;
        $meQueEntity->status = "CREADO";
        $meQueEntity->sentAt = NULL;
        $meQueEntity->sheduledAt = NULL;
        $meQueEntity->created_by = $created_by;
        $meQueEntity->retryCount = 0;
        $meQueEntity->lastError = NULL;
        $meQueEntity->messageType = $tipoMensaje;

        $insertFiles = $messageQueueModel->insert($meQueEntity);

        if (!$insertFiles) {
            return false;
        }else{
            return true;
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
            $campaign = $campaignModel->where('idEmpresa',$idEmpresa)->where('id',$idCampaign)->first();
        }else{
            $campaign = $campaignModel->where('idEmpresa',$idEmpresa)->where('id',$idCampaign)->where('created_by',$idUsuario)->first();
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
