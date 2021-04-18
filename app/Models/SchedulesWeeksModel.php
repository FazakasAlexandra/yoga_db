<?php

namespace App\Models;

use CodeIgniter\Model;

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
                        "schedulesWeeksId" => $schedule['schedules_weeks_id'],
                        "hour" => $schedule['hour'],
                        "className" => $schedule['class_name'],
                        "classDescription" => $schedule['class_description'],
                        "classLevel" => $schedule['class_level'],
                        "onlinePrice" => $schedule['online_price'],
                        "offlinePrice" => $schedule['offline_price']
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
}
