<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SchedulesWeeksModel;
use App\Models\UsersModel;
use App\Models\BookingsModel;
use CodeIgniter\HTTP\RequestInterface;

class Bookings extends BaseController
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
    }

    public function getClassBookings($weekScheduleId)
    {
        $bookingsModel = new BookingsModel();

        return $this->setResponseFormat('json')->respond([
            'status' => 201,
            'error' => null,
            'data' => $bookingsModel->getClassBookings($weekScheduleId)
        ]);
    }

    public function postBooking($weekScheduleId, $classType)
    {
        $usersModel = new UsersModel();
        $jwt = $this->request->getHeader('Authorization');

        if (!$usersModel->isUserAuthorized($jwt)) {
            return $this->respond($this->notAuthorized);
        }

        $user = $usersModel->queryUser('jwt', $jwt->getValue());
        $bookingsModel = new BookingsModel();
        $bookingsModel->insertBooking($user->id, $weekScheduleId, $classType);

        return $this->setResponseFormat('json')->respond([
            'status' => 201,
            'error' => null,
            'data' => [
                'message' => 'class successfully booked !'
            ]
        ]);
    }

    // public function getBooking($id){
    //     $bookingsModel = new BookingsModel();
    //     $booking = $bookingsModel->getBooking($id);
    //     // return $this->respond([
    //     //     'status' => 201,
    //     //     'error' => null,
    //     //     'data' => $booking
    //     //     ]);
    //     var_dump($booking);
    //     die();
    // }

    public function chgStatus($id, $status)
    {
        if($status != 'canceled'){
            $usersModel = new UsersModel();
            $jwt = $this->request->getHeader('Authorization');

            if (!$usersModel->isUserAuthorized($jwt)) {
                return $this->respond($this->notAuthorized);
            }
        }

        $bookingsModel = new BookingsModel();
        $booking = $bookingsModel->getBooking($id);
        if (!empty($status)) {
            $bookingsModel->updateBookingStatus($id, $status);
        }
        $newbooking = $bookingsModel->getBooking($id);
        return $this->setResponseFormat('json')->respond([
            'status' => 201,
            'error' => null,
            'message' => 'Status successfully modified!',
            'data' => [
                'newbooking' => $newbooking
            ]
        ]);
    }
}
