<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ClassesModel;
use App\Models\SchedulesDayModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RequestInterface;

class Classes extends BaseController
{
    use ResponseTrait;

    public $notAuthorized = [
        'status' => 401,
        'error' => 'Not authorized'
    ];

    public function __construct()
    {
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $classesModel = new ClassesModel();

        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => $classesModel->getClasses()
        ]);
    }

    public function updateScheduledClassLink($scheduleDayId, $link)
    {
        $schedulesDayModel = new SchedulesDayModel();
        $schedulesDayModel->updateClassLink($scheduleDayId, $link);
        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => 'class succesfully updated !'
        ]);
    }

    public function dltClass($id)
    {
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

    public function attendences()
    {
        $classesModel = new ClassesModel();

        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => $classesModel->getClassesAttendences()
        ]);
    }

    public function addClass()
    {
        $usersModel = new UsersModel();

        if (!$usersModel->isUserAuthorized($this->request->getHeader('Authorization'))) {
            return $this->respond($this->notAuthorized);
        }

        $classesModel = new ClassesModel();
        $data = $this->request->getJSON('true');
        $newClass = $classesModel->addNewClass($data);

        return $this->respond([
            'status' => 201,
            'error' => null,
            'message' => "Class added!",
            "data" => $data
        ]);
    }
}
