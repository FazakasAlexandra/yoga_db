<?php

namespace App\Models;

use CodeIgniter\Model;


class UsersModel extends Model
{
    protected $allowedFields = ['name', 'email', 'jwt', 'is_admin'];
    protected $table = 'users';

    public function isUserAuthorized($jwtHeader)
    {
        if (!$jwtHeader) return false;

        $user = $this->queryUser('jwt', $jwtHeader->getValue());

        if (!$user || $user->is_admin !== 'true') return false;

        return true;
    }

    function getAll()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        return $builder->get()->getResultArray();
    }

    function queryUser($key, $value)
    {
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

    function userClients($text)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        if($text == 'all') 
        {
            $clients = $builder->where(['is_admin !=' => 'true'])
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
            
        }else
        {   
            $clients = $builder->where(['is_admin !=' => 'true'])
            ->where('jwt', $text)
            ->get()
            ->getResultArray();
        }
        
        return $clients;
    }

    function userClientsHistory($id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT
        users.id AS user_id,
        users.is_admin,
        users.jwt,
        bookings.id AS booking_id,
        bookings.class_type,
        bookings.state,
        schedules_weeks.day,
        schedules_weeks.hour,
        classes.id AS classes_id,
        classes.name,
        classes.description,
        classes.level,
        classes.online_price,
        classes.offline_price
    FROM
        users
    INNER JOIN bookings ON bookings.user_id = users.id
    INNER JOIN schedules_weeks ON bookings.schedules_weeks_id = schedules_weeks.id
    INNER JOIN classes ON schedules_weeks.class_id = classes.id
    WHERE
        is_admin = FALSE AND users.id =" . $id . " ");
        
        $results = [];
        for ($i = 0; $i < $query->getNumRows(); $i++) {
            $results[$i] = $query->getRowArray($i);
        };
        return $results;
    }
}
