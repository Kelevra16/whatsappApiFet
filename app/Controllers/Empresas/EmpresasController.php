<?php

namespace App\Controllers\Empresas;

use App\Controllers\BaseController;

class EmpresasController extends BaseController
{
    public function index()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 0) {
            return redirect()->to(base_url('campaign'));
        }
        
        $data = [
            'title' => 'Lista de Empresas',
            'section' => 'empresas',
            'session' => $session,
        ];

        return  view('page/parts/head', $data)
        .   view('page/empresas/empresasBody_view')
        .   view('page/parts/footer');
    }


    public function getListEmpresas($page = 1)
    {
        $session = \Config\Services::session();
        $role = $session->get('role');
        $currentPage = $page;

        if ($role > 0) {
            return redirect()->to(base_url('campaign'));
        }
        $empresas = [];
        $empresasModel = new \App\Models\EmpresaModel();
        $empresas = $empresasModel->paginate(10,'default',$currentPage);

        $returnData = [
            'status' => 200,
            'message' => 'Lista de empresas',
            'susses' => true,
            'data' => $empresas,
            'pager' => $empresasModel->pager->getPageCount(),
        ];

        return $this->response->setJSON($returnData);
    }

    public function createdEmpresa()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 1) {
            return redirect()->to(base_url('campaign'));
        }
        
        $data = [
            'title' => 'Crear Empresa',
            'section' => 'empresas',
            'session' => $session,
        ];

        return  view('page/parts/head', $data)
        .   view('page/empresas/newEmpresa_view.php')
        .   view('page/parts/footer');
    }

    public function saveEmpresa()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 0) {
            return redirect()->to(base_url('campaign'));
        }

        $inputs = $this->validate([
            'nombre' => 'required|min_length[3]',
            'direccion' => 'required|min_length[3]',
            'descripcion' => 'required|min_length[3]',
            'telefono' => 'required|min_length[3]',
            'apikey' => 'required|min_length[3]',
        ], [
            'nombre' => [
                'required' => 'El campo nombre es requerido',
            ],
            'direccion' => [
                'required' => 'El campo direccion es requerido',
            ],
            'descripcion' => [
                'required' => 'El campo descripcion es requerido',
            ],
            'telefono' => [
                'required' => 'El campo telefono es requerido',
            ],
            'apikey' => [
                'required' => 'El campo apikey es requerido',
            ],
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
        $direccion = $this->request->getVar('direccion');
        $descripcion = $this->request->getVar('descripcion');
        $telefono = $this->request->getVar('telefono');
        $apikey = $this->request->getVar('apikey');


        $emrpesaEntity = new \App\Entities\EmpresaEntity();
        $emrpesaEntity->nombre = $nombre;
        $emrpesaEntity->direccion = $direccion;
        $emrpesaEntity->descripcion = $descripcion;
        $emrpesaEntity->telefono = $telefono;
        $emrpesaEntity->tokenApi = $apikey;


        $empresasModel = new \App\Models\EmpresaModel();
        $saveEmpresa = $empresasModel->save($emrpesaEntity);

        if(!$saveEmpresa){
            $returnData = [
                'status' => 200,
                'message' => 'Error al crear la empresa',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $returnData = [
            'status' => 200,
            'message' => 'Empresa creada',
            'susses' => true,
        ];

        return $this->response->setJSON($returnData);
    }

    public function deleteEmpresa(){
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 0) {
            $returnData = [
                'status' => 200,
                'message' => 'No tienes permisos para eliminar una empresa',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $id = $this->request->getVar('idEmpresa');
        $usuarioModel = new \App\Models\UserModel();
        $usuario = $usuarioModel->where('id_empresa', $id)->first();

        if($usuario){
            $returnData = [
                'status' => 200,
                'message' => 'No se puede eliminar la empresa, tiene usuarios asociados',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $empresasModel = new \App\Models\EmpresaModel();
        $deleteEmpresa = $empresasModel->delete($id);

        if(!$deleteEmpresa){
            $returnData = [
                'status' => 200,
                'message' => 'Error al eliminar la empresa',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $returnData = [
            'status' => 200,
            'message' => 'Empresa eliminada',
            'susses' => true,
        ];

        return $this->response->setJSON($returnData);
    }
}
