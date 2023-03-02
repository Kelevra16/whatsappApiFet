<?php

namespace App\Controllers\Usuarios;

use App\Controllers\BaseController;

class UsuariosController extends BaseController
{
    public function index()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 1) {
            return redirect()->to(base_url('campaign'));
        }
        
        $data = [
            'title' => 'Lista de usuarios',
            'section' => 'usuarios',
            'session' => $session,
        ];

        return  view('page/parts/head', $data)
        .   view('page/users/usersBody_view')
        .   view('page/parts/footer');
    }

    public function getListUser($page = 1){
        $session = \Config\Services::session();
        $role = $session->get('role');
        $idEmpresa = $session->get('idEmpresa');
        $currentPage = $page;

        if ($role > 1) {
            $returnData = [
                'status' => 200,
                'message' => 'No tienes permisos para ver esta lista',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }


        $usuarios = [];
        $usuariosModel = new \App\Models\UserModel();
        $empresaModel = new \App\Models\EmpresaModel();
        $roleModel = new \App\Models\RolesModel();

        if ($role == 0) {
            $usuarios = $usuariosModel->paginate(10,'default',$currentPage);
        }else{
            $usuarios = $usuariosModel->where('id_empresa', $idEmpresa)->paginate(10,'default',$currentPage);
        }

        if (empty($usuarios)) {
            $returnData = [
                'status' => 200,
                'message' => 'No hay usuarios registrados',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }

        foreach ($usuarios as $key => $usuario) {
            $empresa = $empresaModel->where('id', $usuario->id_empresa)->first();
            $role = $roleModel->where('id', $usuario->id_rol)->first();

            $usuarios[$key]->empresa = $empresa->nombre;
            $usuarios[$key]->role = $role->nombre;
            $usuarios[$key]->nombreCompleto = $usuario->nombre . ' ' . $usuario->aPaterno . ' ' . $usuario->aMaterno;
        }


        $returnData = [
            'status' => 200,
            'message' => 'Lista de usuarios',
            'susses' => true,
            'data' => $usuarios,
            'pager' => $usuariosModel->pager->getPageCount(),
        ];

        return $this->response->setJSON($returnData);
    }

    public function deleteUser(){
        $session = \Config\Services::session();
        $role = $session->get('role');
        $idEmpresa = $session->get('idEmpresa');
        $idUser = $session->get('idUser');
        $id = $this->request->getPost('idUser');

        if ($role > 1) {
            $returnData = [
                'status' => 200,
                'message' => 'No tienes permisos para eliminar usuarios',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }

        if ($idUser == $id) {
            $returnData = [
                'status' => 200,
                'message' => 'No puedes eliminarte a ti mismo',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }

        $usuariosModel = new \App\Models\UserModel();
        $usuario = $usuariosModel->where('id', $id)->first();

        if (empty($usuario)) {
            $returnData = [
                'status' => 200,
                'message' => 'No se encontró el usuario',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }

        if ($usuario->id_empresa != $idEmpresa && $role == 1) {
            $returnData = [
                'status' => 200,
                'message' => 'No tienes permisos para eliminar este usuario',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }

        $usuariosModel->where('id', $id)->delete();

        $returnData = [
            'status' => 200,
            'message' => 'Usuario eliminado',
            'susses' => true,
            'data' => [],
        ];

        return $this->response->setJSON($returnData);
    }


    public function newUser(){
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 1) {
            return redirect()->to(base_url('campaign'));
        }

        $empresaModel = new \App\Models\EmpresaModel();
        $roleModel = new \App\Models\RolesModel();

        if($role == 0){
            $empresas = $empresaModel->findAll();
            $roles = $roleModel->findAll();
        }else{
            $empresas = $empresaModel->where('id', $session->get('idEmpresa'))->findAll();
            $roles = $roleModel->where("id > 0")->findAll();
        }
        
        $data = [
            'title' => 'Nuevo usuario',
            'section' => 'usuarios',
            'session' => $session,
            'empresas' => $empresas,
            'roles' => $roles,
        ];

        return  view('page/parts/head', $data)
        .   view('page/users/newUser_view.php')
        .   view('page/parts/footer');
    }


    public function saveUser(){
        $session = \Config\Services::session();
        $roleSession = $session->get('role');
        $idroleSession = $session->get('idrole');
        $empresaSession = $session->get('idEmpresa');

        $inputs = $this->validate([
            'nombre' => 'required|min_length[3]',
            'aPaterno' => 'required|min_length[3]',
            'aMaterno' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'confpassword' => 'required|min_length[6]',
            'idEmpresa' => 'required',
            'idRole' => 'required',
        ], [
            'nombre' => [
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
                'valid_email' => 'El campo email no es válido',
            ],
            'password' => [
                'required' => 'El campo contraseña es requerido',
                'min_length' => 'El campo contraseña debe tener al menos 6 caracteres',
            ],
            'confpassword' => [
                'required' => 'El campo confirmar contraseña es requerido',
                'min_length' => 'El campo confirmar contraseña debe tener al menos 6 caracteres',
            ],
            'idEmpresa' => [
                'required' => 'El campo empresa es requerido',
            ],
            'idRole' => [
                'required' => 'El campo rol es requerido',
            ]
        ]);

        if (!$inputs) {
            $errorMessage = '';
            foreach ($this->validator->getErrors() as $key => $value) {
                $errorMessage .= $value . ", ";
            }
            $returnData = [
                'status' => 200,
                'message' => $errorMessage,
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $nombre = $this->request->getVar('nombre');
        $aPaterno = $this->request->getVar('aPaterno');
        $aMaterno = $this->request->getVar('aMaterno');
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $confpassword = $this->request->getVar('confpassword');
        $empresa = $this->request->getVar('idEmpresa');
        $role = $this->request->getVar('idRole');

        if ($roleSession > 1) {
            $returnData = [
                'status' => 200,
                'message' => 'No tienes permisos para editar usuarios',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }

        $roleModel = new \App\Models\RolesModel();
        $roleM = $roleModel->where('id', $role)->first();
        if (empty($roleM)) {
            $returnData = [
                'status' => 200,
                'message' => 'No se encontró el rol',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }


        $empresaModel = new \App\Models\EmpresaModel();
        $empresaM = $empresaModel->where('id', $empresa)->first();
        if (empty($empresaM)) {
            $returnData = [
                'status' => 200,
                'message' => 'No se encontró la empresa',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }


        if($roleSession == 1){
            $idEmpresa = $empresaSession;
            $idRole = ($role > 1 && $role < 4) ? $role : 3;
        }else{
            $idEmpresa = $empresa;
            $idRole = $role;
        }


        $confpassword = $this->generateHash($confpassword);

        if(!password_verify($password,$confpassword)){
            $returnData = [
                'status' => 200,
                'message' => 'Las contraseñas no coinciden',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $userEntity = new \App\Entities\UserEntity();
        $userEntity->username = $email;
        $userEntity->nombre = $nombre;
        $userEntity->aPaterno = $aPaterno;
        $userEntity->aMaterno = $aMaterno;
        $userEntity->correo = $email;
        $userEntity->password = $this->generateHash($password);
        $userEntity->id_empresa = $idEmpresa;
        $userEntity->id_rol = $idRole;


        $usuariosModel = new \App\Models\UserModel();
        $usuario = $usuariosModel->where('username', $email)->first();

        if ($usuario) {
            $returnData = [
                'status' => 200,
                'message' => 'El usuario ya existe',
                'susses' => false,
                'data' => [],
            ];
            
            return $this->response->setJSON($returnData);
        }

        $usuariosModel->save($userEntity);

        $returnData = [
            'status' => 200,
            'message' => 'Usuario guardado',
            'susses' => true,
            'data' => [],
        ];

        return $this->response->setJSON($returnData);

    }

    private function generateHash($password){
        $options = [
            'cost' => 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }
}
