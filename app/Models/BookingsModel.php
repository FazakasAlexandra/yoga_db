<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingsModel extends Model
{
/*     protected $allowedFields = ['name_surname', 'email', 'jwt', 'is_admin'];
    protected $table = 'users'; */


    function insertBooking($userId, $schedulesWeeksId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('bookings');

        $builder->insert([
            'user_id' => $userId,
            'schedules_weeks_id' => $schedulesWeeksId
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
}
