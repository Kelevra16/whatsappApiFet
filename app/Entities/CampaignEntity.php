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
        "tipo" => 0,
        "id_empresa"=> 0,
        "codido" => "",
        "UUID" => "",
        "fecha_hora" => "",
        "id_grupos" => 0,
        "status" => "",
        "totalMensajes" => 0,
        "created_by" => 0
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
