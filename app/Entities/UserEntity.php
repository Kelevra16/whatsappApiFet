<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $datamap = [
        "id" => "",
        "username" => "",
        "password" => "",
        "nombre" => "",
        "aPaterno" => "",
        "aMaterno" => "",
        "correo" => "",
        "idRol" => "",
        "idEmpresa" => ""
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
