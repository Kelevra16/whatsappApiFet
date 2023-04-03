<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class GrupoDifucionEntity extends Entity
{
    protected $datamap = [
        "id" => "",
        "nombre" => "",
        "descripcion" => "",
        "idEmpresa" => 0,
        "iconImage" => "",
        "location" => "",
        "totalContactos" => 0,
        "created_by" => 0
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
