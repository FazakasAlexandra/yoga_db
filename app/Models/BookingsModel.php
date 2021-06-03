<?php

namespace App\Models;

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

        $classBookings = $builder
        ->where('schedules_weeks_id',$schedulesWeeksId)
        ->where('state', 'pending')
        ->get()->getResultArray();
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
        return $query = $db->query("UPDATE bookings SET state = '" . $status . "' WHERE bookings.id = " . $id . " ");
    }
}
