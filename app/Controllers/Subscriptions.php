<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SubscriptionsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RequestInterface;

class Subscriptions extends BaseController
{
    use ResponseTrait;

    public $notAuthorized = [
        'status' => 401,
        'error' => 'Not authorized'
    ];

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

        if(!$usersModel->isUserAuthorized($this->request->getHeader('Authorization'))){
            return $this->respond($this->notAuthorized);
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

    public function deleteSubscription($id, $image)
    {
        $subscriptionsModel = new SubscriptionsModel();
        $subscriptionsModel->deleteSubscription($id, $image);
        return $this->respond([
            'status' => 200,
            'error' => null,
            'message' => "Subscription deleted !"
        ]);
    }

    public function addSubscription()
    {
        $usersModel = new UsersModel();

        if(!$usersModel->isUserAuthorized($this->request->getHeader('Authorization'))){
            return $this->respond($this->notAuthorized);
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

        if(!$usersModel->isUserAuthorized($this->request->getHeader('Authorization'))){
            return $this->respond($this->notAuthorized);
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
