<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class LogErrorEntity extends Entity
{
    protected $datamap = [
        "id" => "",
        "fecha" => "",
        "mensaje" => "",
        "idEmpresa" => "",
        "tipoOrigen" => "",
        "origenText" => "",
        "tipoError" => "",
        "visto" => "",
    ];
    protected $dates   = [];
    protected $casts   = [];
}
