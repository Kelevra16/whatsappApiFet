<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class MessageQueueEntity extends Entity
{
    protected $datamap = [
        "id" => "",
        "idContact" => "",
        "phone" => "",
        "idEmpresa" => "",
        "idCampaign" => "",
        "messageJson" => "",
        "msgId" => "",
        "status" => "",
        "sentAt" => "",
        "scheduledAt" => "",
        "created_by" => "",
        "retryCount" => "",
        "lastError" => "",
        "messageType" => ""
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
