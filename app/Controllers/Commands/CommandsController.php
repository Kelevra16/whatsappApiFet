<?php

namespace App\Controllers\Commands;

use App\Controllers\BaseController;
use App\Models\CampaignModel;

class CommandsController extends BaseController
{

    private $url = "https://api.whatzmeapi.com/";
    private $sendMessage = "own/enviar-mensaje";


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

    public function newCommand()
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
        .   view('page/command/newCommand_view')
        .   view('page/parts/footer');
    }


    public function commandAction($idEmpresa = 0){
        $request = \Config\Services::request();

        $posts = $request->getJSON();

        if(!$posts){
            log_message('alert','No se recibieron datos por POST');
        }

        if (!$posts->type ){
            $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
            log_message('alert','Status: '.$postString);
            return;
        }

        switch ($posts->type) {
            case 'message':
                if ($posts->message->fromMe == true){
                    $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
                    log_message('alert','Mensaje propios: '.$postString);
                }else{
                    $this->commandExecution($posts,$idEmpresa);
                }
            break;

            case 'ack':
                $this->updateStatus($posts,$idEmpresa);
            break;

            case 'error':
                $this->statusError($posts,$idEmpresa);
            break;
            
            default:
                $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
                log_message('alert','Status: '.$postString);
            break;
        }


    }

    private function commandUnsubscribe($idEmpresa,$phone,$lada){
        $empresaModel = new \App\Models\EmpresaModel();
        $empresa = $empresaModel->find($idEmpresa);
        $token = $empresa->tokenApi;
        $contactModel = new \App\Models\ContactosModel();
        $grupoDifucionModel = new \App\Models\GrupoDifucionModel();
        $contactList = $contactModel->where('telefono',$phone)->where('lada',$lada)->findAll();

        if(!$contactList){
            $message = "No se encontró una suscripción activa";
            log_message('alert','No se encontró una suscripción activa');
            $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
            return;
        }

        $isDelete = false;
        foreach ($contactList as $contact) {
            $grupoDifucion = $grupoDifucionModel->where('id',$contact->idGrupoDifucion)->where("idEmpresa",$idEmpresa)->first();

            if(!$grupoDifucion){
                log_message('alert','No se encontró el grupo de difusión');
                continue;
            }

            $totalGrupoDifucion = $grupoDifucion->totalContactos - 1;
            $grupoDifucionModel->update($contact->idGrupoDifucion,["totalContactos"=>$totalGrupoDifucion]);
            $isDelete = $contactModel->delete($contact->id);
        }



        if(!$isDelete){
            $message = "Ocurrió un problema al dar de baja la suscripción, inténtalo mas tarde";
            log_message('alert','No se encontró el contacto');
            $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
            return;
        }

        $message = "Se ha eliminado tu suscripción";
        log_message('alert',"Se ha eliminado tu suscripción");
        $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
        return;
    }

    private function commandSubscription($idEmpresa,$phone,$lada,$nombre,$action){

        $empresaModel = new \App\Models\EmpresaModel();
        $empresa = $empresaModel->find($idEmpresa);
        $token = $empresa->tokenApi;
        $idGrupoDifucion = $action->idGrupoDifucion;

        $grupoDifucionModel = new \App\Models\GrupoDifucionModel();
        $grupoDifucion = $grupoDifucionModel->where("id",$idGrupoDifucion)->first();

        if(!$grupoDifucion){
            $message = "No se encontró el grupo de difusión";
            log_message('alert','No se encontró el grupo de difusión');
            $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
            return;
        }

        $contactModel = new \App\Models\ContactosModel();
        $contact = $contactModel->where('telefono',$phone)->where('lada',$lada)->where('idGrupoDifucion',$idGrupoDifucion)->first();

        if($contact){
            $message = "Ya te encuentras Suscrito";
            log_message('alert',"Ya te encuentras Suscrito");
            $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
            return;
        }

        $contactoEntity = new \App\Entities\ContactosEntity();
        $contactoEntity->idGrupoDifucion = $idGrupoDifucion;
        $contactoEntity->nombre = $nombre;
        $contactoEntity->telefono = $phone;
        $contactoEntity->lada = $lada;
        $contactoEntity->created_by = $grupoDifucion->created_by;

        $contactModel->save($contactoEntity);
        $totalGrupoDifucion = $grupoDifucion->totalContactos + 1;
        $grupoDifucionModel->update($idGrupoDifucion,["totalContactos"=>$totalGrupoDifucion]);

        $message = $action->message;
        log_message('alert',"suscrito correctamente " . $message);
        $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
        return;
    }

    private function sendOnlyTextNoResponse($token,$message,$phone,$lada){
        $urlSend = $this->url . $this->sendMessage . '?token=' . $token;
        $numeroCompleto = $lada . $phone;
        $dataSend = [
            'numero' =>$numeroCompleto,
            'mensaje' => $message,
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

        curl_close($curl);

        if ($status != 200) {
            log_message('alert','Error al enviar mensaje: '.$response);
        }

        log_message('alert','Mensaje enviado: '.$response);

        return;

    }

    public function getListCommands($page = 1){
        $commandModel = new \App\Models\CommandModel();
        $session = \Config\Services::session();
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $role = $session->get('role');
        $currentPage = $page;

        if(($idUsuario == null || $idUsuario == "")|| ($idEmpresa == null || $idEmpresa == "")){
            $returnData = [
                'status' => 400,
                'message' => 'No se encontró la sesión',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        if ($role > 1) {
            $returnData = [
                'status' => 400,
                'message' => 'No cuentas con los permisos necesarios',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $commands = $commandModel->where('idEmpresa',$idEmpresa)->paginate(10,'commands',$currentPage);
        $userModel = new \App\Models\UserModel();

        foreach ($commands as $key => $command) {
            $user = $userModel->find($command->created_by);
            $commands[$key]->created_by = $user->username;
        }

        $returnData = [
            'status' => 200,
            'message' => 'Campañas obtenidas con éxito',
            'susses' => true,
            'data' => $commands,
            'pager' => $commandModel->pager->getPageCount()
        ];

        return $this->response->setJSON($returnData);
    }

    public function deleteCommand()
    {   
        $session = \Config\Services::session();
        $idEmpresa = $session->get('idEmpresa');
        $idUsuario = $session->get('idUser');
        $idCommand = $this->request->getVar('idCommand');
        $role = $this->request->getVar('role');

        $commandModel = new \App\Models\CommandModel();

        if($role > 1){
            $returnData = [
                'status' => 400,
                'message' => 'No cuentas con los permisos necesarios',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $command = $commandModel->where('idEmpresa',$idEmpresa)->where('id',$idCommand)->where('created_by',$idUsuario)->first();
        

        if($command){
            $commandModel->delete($idCommand);
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

    private function commandExecution($posts,$idEmpresa){

        if(!$posts->message->text){
            return;
        }

        $comando = html_entity_decode($posts->message->text);

        if($comando == "Baja" || $comando == "baja" || $comando == "BAJA" || $comando == "*BAJA*"){
            $telefonoCompleto = $posts->user->phone;
            $nombre = $posts->user->name;
            $calculateLada = strlen($telefonoCompleto) - 10;
            $lada = substr($telefonoCompleto, 0, $calculateLada);
            $phone = substr($telefonoCompleto, 3, 10);
            $this->commandUnsubscribe($idEmpresa,$phone,$lada);
            return;
        }

        $commandModel = new \App\Models\CommandModel();
        log_message('info','comando: '.$comando);
        $command = $commandModel->where('command',$comando)->where('idEmpresa',$idEmpresa)->first();

        if(!$command){
            log_message('alert','No se encontró el comando: '.$comando);
            $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
            log_message('alert','Sin Comando: '.$postString);
            return;
        }

        $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
        log_message('alert','mensaje de comando: '.$postString);

        switch ($command->typeCommand) {
            case 'Suscribir':
                $telefonoCompleto = $posts->user->phone;
                $nombre = $posts->user->name;
                $calculateLada = strlen($telefonoCompleto) - 10;
                $lada = substr($telefonoCompleto, 0, $calculateLada);
                $phone = substr($telefonoCompleto, 3, 10);
                $action = json_decode($command->actionCommand);
                $this->commandSubscription($idEmpresa,$phone,$lada,$nombre,$action);
                break;
        }
    }

    private function updateStatus($post,$idEmpresa){
        $messageQueueModel = new \App\Models\MessageQueueModel();
        $campaignModel = new CampaignModel();

        foreach ($post->data as $data) {
            $msgId = $data->msgId;
            $ackCode = $data->ackCode;

            $messageQueue = $messageQueueModel->where('idEmpresa',$idEmpresa)->where('msgId',$msgId)->first();

            if(!$messageQueue){
                log_message('alert','No se encontró el mensaje: '.$msgId);
                continue;
            }

            $estatus ="";

            switch ($ackCode) {
                case '1':
                    $estatus = "ENVIADO";
                    break;
                
                case '2':
                    $estatus = "ENTREGADO";
                    break;

                case '3':
                    $estatus = "VISTO";
                    break;

                default:
                    log_message('alert', 'Estatus desconocido: ' . $ackCode);
                    $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
                    log_message('alert','Estatus desconocido: '.$postString);
                    break;
            }

            if ($estatus === $messageQueue->status) {
                continue;
            }

            if ($messageQueue->status === "VISTO") {
                log_message('alert', 'No se puede cambiar el estatus de un mensaje visto');
                continue;
            }

            if ($messageQueue->status === "ENTREGADO" && $estatus === "ENVIADO") {
                log_message('alert', 'No se puede cambiar el estatus de un mensaje entregado a enviado');
                continue;
            }


            $campaign = $campaignModel->find($messageQueue->idCampaign);

            if ($estatus === "ENVIADO") {
                $campaign->totalEnviado = $campaign->totalEnviado + 1;
            } elseif ($estatus === "ENTREGADO") {
                $campaign->totalEntregado = $campaign->totalEntregado + 1;
            } elseif ($estatus === "VISTO") {
                $campaign->totalVisto = $campaign->totalVisto + 1;
            }

            if ($messageQueue->status !== $estatus) {
                if ($messageQueue->status === "ENVIADO") {
                    $campaign->totalEnviado = $campaign->totalEnviado - 1;
                } elseif ($messageQueue->status === "ENTREGADO" && $estatus === "VISTO") {
                    $campaign->totalEntregado = $campaign->totalEntregado - 1;
                }
            }

            $messageQueue->status = $estatus;
            $messageQueueModel->save($messageQueue);
            $campaignModel->save($campaign);
            $postString = json_encode($post, JSON_UNESCAPED_UNICODE);
            log_message('alert', 'Mensaje actualizado: ' . $postString);



            // if ($messageQueue->status == "PENDIENTE"){
            //     $messageQueue->status = $estatus;
            //     $messageQueueModel->save($messageQueue);
            //     $campaign = $campaignModel->find($messageQueue->idCampaign);
            //     $campaign->totalEnviado = $campaign->totalEnviado + 1;
            //     $campaignModel->save($campaign);
            //     $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
            //     log_message('alert','Mensaje actualizado: '.$postString);
            // }else{

            //     switch ($messageQueue->status) {
            //         case 'ENVIADO':
            //             $messageQueue->status = $estatus;
            //             $messageQueueModel->save($messageQueue);
            //             $campaign = $campaignModel->find($messageQueue->idCampaign);
            //             $campaign->totalEnviado = $campaign->totalEnviado - 1;

            //             switch ($estatus) {
            //                 case 'ENTREGADO':
            //                     $campaign->totalEntregado = $campaign->totalEntregado + 1;
            //                     break;
                            
            //                 case 'VISTO':
            //                     $campaign->totalVisto = $campaign->totalVisto + 1;
            //                     break;
            //             }
                        
            //             $campaignModel->save($campaign);
            //             $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
            //             log_message('alert','Mensaje actualizado: '.$postString);
                        
            //             break;

            //         case 'ENTREGADO':
            //             if($estatus == "VISTO"){
            //                 $messageQueue->status = $estatus;
            //                 $messageQueueModel->save($messageQueue);
            //                 $campaign = $campaignModel->find($messageQueue->idCampaign);
            //                 $campaign->totalEntregado = $campaign->totalEntregado - 1;
            //                 $campaign->totalVisto = $campaign->totalVisto + 1;
            //                 $campaignModel->save($campaign);
            //                 $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
            //                 log_message('alert','Mensaje actualizado: '.$postString);
            //             }
            //             break;
            //     }

            // }



        }
    }

    private function statusError($post,$idEmpresa){
        $messageQueueModel = new \App\Models\MessageQueueModel();
        $campaignModel = new CampaignModel();

        if (!$post->data) {
            $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
            log_message('alert','No se encontró los datos: '.$postString);
            return;
        }

        $msgId = $post->data->id;

        $messageQueue = $messageQueueModel->where('idEmpresa',$idEmpresa)->where('msgId',$msgId)->where('status','PENDIENTE')->first();

        if($messageQueue){
            $messageQueue->status = "ERROR";
            $messageQueue->lastError = $post->message;
            $messageQueueModel->save($messageQueue);
            $campaign = $campaignModel->find($messageQueue->idCampaign);
            $campaign->totalError = $campaign->totalError + 1;
            $campaignModel->save($campaign);
            $this->logErrorContact($post->message,$messageQueue);
            $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
            log_message('alert','Mensaje actualizado: '.$postString);
        }else{
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $string = $post->data->to_number;
            $parts = explode("@", $string);
            $telefono = $parts[0];
            $swissNumberStr = '+' . $telefono;
            $swissNumberProto = $phoneUtil->parse($swissNumberStr);
            $telefono = $swissNumberProto->getNationalNumber();
            $messageQueue2 = $messageQueueModel->where('idEmpresa',$idEmpresa)->where('phone',$telefono)->where('status','PENDIENTE')->first();

            if (!$messageQueue2) {
                $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
                log_message('alert','No se encontró el mensaje: '.$postString);
                return;
            }

            $messageQueue2->status = "ERROR";
            $messageQueue2->lastError = $post->message;
            $messageQueueModel->save($messageQueue2);
            $campaign = $campaignModel->find($messageQueue2->idCampaign);
            $campaign->totalError = $campaign->totalError + 1;
            $campaignModel->save($campaign);
            $this->logErrorContact($post->message,$messageQueue2);
            $postString = json_encode($post,JSON_UNESCAPED_UNICODE);
            log_message('alert','Mensaje actualizado: '.$postString);
        }


    }


    private function logErrorContact($mensajeError,$messageQueue){
        $logErrorEntity = new \App\Entities\LogErrorEntity();
        $logErrorModel = new \App\Models\LogErrorModel();
        $campaignModel = new CampaignModel();
        $campaignInfo = $campaignModel->where("id", $messageQueue->idCampaign)->first();

        $logErrorEntity->idEmpresa = $messageQueue->idEmpresa;
        $logErrorEntity->tipoOrigen = 1;
        $logErrorEntity->origenText = $messageQueue->idCampaign;
        $logErrorEntity->fecha = date("Y-m-d H:i:s");
        $logErrorEntity->mensaje = "se ha producido un error al enviar la campaña ".$campaignInfo->titulo." con el id ".$campaignInfo->id." al numero ".$messageQueue->phone." Error: ".$mensajeError;
        $logErrorEntity->tipoError = "ERROR";
        $logErrorEntity->visto = 0;

        $logErrorModel->save($logErrorEntity);

    }
}
