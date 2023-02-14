<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EmpresaEntity extends Entity
{
    protected $datamap = [
        "id" => '',
        "nombre" => '',
        "direccion" => '',
        "descripcion" => '',
        "telefono" => '',
        "tokenApi" => '',
        "logo" => '',
        "primaryColor" => '',
        "secondaryColor" => ''
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
