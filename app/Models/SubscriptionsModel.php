<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionsModel extends Model
{
    function getAll()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions');

        $subscriptions = $builder->get()->getResultArray();

        foreach ($subscriptions as &$subscription) {
            $subscription['discounts'] = $this->getDiscounts($subscription['id']);
            $subscription['free_entrences'] = $this->getFreeClasses($subscription['id']);
            $subscription['entrences'] = $this->getEntrences($subscription['id']);
        }

        return $subscriptions;
    }

    function getEntrences($subscriptionId){
        $db = \Config\Database::connect();
        $builder = $db->table('entrences_view');

        return $builder->where('subscription_id', $subscriptionId)->get()->getResultArray();
    }

    function getDiscounts($subscriptionId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('discounts_view');

        return $builder->where('subscription_id', $subscriptionId)->get()->getResultArray();
    }

    function getFreeClasses($subscriptionId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('free_entrences_view');

        return $builder->where('subscription_id', $subscriptionId)->get()->getResultArray();
    }

    function insertSubscription($subscription)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions');

        if($subscription['image']){
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
        $this->insertEntrences($subscriptionId, $subscription['entrences']);
        $this->insertDiscounts($subscriptionId, $subscription['discounts']);
        $this->insertFreeEntrences($subscriptionId, $subscription['free_entrences']);

        $subscription['image'] = $imageName;
        $subscription['id'] = $subscriptionId;
        return $subscription;
    }

    function insertEntrences($subscriptionId, $entrences){
        $db = \Config\Database::connect();
        $builder = $db->table('entrences');

        foreach ($entrences as $entrence) {
            $builder->insert([
                'subscription_id' => $subscriptionId,
                'amount' => $entrence['amount'],
                'class_id' => $entrence['class_id'],
                'class_type' => $entrence['class_type']
            ]);
        }
    }

    function insertDiscounts($subscriptionId, $discounts)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('discounts');

        foreach ($discounts as $discount) {
            $builder->insert([
                'subscription_id' => $subscriptionId,
                'amount' => $discount['amount'],
                'class_id' => $discount['class_id'],
                'class_type' => $discount['class_type']
            ]);
        }
    }

    function insertFreeEntrences($subscriptionId, $freeEntrences)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('free_entrences');

        foreach ($freeEntrences as $freeEntrence) {
            $builder->insert([
                'subscription_id' => $subscriptionId,
                'amount' => $freeEntrence['amount'],
                'class_id' => $freeEntrence['class_id'],
                'class_type' => $freeEntrence['class_type']
            ]);
        }
    }

    function storeImg($base64Data)
    {
        $imgName = uniqid('subscription');
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));

        // HOW TO GET PATH DINAMICALLY??
        $path = '/wamp64/www/yoga/public/assets/subscriptions/';

        file_put_contents($path . $imgName . '.png', $data);

        return $imgName.'.png';
    }

    /* should delete image from directory too */
    function deleteSubscription($subscriptionId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions');

        $builder->delete(['id' => $subscriptionId]);
    }
}
