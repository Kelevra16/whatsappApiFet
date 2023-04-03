<?php

namespace App\Controllers\Commands;

use App\Controllers\BaseController;

class CommandsController extends BaseController
{

    private $url = "https://api.whatzmeapi.com/";
    private $sendMessage = "own/enviar-mensaje";


    public function index()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 1) {
            return redirect()->to(base_url('campaign'));
        }
        
        $data = [
            'title' => 'Lista de Comandos',
            'section' => 'comandos',
            'session' => $session,
        ];

        return  view('page/parts/head', $data)
        .   view('page/command/commandBody_view')
        .   view('page/parts/footer');
    }

    public function newCommand()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        if ($role > 1) {
            return redirect()->to(base_url('campaign'));
        }
        
        $data = [
            'title' => 'Lista de Comandos',
            'section' => 'comandos',
            'session' => $session,
        ];

        return  view('page/parts/head', $data)
        .   view('page/command/newCommand_view')
        .   view('page/parts/footer');
    }


    public function commandAction($idEmpresa = 0){
        $request = \Config\Services::request();

        $posts = $request->getJSON();

        if(!$posts){
            log_message('alert','No se recibieron datos por POST');
        }

        if ($posts->type != "message"){
            $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
            log_message('alert','Status: '.$postString);
            return;
        }

        if ($posts->type == "message" && $posts->message->fromMe == true){
            $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
            log_message('alert','Mensaje propios: '.$postString);
            return;
        }

        $comando = html_entity_decode($posts->message->text);

        $commandModel = new \App\Models\CommandModel();

        $command = $commandModel->where('command',$comando)->where('idEmpresa',$idEmpresa)->first();

        if(!$command){
            log_message('alert','No se encontró el comando: '.$comando);
            $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
            log_message('alert','Sin Comando: '.$postString);
            return;
        }

        $postString = json_encode($posts,JSON_UNESCAPED_UNICODE);
        log_message('alert','mensaje de comando: '.$postString);

        switch ($command->typeCommand) {
            case 'Suscribir':
                $telefonoCompleto = $posts->user->phone;
                $nombre = $posts->user->name;
                $calculateLada = strlen($telefonoCompleto) - 10;
                $lada = substr($telefonoCompleto, 0, $calculateLada);
                $phone = substr($telefonoCompleto, 3, 10);
                $action = json_decode($command->actionCommand);
                $this->commandSubscription($idEmpresa,$phone,$lada,$nombre,$action);
                break;
        }


    }


    private function commandSubscription($idEmpresa,$phone,$lada,$nombre,$action){

        $empresaModel = new \App\Models\EmpresaModel();
        $empresa = $empresaModel->find($idEmpresa);
        $token = $empresa->tokenApi;
        $idGrupoDifucion = $action->idGrupoDifucion;

        $grupoDifucionModel = new \App\Models\GrupoDifucionModel();
        $grupoDifucion = $grupoDifucionModel->where("id",$idGrupoDifucion)->first();

        if(!$grupoDifucion){
            $message = "No se encontró el grupo de difusión";
            log_message('alert','No se encontró el grupo de difusión');
            $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
            return;
        }

        $contactModel = new \App\Models\ContactosModel();
        $contact = $contactModel->where('telefono',$phone)->where('lada',$lada)->where('idGrupoDifucion',$idGrupoDifucion)->first();

        if($contact){
            $message = "Ya te encuentras Suscrito";
            log_message('alert',"Ya te encuentras Suscrito");
            $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
            return;
        }

        $contactoEntity = new \App\Entities\ContactosEntity();
        $contactoEntity->idGrupoDifucion = $idGrupoDifucion;
        $contactoEntity->nombre = $nombre;
        $contactoEntity->telefono = $phone;
        $contactoEntity->lada = $lada;
        $contactoEntity->created_by = $grupoDifucion->created_by;

        $contactModel->save($contactoEntity);
        $totalGrupoDifucion = $grupoDifucion->totalContactos + 1;
        $grupoDifucionModel->update($idGrupoDifucion,["totalContactos"=>$totalGrupoDifucion]);

        $message = $action->message;
        log_message('alert',"suscrito correctamente " . $message);
        $this->sendOnlyTextNoResponse($token,$message,$phone,$lada);
        return;
    }

    private function sendOnlyTextNoResponse($token,$message,$phone,$lada){
        $urlSend = $this->url . $this->sendMessage . '?token=' . $token;
        $numeroCompleto = $lada . $phone;
        $dataSend = [
            'numero' =>$numeroCompleto,
            'mensaje' => $message,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlSend,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dataSend,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($status != 200) {
            log_message('alert','Error al enviar mensaje: '.$response);
        }

        log_message('alert','Mensaje enviado: '.$response);

        return;

    }

    public function getListCommands($page = 1){
        $commandModel = new \App\Models\CommandModel();
        $session = \Config\Services::session();
        $idUsuario = $session->get('idUser');
        $idEmpresa = $session->get('idEmpresa');
        $role = $session->get('role');
        $currentPage = $page;

        if(($idUsuario == null || $idUsuario == "")|| ($idEmpresa == null || $idEmpresa == "")){
            $returnData = [
                'status' => 400,
                'message' => 'No se encontró la sesión',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        if ($role > 1) {
            $returnData = [
                'status' => 400,
                'message' => 'No cuentas con los permisos necesarios',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $commands = $commandModel->where('idEmpresa',$idEmpresa)->paginate(10,'commands',$currentPage);
        $userModel = new \App\Models\UserModel();

        foreach ($commands as $key => $command) {
            $user = $userModel->find($command->created_by);
            $commands[$key]->created_by = $user->username;
        }

        $returnData = [
            'status' => 200,
            'message' => 'Campañas obtenidas con éxito',
            'susses' => true,
            'data' => $commands,
            'pager' => $commandModel->pager->getPageCount()
        ];

        return $this->response->setJSON($returnData);
    }

    public function deleteCommand()
    {   
        $session = \Config\Services::session();
        $idEmpresa = $session->get('idEmpresa');
        $idUsuario = $session->get('idUser');
        $idCommand = $this->request->getVar('idCommand');
        $role = $this->request->getVar('role');

        $commandModel = new \App\Models\CommandModel();

        if($role > 1){
            $returnData = [
                'status' => 400,
                'message' => 'No cuentas con los permisos necesarios',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $command = $commandModel->where('idEmpresa',$idEmpresa)->where('id',$idCommand)->where('created_by',$idUsuario)->first();
        

        if($command){
            $commandModel->delete($idCommand);
            $returnData = [
                'status' => 200,
                'message' => 'Campaña eliminada con éxito',
                'susses' => true,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }else{
            $returnData = [
                'status' => 400,
                'message' => 'No se encontró la campaña',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }
    }
}
