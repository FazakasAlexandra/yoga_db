<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ClassesModel;
use CodeIgniter\HTTP\RequestInterface;

class Classes extends BaseController
{
    use ResponseTrait;

    public function __construct(){
        $this->request = \Config\Services::request();
    }

    public function index(){
        $classesModel = new ClassesModel();

        return $this->respond([
        'status' => 201,
        'error' => null,
        'data' => $classesModel->getClasses()
        ]);
    }

    public function dltClass($id){
        $classesModel = new ClassesModel();
		$class = $classesModel->find($id);
		if ($class) {
			$classesModel->delete($id);
		};
		
        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => $classesModel->getClasses()
        ]);
    }
}
