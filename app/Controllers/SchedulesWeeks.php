<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SchedulesWeeksModel;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\UsersModel;
use App\Models\SchedulesDayModel;

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
    $schedulesDayModel = new SchedulesDayModel();

    return $this->respond([
      'status' => 200,
      'error' => null,
      'data' => $schedulesDayModel->getDaySchedule($date)
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

  public function postWeekSchedule()
  {
    $usersModel = new UsersModel();
    $schedulesDayModel = new schedulesDayModel();
    $schedulesWeekModel = new SchedulesWeeksModel();

    if (!$usersModel->isUserAuthorized($this->request->getHeader('Authorization'))) {
      return $this->respond($this->notAuthorized);
    }

    $weekSchedule = $this->request->getJSON('true');

    foreach ($weekSchedule as $daySchedule) {
      foreach ($daySchedule['schedule'] as $schedule) {
        $dayScheduleId = $schedulesDayModel->insertDaySchedule($schedule);

        $schedulesWeekModel->insertWeekSchedule([
          'schedule_day_id' => $dayScheduleId,
          'date_day' => $daySchedule['date_day'],
          'date_week_start' => $daySchedule['date_week_start'],
          'date_week_end' => $daySchedule['date_week_end']
        ]);
      }
    }

    return $this->setResponseFormat('json')->respond([
      'status' => 201,
      'error' => null,
      'data' => $weekSchedule
    ]);
  }
}
