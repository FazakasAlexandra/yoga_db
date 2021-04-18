<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SchedulesWeeksModel;
use CodeIgniter\HTTP\RequestInterface;

class SchedulesWeeks extends BaseController
{
  use ResponseTrait;

  public function __construct()
  {
    $this->request = \Config\Services::request();
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
}
