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
        $role = $session->get('role');
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $currentPage = $page;

        $difusionModel = new \App\Models\GrupoDifucionModel();

        $difusion = [];

        if ($role <= 1) {
            $difusion = $difusionModel->where('idEmpresa', $idEmpresa)->paginate(10,'default',$currentPage);
        } else {
            $difusion = $difusionModel->where('idEmpresa', $idEmpresa)->where('created_by', $idUsuario)->paginate(10,'default',$currentPage);
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

        $request = \Config\Services::request();
        $nombre = $request->getPost('titulo');
        $description = $request->getPost('descripcion');
        $location = $request->getPost('location');
        $icono = "";

        if (!$nombre || !$description || !$location) {
            $returnData = [
                'status' => 400,
                'message' => 'Los campos titulo, descripción y ubicación son requeridos',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $nombre = htmlspecialchars($nombre);
        $description = htmlspecialchars($description);
        $location = htmlspecialchars($location);

        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');

        $file = $request->getFile('excel');

        if (!$file) {
            $returnData = [
                'status' => 400,
                'message' => 'El archivo es requerido',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        if (!$file->isValid() || $file->hasMoved()) {
            $returnData = [
                'status' => 400,
                'message' => 'Error al subir el archivo',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        if(!$file->move(WRITEPATH . 'uploads')){
            $returnData = [
                'status' => 400,
                'message' => 'Error al mover el archivo',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $file = WRITEPATH . 'uploads/' . $file->getName();

        $difusionModel = new \App\Models\GrupoDifucionModel();
        $difusion = $difusionModel->where('nombre', $nombre)->where('descripcion', $description)->where('idEmpresa', $idEmpresa)->first();

        if ($difusion) {
            $returnData = [
                'status' => 400,
                'message' => 'Ya existe una lista con ese nombre',
                'susses' => false,
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
        $difusionEntity->idEmpresa = $idEmpresa;
        $difusionEntity->iconImage = $icono;
        $difusionEntity->location = $location;
        $difusionEntity->totalContactos = 0;
        $difusionEntity->created_by = $idUsuario;

        $idDifusion = $difusionModel->insert($difusionEntity);

        if (!$idDifusion) {
            $returnData = [
                'status' => 400,
                'message' => 'Error al crear la lista de difusion',
                'susses' => false,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $contactoModel = new \App\Models\ContactosModel();
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        $data = [];
        $total = 0;
        $error = "";
        foreach ($sheetData as $key => $value) {
            if ($key > 0) {

                if (!isset($value[0]) && !isset($value[1]) && !isset($value[2])) {
                    $error = "Hay campos vacíos que serán omitidos";
                    continue;
                }

                $telefono = intval(preg_replace("/[^0-9]/", '',  $value[1]));
                $lada = intval(preg_replace("/[^0-9]/", '',  $value[0]));

                if(!is_numeric($telefono) || !is_numeric($lada)){
                    $error = "Hay campos que no son números que serán omitidos";
                    continue;
                }


                try {
                   $swissNumberStr = '+' . $lada . $telefono;
                    $swissNumberProto = $phoneUtil->parse($swissNumberStr);
                    $isValid = $phoneUtil->isValidNumber($swissNumberProto);
                    if (!$isValid) {
                        $error = "Hay campos que no son números válidos que serán omitidos";
                        continue;
                    }

                    $region = $phoneUtil->getRegionCodeForNumber($swissNumberProto);

                    if ($region == 'MX') {
                        if ($lada != '521') {
                            $lada = '521';
                        }
                    }

                } catch (\libphonenumber\NumberParseException $e) {
                    $error = "Hay campos que no son números válidos que serán omitidos";
                    continue;
                }


                
                $contactoEntity = new \App\Entities\ContactosEntity();
                $contactoEntity->telefono = $telefono;
                $contactoEntity->lada = $lada;
                $contactoEntity->nombre = $value[2];
                $contactoEntity->empresa = (isset($value[3]))?$value[3]:"";
                $contactoEntity->puesto = (isset($value[4]))?$value[4]:"";
                $contactoEntity->email = (isset($value[5]))?$value[5]:"";;
                $contactoEntity->idGrupoDifucion = $idDifusion;
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
            'error' => $error,
            'susses' => true,
            'url' => '',
        ];

        return $this->response->setJSON($returnData);
    }

    public function createdListDifusion()
    {
        $session = \Config\Services::session();

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

        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');

        $difusionModel = new \App\Models\GrupoDifucionModel();
        $difusion = $difusionModel->where('nombre', $nombre)->where('descripcion', $description)->where('idEmpresa', $idEmpresa)->first();

        if ($difusion) {
            $returnData = [
                'status' => 400,
                'message' => 'Ya existe una lista con ese nombre',
                'susses' => true,
                'url' => '',
            ];
            return $this->response->setJSON($returnData);
        }


        $difusionEntity = new \App\Entities\GrupoDifucionEntity();
        $difusionEntity->nombre = $nombre;
        $difusionEntity->descripcion = $description;
        $difusionEntity->idEmpresa = $idEmpresa;
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
            'session' => $session,
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
        $list = $listContactos->where('idGrupoDifucion', $idListDifusion)->paginate(10,'default',$currentPage);

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
        $role = $session->get('role');
        $idEmpresa = $session->get('idEmpresa');
        $idContacto = $request->getPost('idContacto');
        $contactoModel = new \App\Models\ContactosModel();
        $difucionModel = new \App\Models\GrupoDifucionModel();
        
        if($role <= 1){
            $lisDifucionModel = $difucionModel->where('idEmpresa', $idEmpresa)->findAll();
        }else{
            $lisDifucionModel = $difucionModel->where('created_by', $idUsuario)->findAll();
        }

        $listIdDufucion = [];
        foreach ($lisDifucionModel as $key => $value) {
            $listIdDufucion[] = $value->id;
        }


        $contacto = $contactoModel->where('id', $idContacto)->whereIn('idGrupoDifucion', $listIdDufucion)->first();

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
            $idDifusion = $contacto->idGrupoDifucion;
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

    public function saveContacto(){
        $session = \Config\Services::session();
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');

        
        $inputs = $this->validate([
            'nombre' => 'required|min_length[3]',
            "lada" => "required|numeric|min_length[2]",
            "telefono" => "required|numeric|min_length[10]|max_length[10]",
        ], [
            'nombre' => [
                'required' => 'El campo nombre es requerido',
            ],
            'lada' => [
                'required' => 'El campo lada es requerido',
            ],
            'telefono' => [
                'required' => 'El campo teléfono es requerido',
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

        $idDifucion = $this->request->getVar('idDifucion');
        $name = $this->request->getVar('nombre');
        $lada = $this->request->getVar('lada');
        $telefono = $this->request->getVar('telefono');
        $empresa = $this->request->getVar('empresa');
        $puesto = $this->request->getVar('puesto');
        $email = $this->request->getVar('email');
        $difusionModel = new \App\Models\GrupoDifucionModel();
        $contactoModel = new \App\Models\ContactosModel();

        $difusion = $difusionModel->where('id', $idDifucion)->where('idEmpresa', $idEmpresa)->first();

        if (!$difusion) {
            $returnData = [
                'status' => 400,
                'message' => 'No se encontró la lista de difusión',
                'susses' => false,
                'data' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $lada = intval(preg_replace("/[^0-9]/", '',  $lada));
        $telefono = intval(preg_replace("/[^0-9]/", '',  $telefono));

        $existContacto = $contactoModel->where('telefono', $telefono)->where('lada', $lada)->where('idGrupoDifucion', $idDifucion)->first();

        if ($existContacto) {
            $returnData = [
                'status' => 200,
                'message' => 'El contacto ya existe en la lista de difusión',
                'susses' => true,
                'data' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $contactoEntity = new \App\Entities\ContactosEntity();
        $contactoEntity->telefono = $telefono;
        $contactoEntity->lada = $lada;
        $contactoEntity->nombre = $name;
        $contactoEntity->empresa = $empresa;
        $contactoEntity->puesto = $puesto;
        $contactoEntity->email = $email;
        $contactoEntity->idGrupoDifucion = $idDifucion;
        $contactoEntity->created_by = $idUsuario;

        $idContacto = $contactoModel->insert($contactoEntity);

        if (!$idContacto) {
            $returnData = [
                'status' => 400,
                'message' => 'Error al crear el contacto',
                'susses' => false,
                'data' => '',
            ];
            return $this->response->setJSON($returnData);
        }

        $difusion->totalContactos = $difusion->totalContactos + 1;
        $difusion->id = $idDifucion;
        $difusionModel->save($difusion);

        $returnData = [
            'status' => 200,
            'message' => 'Contacto creado',
            'susses' => true,
            'data' => '',
        ];

        return $this->response->setJSON($returnData);

    }
}
