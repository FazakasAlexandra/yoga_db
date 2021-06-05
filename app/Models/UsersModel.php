<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $allowedFields = ['name', 'email', 'jwt', 'is_admin'];
    protected $table = 'users';

    function getAll()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        return $builder->get()->getResultArray();
    }

    function queryUser($key, $value){
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        return $builder->where($key, $value)->get()->getRowObject();
    }

    function insertUser($user)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->insert($user);
        return $db->insertID();
    }

    function userClients(){
        $db = \Config\Database::connect();
        return $this->db->table('users')
                        ->where(['is_admin !=' => 'true'])
                        ->orderBy('name', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    function userClientsHistory($id){
        $db = \Config\Database::connect();

        $query = $db->query("Select users.id as user_id, users.is_admin, users.jwt, bookings.id as booking_id, bookings.class_type, bookings.state, schedules_day.day, schedules_day.hour, classes.id as classes_id, classes.name, classes.description, classes.level, classes.online_price, classes.offline_price from users inner join bookings on bookings.user_id = users.id inner join schedules_weeks on bookings.schedules_weeks_id = schedules_weeks.id inner join schedules_day on schedules_weeks.schedule_day_id = schedules_day.id inner join classes on schedules_day.class_id = classes.id where is_admin = false and users.id = " . $id . " ");
        $results = [];
        for($i = 0; $i < $query->getNumRows(); $i++){
            $results[$i] = $query->getRow($i);
        };
        return $results;
    }

}
