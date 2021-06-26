<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SchedulesWeeksModel;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\UsersModel;

class SchedulesWeeks extends BaseController
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

  public function getDaySchedule($date)
  {
    $schedulesModel = new SchedulesWeeksModel();

    return $this->respond([
      'status' => 200,
      'error' => null,
      'data' => $schedulesModel->getDaySchedule($date)
    ]);
  }

  public function index($startDate, $endDate)
  {
    $schedulesModel = new SchedulesWeeksModel();

    return $this->respond([
      'status' => 201,
      'error' => null,
      'data' => $schedulesModel->getWeekSchedule($startDate, $endDate)
    ]);
  }

  public function mostRecent()
  {
    $schedulesModel = new SchedulesWeeksModel();

    return $this->setResponseFormat('json')->respond([
      'status' => 201,
      'error' => null,
      'data' => $schedulesModel->getMostRecentSchedule()
    ]);
  }

  public function updateScheduledClassLink($scheduleWeekId, $link)
  {
    $SchedulesWeeksModel = new SchedulesWeeksModel();
    $SchedulesWeeksModel->updateClassLink($scheduleWeekId, $link);
    return $this->respond([
      'status' => 201,
      'error' => null,
      'data' => 'class succesfully updated !'
    ]);
  }

  public function postWeekSchedule($startDate, $endDate)
  {
    $usersModel = new UsersModel();

    if (!$usersModel->isUserAuthorized($this->request->getHeader('Authorization'))) {
      return $this->respond($this->notAuthorized, 401);
    }

    $schedulesWeekModel = new SchedulesWeeksModel();
    $weekSchedule = $this->request->getJSON('true');
    $message = 'The new week schedule was added!';

    $scheduleError = $schedulesWeekModel->validation(count($weekSchedule), $startDate, $endDate);

    if ($scheduleError['error']) {
      if ($scheduleError['errorType'] == 'overlap') {
        $schedulesWeekModel->deleteSchedule($startDate);
        $message = 'The schedule for '
          . date('d F', strtotime($startDate))
          . ' to '
          . date('d F', strtotime($endDate))
          . ' was replaced !';
      } else {
        return $this->failValidationError($scheduleError['errorType'], null, $scheduleError['errorMessage']);
      }
    }

    $schedulesWeekModel->insertWeekSchedule($weekSchedule, $startDate, $endDate);

    return $this->setResponseFormat('json')->respond([
      'error' => $scheduleError['errorType'],
      'message' => $message
    ], 201);
  }
}
