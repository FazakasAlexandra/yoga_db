<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RequestInterface;

class ClientsHistory extends BaseController
{
    use ResponseTrait;

    public function __construct(){
        $this->request = \Config\Services::request();
    }

    public function index(){

    }

    public function client($id){
        $usersModel = new UsersModel();
        $data = $usersModel->userClientsHistory($id);
        
        if(empty($data)){
            return $this->respond([
                'status' => 201,
                'error' => 'user is admin'
            ]);
        }else{
            return $this->respond([
                'status' => 201,
                'error' => null,
                'data' => $usersModel->userClientsHistory($id)
                ]);
        }
    }

}
