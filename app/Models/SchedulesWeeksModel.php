<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class SchedulesWeeksModel extends Model
{
    function formatData($weekSchedulesData)
    {
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $weekSchedule = array();

        foreach ($days as $day) {
            $result = (object)
            $daySchedule = array();
            $result->day = $day;

            foreach ($weekSchedulesData as $schedule) {
                if ($day == $schedule['day']) {
                    $result->date = $schedule['date_day'];
                    $result->dateWeekStart = $schedule['date_week_start'];
                    $result->dateWeekEnd = $schedule['date_week_end'];

                    array_push($daySchedule, [
                        'class_id' => $schedule['class_id'],
                        'id' => uniqid(),
                        "schedules_weeks_id" => $schedule['schedules_weeks_id'],
                        "hour" => $schedule['hour'],
                        "name" => $schedule['class_name'],
                        "description" => $schedule['class_description'],
                        "level" => $schedule['class_level'],
                        "online_price" => $schedule['online_price'],
                        "offline_price" => $schedule['offline_price']
                    ]);
                }
            }

            $result->schedule = $daySchedule;
            array_push($weekSchedule, $result);
        }
        return $weekSchedule;
    }

    function getWeekSchedule($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('schedules_weeks_view');

        return $this->formatData($builder->where('date_day >=', $startDate)->where('date_day <=', $endDate)->get()->getResultArray());
    }

    function getMostRecentSchedule()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('most_recent_schedule');

        return $this->formatData($builder->get()->getResultArray());
    }

    function insertWeekSchedule($weekSchedule) {
        $db = \Config\Database::connect();
        $builder = $db->table('schedules_weeks');

        $builder->insert($weekSchedule);
    }
}
