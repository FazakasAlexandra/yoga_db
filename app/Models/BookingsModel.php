<?php

namespace App\Models;
use App\Models\SubscriptionsModel;
use CodeIgniter\Model;

class BookingsModel extends Model
{
/*     protected $allowedFields = ['name_surname', 'email', 'jwt', 'is_admin'];
    protected $table = 'users'; */


    function insertBooking($userId, $schedulesWeeksId, $classType)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('bookings');

        $builder->insert([
            'user_id' => $userId,
            'schedules_weeks_id' => $schedulesWeeksId,
            'class_type' => $classType
        ]);

        return $db->insertID();
    }

    function getUserBookings($userId){
        $db = \Config\Database::connect();
        $builder = $db->table('bookings');

        $userBookingsIds = $builder->where('user_id', $userId)->select('schedules_weeks_id')->get()->getResult('array');
        $result = array();
        foreach ($userBookingsIds as $booking) {
            array_push($result, $booking['schedules_weeks_id']);
        }
        return $result;
    }

    function getClassBookings($schedulesWeeksId){
        $db = \Config\Database::connect();
        $builder = $db->table('bookings_view');

        $subsModel = new SubscriptionsModel();
        
        $classBookings = $builder
        ->where('schedules_weeks_id',$schedulesWeeksId)
        ->where('state', 'pending')
        ->get()->getResultArray();

        foreach ($classBookings as &$booking) {
            $booking['user_subscriptions'] = $subsModel->getUserSubscriptionByClass($booking['user_id'], $booking['class_id']);
        }

        return $classBookings;
    }
}
