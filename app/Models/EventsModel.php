<?php

namespace App\Models;

use CodeIgniter\Model;

class EventsModel extends Model
{
    protected $allowedFields = ['title', 'description', 'date', 'hour', 'image', 'link'];
    protected $table = 'events';

    function getEvents(){
        $db = \Config\Database::connect();
        $builder = $db->table('events');

        return $builder->orderBy('date', 'ASC')->get()->getResultArray();
    }

    function getUpcomingEvents($date){
        $db = \Config\Database::connect();
        $builder = $db->table('events');

        return $builder->where('date >=', $date)->orderBy('date', 'DESC')->get()->getResultArray();
    }

    function storeImg($base64Data)
    {
        $imgName = uniqid('event');
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
        $path = '/wamp64/www/yoga/public/assets/events/';

        file_put_contents($path . $imgName . '.png', $data);

        return $imgName . '.png';
    }

    function addNewEvent($data){
        $db = \Config\Database::connect();
        if ($data['image']) {
            $imageName = $this->storeImg($data['image']);
        } else {
            $imageName = "icon.png";
        }

        $db->query("insert into `events` (`id`, `title`, `description`, `image`, `date`, `hour`, `link`, `location`) VALUES (NULL, '". $data['name'] . "', '" . $data['description'] . "', '" . $imageName . "', '" . $data['date'] . "', '" . $data['hour'] . "', '" . $data['link'] . "', '" . $data['location'] . "')");
    }
}