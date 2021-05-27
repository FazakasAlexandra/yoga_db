<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\UsersModel;
use App\Models\BookingsModel;
use CodeIgniter\HTTP\RequestInterface;

class Users extends BaseController
{
  use ResponseTrait;

  public function __construct()
  {
    $this->request = \Config\Services::request();
  }

  public function index()
  {
    $usersModel = new UsersModel();

    return $this->respond([
      'status' => 201,
      'error' => null,
      'data' => $usersModel->getAll()
    ]);
  }

  public function post()
  {
    /*
    $jwt = $this->request->getHeader('Authorization')->getValue();
    $user = $this->queryUser('jwt', $jwt);

    // only a registered and admin user can perform this acction
    if (!$user || !$user->is_admin) {
      return $this->respond([
        'status' => 401,
        'error' => 'Not authorized'
      ]);
    } 
    */

    $usersModel = new UsersModel();
    $userId = $usersModel->insert($this->request->getJSON('true'));
    $newUser = $this->queryUser('id', $userId);

    return $this->respond([
      'status' => 200,
      'error' => null,
      'data' => $newUser
    ]);
  }

  public function getUser($key, $value)
  {
    $usersModel = new UsersModel();
    $bookingsModel = new BookingsModel();
    $user = $usersModel->queryUser($key, $value);
    $user->bookingIds = $bookingsModel->getUserBookings($user->id);

    return $this->setResponseFormat('json')->respond([
      'status' => 200,
      'data' => $user
    ]);
  }

  public function queryUser($key, $value)
  {
    $usersModel = new UsersModel();

    return $usersModel->queryUser($key, $value);
  }

  public function usersNonAdm(){
    $usersModel = new UsersModel();
    return $this->respond([
      'status' => 201,
      'error' => null,
      'data' => $usersModel->userClients()
    ]);
  }
}
