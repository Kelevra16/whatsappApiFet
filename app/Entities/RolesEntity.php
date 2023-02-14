<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class RolesEntity extends Entity
{
    protected $datamap = [
        "id" => "",
        "nombre" => "",
        "descripcion" => "",
        "nivel" => ""
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
