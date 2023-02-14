<?php 

namespace App\Controllers\Login;
use App\Controllers\BaseController;

/**
 * Controlador de login
 * 
 * @package App\Controllers\Login
 * @version 1.0
 * @since 1.0
 * @author kelevra16
 * @license 
 * @link 
 */
class LoginController extends BaseController
{
    private $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }


    public function index()
    {
        return view('page/login/login_view');
    }

    public function login()
    {

        $inputs = $this->validate([
            'username' => 'required|min_length[5]',
            'password' => 'required|min_length[5]',
        ], [
            'username' => [
                'required' => 'El campo usuario es requerido',
            ],
            'password' => [
                'required' => 'El campo contraseña es requerido',
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

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $username = htmlspecialchars($username);
        $password = htmlspecialchars($password);

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user->password)) {
            $returnData = [
                'status' => 200,
                'message' => 'Usuario o Contraseña incorrectos',
                'susses' => false,
                'url' => '',
            ];
            $this->session->destroy();
            return $this->response->setJSON($returnData);
        }

        $empresaModel = new \App\Models\EmpresaModel();
        $empresa = $empresaModel->where('id', $user->id_empresa)->first();
        $ses_data = [
            'idUser' => $user->id,
            'username' => $user->username,
            'role' => $user->id_rol,
            'idEmpresa' => $user->id_empresa,
            'tokenApi' => $empresa->tokenApi,
            'logged_in' => TRUE
        ];
        $this->session->set($ses_data);
        $returnData = [
            'status' => 200,
            'message' => 'Login exitoso',
            'susses' => true,
            'url' => base_url('campaign')
        ];

        return $this->response->setJSON($returnData);
    }

    public function createdUsuario(){
        // $data = $this->request->getPost();
        // $username = $data['username'];
        // $password = $data['password'];
        // $userModel = new \App\Models\UserModel();
        // $user = $userModel->where('username', $username)->first();
        // if ($user) {
        //     $this->session->setFlashdata('msg', 'Usuario ya existe');
        //     return redirect()->to(base_url());
        // } else {
        //     $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        //     $newData = [
        //         'username' => $username,
        //         'password' => $pass_hash
        //     ];
        //     $userModel->insert($newData);
        //     $this->session->setFlashdata('msg', 'Usuario creado');
        //     return redirect()->to(base_url());
        // }
    }

    private function generateHash($password){
        $options = [
            'cost' => 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('/'));
    }
}



?>