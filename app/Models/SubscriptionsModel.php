<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

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
        }

        return $subscriptions;
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

        $imageName = $this->storeImg($subscription['image']);

        $builder->insert([
            "name" => $subscription['name'],
            "attendences" => $subscription['attendences'],
            "price" => $subscription['price'],
            "months" => $subscription['months'],
            "image" => $imageName
        ]);

        $subscriptionId = $db->insertID();
        $this->insertDiscounts($subscriptionId, $subscription['discounts']);
        $this->insertFreeEntrences($subscriptionId, $subscription['free_entrences']);

        $subscription['image'] = $imageName;
        $subscription['id'] = $subscriptionId;
        return $subscription;
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

    function insertDiscounts($subscriptionId, $discounts)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('discounts');

        foreach ($discounts as $discount) {
            $builder->insert([
                'subscription_id' => $subscriptionId,
                'discount' => $discount['discount'],
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

    /* should delete image from directory too */
    function deleteSubscription($subscriptionId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions');

        $builder->delete(['id' => $subscriptionId]);
    }
}
