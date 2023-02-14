<?php

namespace App\Controllers\Myaccount;

use App\Controllers\BaseController;

class MyAccountController extends BaseController
{
    public function index()
    {
        $session = \Config\Services::session();

        $idUser = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $role = $session->get('role');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('id', $idUser)->first();

        $empresaModel = new \App\Models\EmpresaModel();
        $empresa = $empresaModel->where('id', $idEmpresa)->first();

        $roles = new \App\Models\RolesModel();
        $roles = $roles->where('id', $role)->first();

        $data = [
            'title' => 'Mi cuenta',
            'section' => 'myAccount',
            'session' => $session,
            'user' => $user,
            'empresa' => $empresa,
            'roles' => $roles
        ];


        return  view('page/parts/head', $data)
            .   view("page/myaccount/myaccount_view")
            .   view('page/parts/footer');
    }

    public function updateSelfUser()
    {
        $session = \Config\Services::session();

        $inputs = $this->validate([
            'name' => 'required|min_length[3]',
            'aPaterno' => 'required|min_length[3]',
            'aMaterno' => 'required|min_length[3]',
            'email' => 'required|min_length[3]',
        ], [
            'name' => [
                'required' => 'El campo nombre es requerido',
            ],
            'aPaterno' => [
                'required' => 'El campo apellido paterno es requerido',
            ],
            'aMaterno' => [
                'required' => 'El campo apellido materno es requerido',
            ],
            'email' => [
                'required' => 'El campo email es requerido',
            ],
        ]);

        if (!$inputs) {
            $returnData = [
                'status' => 200,
                'message' => $this->validator->listErrors(),
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $name = $this->request->getVar('name');
        $aPaterno = $this->request->getVar('aPaterno');
        $aMaterno = $this->request->getVar('aMaterno');
        $email = $this->request->getVar('email');

        $idUser = $session->get('idUser');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('id', $idUser)->first();

        if (!$user) {
            $returnData = [
                'status' => 200,
                'message' => 'Usuario no encontrado',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $userModel->update($idUser, [
            'nombre' => $name,
            'aPaterno' => $aPaterno,
            'aMaterno' => $aMaterno,
            'correo' => $email,
        ]);

        $returnData = [
            'status' => 200,
            'message' => 'Usuario actualizado correctamente',
            'susses' => true,
            'data' => []
        ];

        return $this->response->setJSON($returnData);

    }

    public function updatePassword(){
        $session = \Config\Services::session();

        $inputs = $this->validate([
            'password' => 'required|min_length[3]',
            'newPassword' => 'required|min_length[3]',
            'confirmPassword' => 'required|min_length[3]',
        ], [
            'password' => [
                'required' => 'El campo contraseña es requerido',
            ],
            'newPassword' => [
                'required' => 'El campo nueva contraseña es requerido',
            ],
            'confirmPassword' => [
                'required' => 'El campo confirmar contraseña es requerido',
            ],
        ]);

        if (!$inputs) {
            $returnData = [
                'status' => 200,
                'message' => $this->validator->listErrors(),
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $password = $this->request->getVar('password');
        $newPassword = $this->request->getVar('newPassword');
        $confirmPassword = $this->request->getVar('confirmPassword');

        $idUser = $session->get('idUser');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('id', $idUser)->first();

        if (!$user) {
            $returnData = [
                'status' => 200,
                'message' => 'Usuario no encontrado',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        if($newPassword != $confirmPassword){
            $returnData = [
                'status' => 200,
                'message' => 'Las contraseñas no coinciden',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        // dd($password);
        
        $password = htmlspecialchars($password);
        if(!password_verify($password, $user->password)){
            $returnData = [
                'status' => 200,
                'message' => 'La contraseña actual no es correcta',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $options = [
            'cost' => 12,
        ];

        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT,$options);

        $userModel->update($idUser, [
            'password' => $newPassword,
        ]);

        $returnData = [
            'status' => 200,
            'message' => 'Contraseña actualizada correctamente',
            'susses' => true,
            'data' => []
        ];

        return $this->response->setJSON($returnData);
    }

}
