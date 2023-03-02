<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ContactosEntity extends Entity
{
    protected $datamap = [
        'id'                =>  "",
        'telefono'          =>  "",
        'lada'              =>  "",
        'nombre'            =>  "",
        'empresa'           =>  "",
        'puesto'            =>  "",
        'email'             =>  "",
        'id_grupoDifucion'  =>  "",
        'created_by'        =>  ""
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
