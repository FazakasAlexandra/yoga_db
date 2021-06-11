<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ClassesModel;
use App\Models\SchedulesDayModel;
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

    public function updateScheduledClassLink($scheduleDayId, $link){
        $schedulesDayModel = new SchedulesDayModel();
        $schedulesDayModel->updateClassLink($scheduleDayId, $link);
        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => 'class succesfully updated !'
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

    public function attendences(){
        $classesModel = new ClassesModel();

        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => $classesModel->getClassesAttendences()
        ]);
    }

}
