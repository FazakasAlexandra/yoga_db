<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $allowedFields = ['name_surname', 'email', 'jwt', 'is_admin'];
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

        return $this->db->table('users')
                        ->where(['is_admin !=' => 'true'])
                        ->where('users.id =', $id)
                        ->join('bookings', 'bookings.user_id = users.id')
                        ->join('schedules_weeks', 'bookings.schedules_weeks_id = schedules_weeks.id') 
                        ->join('schedules_day', 'schedules_weeks.schedule_day_id = schedules_day.id')
                        ->join('classes', 'schedules_day.class_id = classes.id') 
                        ->get()
                        ->getResultArray();
    }

}
