<?php

namespace App\Models;
use App\Models\SubscriptionsModel;
use CodeIgniter\Model;

class BookingsModel extends Model
{
/*     protected $allowedFields = ['name_surname', 'email', 'jwt', 'is_admin'];
        protected $table = 'users'; */

       // protected $table = 'bookings';
       // protected $allowedFields = ['state'];

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

    function checkExistingBooking($userId, $schedulesWeeksId){
        $db = \Config\Database::connect();
        $builder = $db->table('bookings');

        return $builder
        ->where('user_id', $userId)
        ->where('schedules_weeks_id', $schedulesWeeksId)
        ->get()
        ->getRowObject();
    }

    function updateBooking($id, $classType, $state = 'pending'){
        $db = \Config\Database::connect();
        $builder = $db->table('bookings');
        $builder->update([
            'state' => $state,
            'class_type' => $classType
        ], 'id ='. $id);
    }

    function getUserBookings($userId){
        $db = \Config\Database::connect();
        $builder = $db->table('bookings');

        $userBookingsIds = $builder->where('user_id', $userId)->where('state !=', 'canceled')->select('schedules_weeks_id')->get()->getResult('array');
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

    function getBooking($id){
        $db = \Config\Database::connect();
        $builder = $db->table('bookings');
        $booking = $builder->where(['id =' => $id])->get()->getResultArray();
        return $booking;
    }
    
    function updateBookingStatus($id, $status){
        $db = \Config\Database::connect();
        return $db->query("UPDATE bookings SET state = '" . $status . "' WHERE bookings.id = " . $id . " ");
    }
}
