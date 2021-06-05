<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionsModel extends Model
{
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
}