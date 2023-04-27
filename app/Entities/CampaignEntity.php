<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class CampaignEntity extends Entity
{
    protected $datamap = [
        "id" => 0,
        "titulo" => "",
        "mensaje" => "",
        "adjunto" => "",
        "messageType" => "",
        "idEmpresa" => 0,
        "codigo" => "",
        "idGrupos" => "",
        "status" => "",
        "dateSend" => "",
        "totalEnviado" => 0,
        "totalEntregado" => 0,
        "totalVisto" => 0,
        "totalError" => 0,
        "totalMensajes" => 0,
        "created_by" => 0,
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
