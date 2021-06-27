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

        $classes = $builder->get()->getResultArray();
        foreach ($classes as &$yogaClass) {
            $yogaClass['attendences'] = $this->countSingleClassAttendences($yogaClass['id']);
        }
        return $classes;
    }
    
    function countSingleClassAttendences($classId){
        $db = \Config\Database::connect();
        $builder = $db->table('bookings_view');
        $attendences = $builder->where('class_id', $classId)->where('state', 'present')->get()->getResultArray();
        return count($attendences);
    }

    function getDailyPresence($id){
        $db = \Config\Database::connect();
        $builder = $db->table('classes_daily_attendences');
        
        if($id == 'all') 
        {
            $attendences = $builder->orderBy('date_day', 'ASC')->get()->getResultArray();
        }else
        {   
            $attendences = $builder->where('class_id', $id)->orderBy('date_day', 'ASC')->get()->getResultArray();
        }
        
        return $attendences;
    }

    function addNewClass($data){
        $db = \Config\Database::connect();
        $db->query("insert into `classes` (`id`, `name`, `description`, `level`, `online_price`, `offline_price`) VALUES (NULL, '". $data['name'] . "', '" . $data['description'] . "', '" . $data['level'] . "', '" . $data['online'] . "', '" . $data['offline'] . "')");
    }
}
