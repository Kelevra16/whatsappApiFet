<?php

namespace App\Controllers\ApiWhatsApp;

use App\Controllers\BaseController;
use App\Models\ContactosModel;
use App\Models\CampaignModel;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\Log\Exceptions\LogException;
use Exception;
use InvalidArgumentException;

class ApiWhatsAppController extends BaseController
{
    private $url = "https://api.whatzmeapi.com/";
    private $sendMessageContacts = "own/enviar-mensaje-muchos-contactos";
    private $sendFileManyContacts = "own/enviar-archivo-muchos-contactos";
    private $assignWebhook = "own/webhook";
    private $logout = "own/cerrar-sesion";
    private $getQRCode = "own/codigo-qr";
    private $getState = "own/estado";
    private $getSubscriptionDetails = "own/suscripcion";
    private $sendTextMessage = "own/enviar-mensaje";
    private $sendFile = "own/enviar-archivo";
    private $clearMessageQueue = "own/limpiar-mensajes";
    private $messageQueueState = "own/cola-mensajes";

    /**
     * Enviar mensaje de texto a varios contactos
     * 
     * @param mixed $data 
     * @param mixed $contactos 
     * @param string $token 
     * @return mixed|boolean 
     */
    public function sendTextLotContact($data, $contactos, $token)
    {
        $urlSend = $this->url . $this->sendMessageContacts . '?token=' . $token;
        $arrayContacto = $this->arrayContactos($contactos);
        $dataSend = [
            'numeros' => $arrayContacto,
            'mensaje' => $data->mensaje,
        ];

        $response = $this->sendToApiPost($urlSend, $dataSend);

        if (!$response) {
            return false;
        }

        if ($response->status != 200) {
            return false;
        }

        return $response->response;
    }

    /**
     * Enviar imagen a varios contactos
     * 
     * @param mixed $data 
     * @param mixed $contactos 
     * @param string $name 
     * @param string $token 
     * @return mixed|boolean 
     */
    public function sendFilesLotContact($data, $contactos, $name, $token)
    {
        $urlArchivo = base_url() . "/" . $data->adjunto;
        $urlSend = $this->url . $this->sendFileManyContacts . '?token=' . $token;
        $arrayContacto = $this->arrayContactos($contactos);
        $dataSend = array(
            "numeros" => $arrayContacto,
            "url" => $urlArchivo,
            "nombrearchivo" => $name,
            "textoimagen" => $data->mensaje,
            "nombreCampania" => $data->titulo
        );

        $response = $this->sendToApiPost($urlSend, $dataSend);

        if (!$response) {
            return false;
        }

        if ($response->status != 200) {
            return false;
        }

        return $response->response;
        
    }

    /**
     * Envió de mensaje de texto a un solo contacto
     * @param string $data 
     * @param string $token 
     * @return mixed 
     */ 
    public function sendWhatsAppText($data, string $token)
    {
        $urlSend = $this->url . $this->sendTextMessage . '?token=' . $token;
        
        $dataSend = json_decode($data);

        try {
            $response = $this->sendToApiPost($urlSend, $dataSend);

            if (!$response) {
                log_message('error', 'Error al enviar mensaje de texto a un solo contacto');
                return false;
            }
    
            if ($response->status != 200) {
                log_message('error', 'Error al enviar mensaje de texto a un solo contacto estatus != 200');
                return false;
            }
    
            return $response->response;

        } catch (\Throwable $th) {
            log_message('error', 'Error al enviar mensaje de texto a un solo contacto' . $th->getMessage());
            return false;
        }

    }

    /**
     * Envió de imagen a un solo contacto
     * @param string $data 
     * @param string $token 
     * @return mixed 
     */
    public function sendWhatsAppImage($data, string $token)
    {
        $urlSend = $this->url . $this->sendFile . '?token=' . $token;
        $dataSend = json_decode($data);

        try {
            $response = $this->sendToApiPost($urlSend, $dataSend);

            if (!$response) {
                log_message('error', 'Error al enviar imagen a un solo contacto');
                return false;
            }
    
            if ($response->status != 200) {
                log_message('error', 'Error al enviar imagen a un solo contacto estatus != 200');
                return false;
            }
    
            return $response->response;
        } catch (\Throwable $th) {
            log_message('error', 'Error al enviar imagen a un solo contacto' . $th->getMessage());
            return false;
        }
    }

    /**
     * traer QR de la API
     * 
     * @param string $token 
     * @return mixed 
     */
    public function getQr($token){
        $curl = curl_init();
        $urlSend = $this->url . $this->getQRCode . '?token=' . $token;

        curl_setopt_array($curl, array(
          CURLOPT_URL => $urlSend,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $estatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        // $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);

        if($error){
            return false;
        }

        if($estatus != 200){
            return false;
        }

        // if(is_string($response)){
        //     $response = json_decode($response);
        //     $jsonError = json_last_error();

        //     if($jsonError != JSON_ERROR_NONE){
        //         $this->genErrorLog('UtilsFunction','getQr','Error al decodificar el json');
        //         return $response;
        //     }
    
        //     if(!$response->success){
        //         $this->genErrorLog('UtilsFunction','getQr','Error al obtener el estado de la instancia');
        //         return false;
        //     }
        // }

        return $response;
    }

    /**
     * Obtener estado de la instancia
     * 
     * @param string $token 
     * @return mixed|boolean
     */
    public function getEstadoInstancia($token){

        $urlSend = $this->url . $this->getState . '?token=' . $token;
        $response = $this->sendToApiGet($urlSend);

        if (!$response) {return false;}
        if ($response->status != 200) {return false;}

        if(!$response->response->exito){
            return false;
        }

        return $response->response->respuesta;
    }

    /**
     * Create a array of contacts
     * 
     * @param mixed $contactos 
     * @return array 
     */
    private function arrayContactos($contactos)
    {
        $arrayContactos = [];
        foreach ($contactos as $contacto) {
            $arrayContactos[] = $contacto->lada . $contacto->telefono;
        }
        return $arrayContactos;
    }

    /**
     * @param string $url
     * @param mixed $data
     * @description Función para enviar peticiones POST a la API de WhatsApp 
     * @return mixed|boolean
     */
    private function sendToApiPost(string $url, $data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $dataReturn = [
            'status' => $status,
            'response' => null
        ];

        if (curl_errno($curl)) {
            curl_close($curl);
            return false;
        }

        curl_close($curl);

        try {
            $dataReturn['response'] = json_decode($response);
        } catch (Exception $e) {
            return false;
        }

        return json_decode(json_encode($dataReturn));
    }

    /**
     * @param string $url
     * @param mixed $param
     * @description Función para enviar peticiones GET a la API de WhatsApp
     * @return mixed|null
     */
    private function sendToApiGet(string $url, $param = null)
    {
        $curl = curl_init();

        if ($param !== null) {
            $url = $url . '?' . http_build_query($param);
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $dataReturn = [
            'status' => $status,
            'response' => null,
        ];

        if (curl_errno($curl)) {
            curl_close($curl);
            return false;
        }

        curl_close($curl);

        try {
            $dataReturn['response'] = json_decode($response);
        } catch (Exception $e) {
            return false;
        }

        return json_decode(json_encode($dataReturn));
    }

    /**
     * Genera un log de error en el archivo de log de codeigniter
     * @param string $modulo 
     * @param string $function 
     * @param string $message 
     * @param string $tipo debug|info|notice|warning|error|critical|alert|emergency
     * @return void 
     * @throws LogException 
     */
    private function genErrorLog($modulo, $function, $message,$tipo = 'alert'){
        $returnData = [
            'modulo ' => $modulo,
            'function' => $function,
            'message' => $message
        ];

        log_message($tipo,json_encode($returnData));
    }

}




// {
//     "type": "error",
//     "product_id": "2c480256-158b-456d-84bd-74f87b35c69d",
//     "phone_id": 26412,
//     "code": "E01",
//     "message": "The contact cannot be found!",
//     "data": {
//         "to_number": "5219656516933@c.us",
//         "type": "media",
//         "message": "https:\/\/mensajes.fec-chiapas.com.mx\/public\/\/viewfile\/20230328\/1680020172_BOLETIN TURISMO MAR 2023.png",
//         "text": "Boletín del Sector Turismo",
//         "id": "ad572f70-cd85-11ed-a334-49b60a5a75cc"
//     },
//     "phoneId": 26412
// },
// {
//     "type": "ack",
//     "product_id": "2c480256-158b-456d-84bd-74f87b35c69d",
//     "phone_id": 26412,
//     "data": [
//         {
//             "ackType": "delivered",
//             "ackCode": 1,
//             "chatId": "5219616588011@c.us",
//             "msgId": "aa461ee0-cd85-11ed-a7d8-93697770b8aa",
//             "time": 1680020948
//         }
//     ],
//     "phoneId": 26412
// }
