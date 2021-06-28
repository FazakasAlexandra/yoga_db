<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\EventsModel;
use App\Models\UsersModel;

class Events extends BaseController
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
        $eventsModel = new EventsModel();

        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => $eventsModel->getEvents()
        ]);
    }

    public function getUpcomingEvents($date)
    {
        $eventsModel = new EventsModel();

        return $this->respond([
            'status' => 201,
            'error' => null,
            'data' => $eventsModel->getUpcomingEvents($date)
        ]);
    }

    public function addEvent()
    {
        $usersModel = new UsersModel();

        if (!$usersModel->isUserAuthorized($this->request->getHeader('Authorization'))) {
            return $this->respond($this->notAuthorized);
        }

        $eventModel = new EventsModel();
        $data = $this->request->getJSON('true');
        $newEvent = $eventModel->addNewEvent($data);

        return $this->respond([
            'status' => 201,
            'error' => null,
            'message' => "Event added!",
        ]);
    }

    public function dltEvent($id, $img)
    {
        $eventsModel = new EventsModel();
        $event = $eventsModel->find($id);
        if ($event) {
            $eventsModel->deleteEvent($id, $img);
        };

        return $this->respond([
            'status' => 201,
            'error' => null,
            'message' => 'event deleted succesfully',
            'data' => $eventsModel->getEvents()
        ]);
    }

}