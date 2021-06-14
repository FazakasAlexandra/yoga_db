<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SubscriptionsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RequestInterface;

class Subscriptions extends BaseController
{
    use ResponseTrait;

    public function decreaseSubscriptionCoverage($coverageType, $id){
        $subscriptionsModel = new SubscriptionsModel();
        $remainedEntrences = $subscriptionsModel->decreaseSubscriptionCoverage($coverageType, $id);
        return $this->respond([
            'status' => 204,
            'error' => null,
            'data' => $remainedEntrences
        ]);
    }

    public function userSubscriptions($userId){
        $subscriptionsModel = new SubscriptionsModel();

        return $this->respond([
            'status' => 200,
            'error' => null,
            'data' => $subscriptionsModel->getUserSubscriptions($userId)
        ]);
    }

    public function removeUserSubscription($userSubscriptionId){
        $usersModel = new UsersModel();
        $jwt = $this->request->getHeader('Authorization');

        if(!$jwt || !$usersModel->isUserAuthorized($jwt->getValue())){
            return $this->respond([
                'status' => 401,
                'error' => 'Not authorized'
            ]);
        }

        $subscriptionsModel = new SubscriptionsModel();
        $subscriptionsModel->removeUserSubscription($userSubscriptionId);

        return $this->respond([
            'status' => 200,
            'error' => null,
            'message' => 'Subscription successfully removed !' 
        ]);
    }

    public function getUserSubscriptionByClass($userid, $classid){
        $subscriptionsModel = new SubscriptionsModel();
        $data = $subscriptionsModel->getUserSubscriptionByClass($userid, $classid);

        return $this->respond([
            'status' => 200,
            'error' => null,
            'data' => $data
        ]);
    }

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
        $newSubscription = $subscriptionsModel->insertSubscription($subscription);

        return $this->respond([
            'status' => 201,
            'error' => null,
            'message' => "Subscription added !",
            "data" => $newSubscription
        ]);
    }

    public function getSubscriptionsNames()
    {
        $subscriptionsModel = new SubscriptionsModel();
        $data = $subscriptionsModel->getSubscriptionsForCombo();
        return $this->respond([
            'status' => 200,
            'error' => null,
            "data" => $data
        ]);
    }

    public function addSubscriptionToUser($iduser, $idsubscription){
        $usersModel = new UsersModel();
        $jwt = $this->request->getHeader('Authorization')->getValue();
        $user = $usersModel->queryUser('jwt', $jwt);

        if (!$user) {
            return $this->respond([
                'status' => 401,
                'error' => 'Not authorized',
                'data' => $jwt,
                'user' => $user
            ]);
        }


        $subscriptionsModel = new SubscriptionsModel();
        $data = $subscriptionsModel->addSubscriptionToUser($iduser, $idsubscription);
        
        return $this->respond([
            'status' => 201,
            'error' => null,
            'message' => "Subscription added !",
            "data" => $data
        ]);
    }

}
