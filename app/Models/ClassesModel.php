<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassesModel extends Model
{
    protected $allowedFields = ['name', 'description', 'level', 'online_price', 'offline_price'];
    protected $table = 'classes';

    function getClasses(){
        $db = \Config\Database::connect();
        $builder = $db->table('classes');

        return $builder->get()->getResultArray();
    }
}
