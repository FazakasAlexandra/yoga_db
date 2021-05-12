<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SubscriptionsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RequestInterface;

class Subscriptions extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $subscriptionsModel = new SubscriptionsModel();

        return $this->respond([
            'status' => 200,
            'error' => null,
            'data' => $subscriptionsModel->getAll()
        ]);
    }

    public function deleteSubscription($id)
    {
        $subscriptionsModel = new SubscriptionsModel();
        $subscriptionsModel->deleteSubscription($id);
        return $this->respond([
            'status' => 200,
            'error' => null,
            'message' => "Subscription deleted !"
        ]);
    }

    public function addSubscription()
    {
        $usersModel = new UsersModel();
        $jwt = $this->request->getHeader('Authorization')->getValue();
        $user = $usersModel->queryUser('jwt', $jwt);

        if (!$user) {
            return $this->respond([
                'status' => 401,
                'error' => 'Not authorized'
            ]);
        }

        $subscriptionsModel = new SubscriptionsModel();
        $subscription = $this->request->getJSON('true');
        $subscriptionsModel->insertSubscription($subscription);

        return $this->respond([
            'status' => 201,
            'error' => null,
            'message' => "Subscription added !"
        ]);
    }
}
