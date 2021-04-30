<?php

namespace App\Models;

use CodeIgniter\Model;

class SchedulesDayModel extends Model
{
    function insertDaySchedule($daySchedule)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('schedules_day');

        $builder->insert($daySchedule);
        return $db->insertID();
    }
}
