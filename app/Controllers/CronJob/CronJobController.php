<?php

namespace App\Controllers\CronJob;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\EmpresaModel;
use App\Models\MessageQueueModel;
use App\Controllers\ApiWhatsApp\ApiWhatsAppController as ApiWhatsApp;

class CronJobController extends BaseController{

    private $api;

    public function cronJobStart(){
        $messageQueueModel = new MessageQueueModel();

        $starDateJob = date("Y-m-d H:i:s");

        $totalPendientes = $messageQueueModel->where("status", "PENDIENTE")->countAllResults();

        if ($totalPendientes > 0) {
            log_message('alert', 'Hay mensajes con estatus pendientes');
            return;
        }

        $totalEstEnviado = $messageQueueModel->where("status", "ENVIADO")->where("DATE(sentAt)", "CURDATE()")->countAllResults();

        if($totalEstEnviado > 400){
            log_message('alert', 'se alcanzo el limite de mensajes con estatus enviado por dia, toca esperar hasta que se liberen los mensajes');
            return;
        }

        $total = $messageQueueModel->select("SUM(CASE WHEN DATE(sentAt) = CURDATE() THEN 1 ELSE 0 END) AS Total")->first();

        $totalPorEnviar = $total->Total + 100;

        if ($totalPorEnviar > 6000) {
            log_message('alert', 'Se ha alcanzado el limite de mensajes enviados por dia');
            return;	
        }

        $messageQueues = $messageQueueModel->where("status", "CREADO")->orWhere("status = 'PROGRAMADO' AND sentAt <= NOW()")->orderBy("status", "DESC")->orderBy("sentAt", "ASC")->findAll(100);

        if(!$messageQueues){
            log_message('alert', 'No hay mensajes por enviar');
            return;
        }

        $this->sendMessage($messageQueues);

        $endDateJob = date("Y-m-d H:i:s");

        $tiempoEjecucion = strtotime($endDateJob) - strtotime($starDateJob);

        return $this->response->setJSON([
            "status" => "success",
            "message" => "Se han enviado los mensajes",
            "tiempoEjecucion" => $tiempoEjecucion
        ]);

    }

    public function prueba(){
        log_message('alert', 'prueba job cron');
    }

    private function sendMessage($messageQueues){
        $messageQueueModel = new MessageQueueModel();
        $empresaModel = new EmpresaModel();
        $campaignModel = new CampaignModel();

        $this->api = new ApiWhatsApp();

        $serviceEstatus = $this->api->getEstadoInstancia('qwerefgt84jkyj');

        if($serviceEstatus){
            if ($serviceEstatus->exito === true && $serviceEstatus->estatus->logueado === false) {
                log_message('alert', 'No se ha podido iniciar sesión en la instancia se necesita escanear el codigo QR');
                return;
            }

            if ($serviceEstatus->exito === false) {
                log_message('alert', 'No se ha podido iniciar sesión en la instancia');
                return;
            }
        }

        foreach ($messageQueues as $messageQueue) {
            $token = $empresaModel->select("tokenApi")->where("id", $messageQueue->idEmpresa)->first();

            $campaign = $campaignModel->where("id", $messageQueue->idCampaign)->first();

            if($campaign->status == "ERROR"){
                $messageQueue->lastError = "La campaña se encuentra en estado de error";
                $messageQueue->status = "ERROR";
                $messageQueue->sentAt = date("Y-m-d H:i:s");
                $messageQueueModel->save($messageQueue);
                continue;
            }

            $respuesta = false;

            switch ($messageQueue->messageType) {
                case 'TEXTO':
                    $respuesta = $this->api->sendWhatsAppText($messageQueue->messageJson, $token->tokenApi);
                    break;

                case 'IMAGEN':
                    $respuesta = $this->api->sendWhatsAppImage($messageQueue->messageJson, $token->tokenApi);
                    break;

                case 'ARCHIVO':
                    $respuesta = $this->api->sendWhatsAppImage($messageQueue->messageJson, $token->tokenApi);
                    break;
            }

            if(!$respuesta){
                $messageQueue->lastError = "Error al enviar el mensaje";
                $messageQueue->retryCount = $messageQueue->retryCount + 1;
                $messageQueueModel->save($messageQueue);
                $this->logErrorContact("El servicio ha marcado un error al intentar comunicarse",$messageQueue);
                continue;
            }

            log_message('alert', json_encode($respuesta));

            if ($respuesta->exito == false) {
                $messageQueue->lastError = $respuesta->mensajeError;
                $messageQueue->status = "ERROR";
                $messageQueue->sentAt = date("Y-m-d H:i:s");
                $messageQueue->msgId = $respuesta->codigo;
                $messageQueueModel->save($messageQueue);

                $campaign = $campaignModel->where("id", $messageQueue->idCampaign)->first();
                $campaign->totalError = $campaign->totalError + 1;
                $campaignModel->save($campaign);

                $this->logErrorContact($respuesta->mensajeError,$messageQueue);
                
                continue;
            }

            $messageQueue->status = "PENDIENTE";
            $messageQueue->msgId = $respuesta->id;
            $messageQueue->sentAt = date("Y-m-d H:i:s");
            $messageQueueModel->save($messageQueue);

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



?>