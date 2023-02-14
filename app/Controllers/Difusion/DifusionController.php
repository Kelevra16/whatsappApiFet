<?php

namespace App\Controllers\Difusion;

use App\Controllers\BaseController;

class DifusionController extends BaseController
{
    public function index()
    {
        $session = \Config\Services::session();
        if ($session->get('logged_in') == FALSE) {
            return redirect()->to(base_url('/'));
        }

        $data = [
            'title' => 'Difusión',
            'section' => 'difusión',
            'session' => $session
        ];

        return  view('page/parts/head', $data)
            .   view('page/difusion/difusionBody_view')
            .   view('page/parts/footer');
    }

    public function geListDifucion($page = 1)
    {
        $session = \Config\Services::session();
        $idRol = $session->get('role');
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $currentPage = $page;

        $difusionModel = new \App\Models\GrupoDifucionModel();

        $difusion = [];

        if ($idRol <= 1) {
            $difusion = $difusionModel->where('id_empresa', $idEmpresa)->paginate(3,'default',$currentPage);
        } else {
            $difusion = $difusionModel->where('id_empresa', $idEmpresa)->where('created_by', $idUsuario)->paginate(3,'default',$currentPage);
        }

        $returnData = [
            'status' => 200,
            'message' => 'Lista de difusion',
            'susses' => true,
            'data' => $difusion,
            'pager' => $difusionModel->pager->getPageCount(),
        ];

        return $this->response->setJSON($returnData);
    }

    public function createdListDifusionByFileXlsx()
    {
        $session = \Config\Services::session();
        // if ($session->get('logged_in') == FALSE) {
        //     return redirect()->to(base_url('/'));
        // }

        $request = \Config\Services::request();
        $nombre = $request->getPost('titulo');
        $description = $request->getPost('descripcion');
        $location = $request->getPost('location');
        $icono = "";

        if (!$nombre || !$description || !$location) {
            $returnData = [
                'status' => 400,
                'message' => 'Los campos titulo, descripción y ubicación son requeridos',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $nombre = htmlspecialchars($nombre);
        $description = htmlspecialchars($description);
        $location = htmlspecialchars($location);

        // $idRol = $session->get('role');
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');

        $file = $request->getFile('excel');

        if (!$file) {
            $returnData = [
                'status' => 400,
                'message' => 'El archivo es requerido',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        if (!$file->isValid() || $file->hasMoved()) {
            $returnData = [
                'status' => 400,
                'message' => 'Error al subir el archivo',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        if(!$file->move(WRITEPATH . 'uploads')){
            $returnData = [
                'status' => 400,
                'message' => 'Error al mover el archivo',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $file = WRITEPATH . 'uploads/' . $file->getName();

        $difusionModel = new \App\Models\GrupoDifucionModel();
        $difusion = $difusionModel->where('nombre', $nombre)->where('descripcion', $description)->where('id_empresa', $idEmpresa)->first();

        if ($difusion) {
            $returnData = [
                'status' => 400,
                'message' => 'Ya existe una lista con ese nombre',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }


        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $difusionEntity = new \App\Entities\GrupoDifucionEntity();
        $difusionEntity->nombre = $nombre;
        $difusionEntity->descripcion = $description;
        $difusionEntity->id_empresa = $idEmpresa;
        $difusionEntity->iconImage = $icono;
        $difusionEntity->location = $location;
        $difusionEntity->totalContactos = 0;
        $difusionEntity->created_by = $idUsuario;

        $idDifusion = $difusionModel->insert($difusionEntity);

        if (!$idDifusion) {
            $returnData = [
                'status' => 400,
                'message' => 'Error al crear la lista de difusion',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $contactoModel = new \App\Models\ContactosModel();

        $data = [];
        $total = 0;
        foreach ($sheetData as $key => $value) {
            if ($key > 0) {
                $contactoEntity = new \App\Entities\ContactosEntity();
                $contactoEntity->telefono = $value[1];
                $contactoEntity->lada = $value[0];
                $contactoEntity->nombre = $value[2];
                $contactoEntity->id_grupoDifucion = $idDifusion;
                $contactoEntity->created_by = $idUsuario;

                $data[] = $contactoEntity;

                if (count($data) == 1000) {
                    $contactoModel->insertBatch($data);
                    $total += count($data);
                    $data = [];
                }
            }
        }

        if (count($data) > 0) {
            $contactoModel->insertBatch($data);
            $total += count($data);
        }

        $difusionEntity->totalContactos = $total;
        $difusionEntity->id = $idDifusion;

        $difusionModel->save($difusionEntity);

        $returnData = [
            'status' => 200,
            'message' => 'Lista de difusion creada',
            'susses' => true,
            'url' => '',
        ];

        return $this->response->setJSON($returnData);
    }

    public function difusionCreated(){
        $session = \Config\Services::session();
        if ($session->get('logged_in') == FALSE) {
            return redirect()->to(base_url('/'));
        }

        $data = [
            'title' => 'Crear Difusión nueva',
            'section' => 'difusión',
            'session' => $session
        ];

        return  view('page/parts/head', $data)
            .   view('page/difusion/newDifusion_view')
            .   view('page/parts/footer');
    }

    public function editListDifucion($idListDifusion){
        $session = \Config\Services::session();

        $difucionModel = new \App\Models\GrupoDifucionModel();
        $difusion = $difucionModel->where('id', $idListDifusion)->first();

        if (!$difusion) {
            return redirect()->to(base_url('/difusion'));
        }

        $data = [
            'title' => 'Editar Lista de Difucion',
            'section' => 'difusión',
            'idDifusion' => $idListDifusion,
            'difusion' => $difusion,
        ];

        return  view('page/parts/head', $data)
            .   view('page/difusion/editDifucion_view')
            .   view('page/parts/footer');
    }

    public function getDataListDifucion($page = 1){
        $session = \Config\Services::session();
        $currentPage = $page;
        $request = \Config\Services::request();
        $idListDifusion = $request->getPost('idDifucion');
        $difusionModel = new \App\Models\GrupoDifucionModel();
        $listContactos = new \App\Models\ContactosModel();
        $difusion = $difusionModel->where('id', $idListDifusion)->first();
        $list = $listContactos->where('id_grupoDifucion', $idListDifusion)->paginate(10,'default',$currentPage);

        if (!$difusion) {
            $returnData = [
                'status' => 400,
                'message' => 'No se encontro la lista de difusión',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        if (!$list) {
            $returnData = [
                'status' => 400,
                'message' => 'No se encontraron contactos en la lista de difusión',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $difusion->listContactos = $list;

        $returnData = [
            'status' => 200,
            'message' => 'Lista de difusión encontrada',
            'susses' => true,
            'url' => '',
            'data' => $difusion,
            'page' => $listContactos->pager->getPageCount(),
        ];

        return $this->response->setJSON($returnData);
    }


    public function deleteContacto(){
        $session = \Config\Services::session();
        $request = \Config\Services::request();
        $idUsuario = $session->get('idUser');
        $idRol = $session->get('role');
        $idEmpresa = $session->get('idEmpresa');
        $idContacto = $request->getPost('idContacto');
        $contactoModel = new \App\Models\ContactosModel();
        $difucionModel = new \App\Models\GrupoDifucionModel();
        
        if($idRol <= 1){
            $lisDifucionModel = $difucionModel->where('id_empresa', $idEmpresa)->findAll();
        }else{
            $lisDifucionModel = $difucionModel->where('created_by', $idUsuario)->findAll();
        }

        $listIdDufucion = [];
        foreach ($lisDifucionModel as $key => $value) {
            $listIdDufucion[] = $value->id;
        }


        $contacto = $contactoModel->where('id', $idContacto)->whereIn('id_grupoDifucion', $listIdDufucion)->first();

        if (!$contacto) {
            $returnData = [
                'status' => 200,
                'message' => 'No se encontro el contacto',
                'susses' => false,
                'data' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $sussesDelete = $contactoModel->delete($idContacto);
        
        if($sussesDelete){
            $idDifusion = $contacto->id_grupoDifucion;
            $difusionEnty = $difucionModel->where('id', $idDifusion)->first();
            $difusionEnty->totalContactos = $difusionEnty->totalContactos - 1;
            $difusionEnty->id = $idDifusion;
            $difucionModel->save($difusionEnty);
        }

        $returnData = [
            'status' => 200,
            'message' => 'Contacto eliminado',
            'susses' => true,
            'data' => '',
        ];

        return $this->response->setJSON($returnData);
    }
}
