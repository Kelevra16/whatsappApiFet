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

        //query para mensajes que hay por enviar en el dia;
        // $total = $messageQueueModel->select("SUM(CASE WHEN status = 'CREADO' THEN 1 ELSE 0 END) + SUM(CASE WHEN status = 'PROGRAMADO' AND DATE(scheduledAt) <= CURDATE() THEN 1 ELSE 0 END) + SUM(CASE WHEN status NOT IN ('CREADO', 'PROGRAMADO') AND DATE(sentAt) = CURDATE() THEN 1 ELSE 0 END) AS Total")->first();

        $totalPendientes = $messageQueueModel->where("status", "PENDIENTE")->orWhere("status", "ENVIADO")->countAllResults();

        if ($totalPendientes > 0) {
            log_message('alert', 'Hay mensajes con estatus pendientes');
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

        foreach ($messageQueues as $messageQueue) {
            $token = $empresaModel->select("tokenApi")->where("id", $messageQueue->idEmpresa)->first();

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
                
                continue;
            }

            $messageQueue->status = "PENDIENTE";
            $messageQueue->msgId = $respuesta->id;
            $messageQueue->sentAt = date("Y-m-d H:i:s");
            $messageQueueModel->save($messageQueue);

        }

    }


}



?>