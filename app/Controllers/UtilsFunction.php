<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\ApiWhatsApp\ApiWhatsAppController as ApiWhatsApp;

class UtilsFunction extends BaseController
{
    private $api;

    public function __construct()
    {
        $this->api = new ApiWhatsApp();
    }

    public function getStateInstancia()
    {

        $session = \Config\Services::session();
        $token = $session->get('tokenApi');

        $estado =  $this->api->getEstadoInstancia($token);

        if($estado == false){
            $returnData = [
                'status' => 200,
                'message' => 'No se pudo obtener el estado de la instancia',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        if($estado->estatus->logueado === true){
            $idEmpresa = $session->get('idEmpresa');
            $this->api->createdWedHook($token,$idEmpresa);
        }

        $returnData = [
            'status' => 200,
            'message' => 'Estado de la instancia',
            'susses' => true,
            'data' => $estado
        ];

        return $this->response->setJSON($returnData);
        
    }

    private function genErrorLog($modulo, $funcion, $message,$tipo = 'alert'){
        $returnData = [
            'modulo ' => $modulo,
            'funcion' => $funcion,
            'message' => $message
        ];

        log_message($tipo,json_encode($returnData));
    }

    public function getQrImage(){
        $session = \Config\Services::session();
        $role = $session->get('role');
        
        if($role > 1){
            $returnData = [
                'status' => 200,
                'message' => 'No tienes permisos para realizar esta acción',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }
        $token = $session->get('tokenApi');

        $qr = $this->api->getQr($token);

        if($qr == false){
            $returnData = [
                'status' => 200,
                'message' => 'No se pudo obtener el estado de la instancia',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        //qr as image
        return $this->response->setHeader('Content-Type', 'image/png')->setBody($qr);

    }

    public function getAllrequestPOST(){

        $request = \Config\Services::request();

        $posts = $request->getJSON();

        if(!$posts){
            log_message('alert','No se recibieron datos por POST');
        }

        $postString = json_encode($posts);

        log_message('alert','POST: '.$postString);
    }

    public function unlinkAccount(){
        $session = \Config\Services::session();
        $role = $session->get('role');
        
        if($role > 1){
            $returnData = [
                'status' => 200,
                'message' => 'No tienes permisos para realizar esta acción',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }
        $token = $session->get('tokenApi');

        $unlink = $this->unlinkAccountApi($token);

        if($unlink == false){
            $returnData = [
                'status' => 200,
                'message' => 'No se pudo cerrar la sesión',
                'susses' => false,
                'data' => []
            ];
            return $this->response->setJSON($returnData);
        }

        $returnData = [
            'status' => 200,
            'message' => $unlink,
            'susses' => true,
            'data' => []
        ];
        return $this->response->setJSON($returnData);
    }

    private function unlinkAccountApi($token){
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.whatzmeapi.com/own/cerrar-sesion?token='.$token,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $response = curl_exec($curl);
        $estatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if($error){
            $this->genErrorLog('UtilsFunction','getEstadoInstancia',$error);
            return false;
        }

        if($estatus != 200){
            $this->genErrorLog('UtilsFunction','getEstadoInstancia','Estatus diferente a 200');
            return false;
        }

        $response = json_decode($response);

        $jsonError = json_last_error();

        if($jsonError != JSON_ERROR_NONE){
            $this->genErrorLog('UtilsFunction','getEstadoInstancia','Error al decodificar el json');
            return false;
        }

        if(!$response->exito){
            $this->genErrorLog('UtilsFunction','getEstadoInstancia','Error al obtener el estado de la instancia');
            return false;
        }


        /*
            "respuesta": {
                "exito": true,
                "estatus": {
                    "logueado": false,
                    "cargando": false,
                    "mensaje": null,
                    "state": null,
                    "multidispositivo": true
                },
                "numero": null
            }
        */
        return $response->respuesta;
    }
}
