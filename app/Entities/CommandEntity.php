<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class CommandEntity extends Entity
{
    protected $datamap = [
        "id"=>"",
        "titulo"=>"",
        "typeCommand"=>"",
        "idEmpresa"=>"",
        "command"=>"",
        "actionCommand" => "",
        "created_by"=>""
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
