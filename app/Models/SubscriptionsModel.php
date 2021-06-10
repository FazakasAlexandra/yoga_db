<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionsModel extends Model
{
    function decreaseSubscriptionCoverage($coverageType, $id){
        $table = 'users_subscriptions_'.$coverageType;
        $db = \Config\Database::connect();
        $builder = $db->table($table);

        $coverage = $builder->where(['id', $id])->get()->getRowObject();

        $builder->set('remained_entrences', +$coverage->remained_entrences - 1)->where(['id', $id])->update();
    }
    
    function getUserSubscriptions($userId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions_view');

        $userSubscriptions = $builder->where('user_id', $userId)->get()->getResultArray();

        foreach ($userSubscriptions as &$subscription) {
            $subscription['entrences'] = $this->getSubscriptionData($subscription['user_subscription_id'], 'users_subs_ent_view', 'user_subscription_id');
            $subscription['free_entrences'] = $this->getSubscriptionData($subscription['user_subscription_id'], 'users_subs_free_ent_view', 'user_subscription_id');
            $subscription['discounts'] = $this->getSubscriptionData($subscription['user_subscription_id'], 'users_subs_discounts_view', 'user_subscription_id');
        }

        return $userSubscriptions;
    }

    function getAll()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions');

        $subscriptions = $builder->get()->getResultArray();

        foreach ($subscriptions as &$subscription) {
            $subscription['discounts'] = $this->getSubscriptionData($subscription['id'], 'discounts', 'subscription_id');
            $subscription['free_entrences'] = $this->getSubscriptionData($subscription['id'], 'free_entrences', 'subscription_id');
            $subscription['entrences'] = $this->getSubscriptionData($subscription['id'], 'entrences', 'subscription_id');
        }

        return $subscriptions;
    }

    function getSubscriptionData($id, $view, $key)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($view);

        return $builder->where($key, $id)->get()->getResultArray();
    }

    // checks if a given subscription of a user has discount/free_entrences/entrences for a given class
    function getUserSubscriptionByClass($userId, $classId)
    {
        $userSubscriptions = $this->getUserSubscriptions($userId);

        $subscriptionData = array();

        foreach ($userSubscriptions as $subscription) {
            array_push($subscriptionData, [
                'subscription_name' => $subscription['subscription_name'],
                'subscription_id' => $subscription['subscription_id'],
                'checked_class_id' => $classId,
                'free_entrences' => $this->checkSubscriptionData($subscription, $classId, 'free_entrences'),
                'entrences' => $this->checkSubscriptionData($subscription, $classId, 'entrences'),
                'discounts' => $this->checkSubscriptionData($subscription, $classId, 'discounts')
            ]);
        }

        return $subscriptionData;
    }

    function checkSubscriptionData($subscription, $classId, $type)
    {
        $data = array();
        foreach ($subscription[$type] as $subscriptionData) {
            if (+$subscriptionData['class_id'] === +$classId) {
                array_push($data, $subscriptionData);
            }
        }
        return sizeof($data) > 0 ? $data : null;
    }

    function insertSubscription($subscription)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions');

        if ($subscription['image']) {
            $imageName = $this->storeImg($subscription['image']);
        } else {
            $imageName = "icon.png";
        }

        $builder->insert([
            "name" => $subscription['name'],
            "price" => $subscription['price'],
            "months" => $subscription['months'],
            "image" => $imageName
        ]);

        $subscriptionId = $db->insertID();
        $this->insertSubscriptionData($subscriptionId, $subscription['entrences'], 'entrences');
        $this->insertSubscriptionData($subscriptionId, $subscription['discounts'], 'discounts');
        $this->insertSubscriptionData($subscriptionId, $subscription['free_entrences'], 'free_entrences');

        $subscription['image'] = $imageName;
        $subscription['id'] = $subscriptionId;
        return $subscription;
    }

    function insertSubscriptionData($id, $data, $table)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($table);

        foreach ($data as $entrence) {
            $builder->insert([
                'subscription_id' => $id,
                'amount' => $entrence['amount'],
                'class_id' => $entrence['class_id'],
                'class_type' => $entrence['class_type']
            ]);
        }
    }

    function storeImg($base64Data)
    {
        $imgName = uniqid('subscription');
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));

        // FIX ME get my path dinamicaly
        $path = '/wamp64/www/yoga/public/assets/subscriptions/';

        file_put_contents($path . $imgName . '.png', $data);

        return $imgName . '.png';
    }

    // FIX ME should delete the images from directory too 
    function deleteSubscription($subscriptionId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions');

        $builder->delete(['id' => $subscriptionId]);
    }

    function getSubscriptionsForCombo(){
        $db = \Config\Database::connect();
        $query = $db->query("select subscriptions.id, subscriptions.name from subscriptions");
        $results = [];
        for($i = 0; $i < $query->getNumRows(); $i++){
            $results[$i] = $query->getRowArray($i);
        };
        return $results;
    }

    function getSubscriptionsByUser($id){
        $db = \Config\Database::connect();
        $query = $db->query("select users_subscriptions.user_id, users_subscriptions.id as usersSubscriptionID, users.name, subscriptions.id as subscriptionID, subscriptions.name, subscriptions.attendences as subscriptionAttendances, users_subscriptions.expiration_date, SUM(users_subscriptions_entrences.remained_entrences) as remainedEntrances, SUM(users_subscriptions_free_entrences.remained_entrences) as remainedFreeEntrances, subscriptions.image from users_subscriptions left join subscriptions on users_subscriptions.subscription_id = subscriptions.id left join users on users.id = users_subscriptions.user_id left join users_subscriptions_entrences on users_subscriptions_entrences.user_subscription_id = users_subscriptions.id left join users_subscriptions_free_entrences on users_subscriptions_free_entrences.user_subscription_id = users_subscriptions.id where users_subscriptions.user_id = ". $id . " group by users_subscriptions.id");
        $results = [];
        for($i = 0; $i < $query->getNumRows(); $i++){
            $results[$i] = $query->getRow($i);
        };
        return $results;
    }

    function getSubscriptionDetails($iduser){
        $db = \Config\Database::connect();
        $query = $db->query("select subscriptions.id, subscriptions.name, discounts_view.class_name, discounts_view.amount, discounts_view.class_type from subscriptions join discounts_view on subscriptions.id = discounts_view.subscription_id where subscriptions.id = ". $iduser);
        $results = [];
        for($i = 0; $i < $query->getNumRows(); $i++){
            $results[$i] = $query->getRow($i);
        };
        return $results;
    }

    function addSubscriptionToUser($iduser, $idsubscription){
        $db = \Config\Database::connect();
        $query = $db->query("SELECT subscriptions.months from subscriptions where subscriptions.id = " . $idsubscription);

        $subscriptionPeriod = $query->getRowArray();
        $newdate = 'DATE_ADD(CURDATE(), INTERVAL ' . $subscriptionPeriod['months'] .' MONTH))';
        $db->query("insert into `users_subscriptions` (`id`, `subscription_id`, `user_id`, `expiration_date`) VALUES (NULL, '". $idsubscription . "', '" . $iduser . "', " . $newdate);
        $query1 = $db->query("select users_subscriptions.id from users_subscriptions order by id desc limit 1");
        $query2 = $db->query("select entrences.id, entrences.amount from subscriptions join entrences on subscriptions.id = entrences.subscription_id where subscriptions.id = " . $idsubscription);
        $query3 = $db->query("select free_entrences.id, free_entrences.amount from subscriptions join free_entrences on subscriptions.id = free_entrences.subscription_id where subscriptions.id = " . $idsubscription);

        $newlyAddedUserSubID = $query1->getRowArray();
        $dataAboutEntrances = $query2->getResultArray();    
        $dataAboutFreeEntraces = $query3->getResultArray();

        foreach($dataAboutEntrances as $row){
            $db->query("insert into `users_subscriptions_entrences` (`user_subscription_id`, `entrences_id`, `id`, `remained_entrences`) values ('". $newlyAddedUserSubID['id'] ."', '". $row['id'] . "', NULL, '" . $row['amount'] . "')");
        }

        foreach($dataAboutFreeEntraces as $row){
            $db->query("insert into `users_subscriptions_free_entrences` (`user_subscription_id`, `free_entrence_id`, `id`, `remained_entrences`) values ('". $newlyAddedUserSubID['id'] ."', '". $row['id'] . "', NULL, '" . $row['amount'] . "')");
        }

    }

}
