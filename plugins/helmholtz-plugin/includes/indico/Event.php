<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.05.18
 * Time: 17:34
 */

namespace Indico;


class Event
{
    private $default = array(
        'id' => '0',
        'type' => 'event',
        'title' => '',
        'description' => '',
        'location' => '',
        'address' => '',
        'url' => '',
        'start_date' => array(),
        'end_date' => array(),
        'modification_date' => array(),
        'creator' => array()
    );

    private $id;
    private $type;
    private $title;
    private $description;
    private $location;
    private $address;
    private $url;
    private $start_date;
    private $end_date;
    private $modification_date;
    private $creator;

    public function __construct($data)
    {
        $parameters = array_replace($this->default, $data);
        $this->load($parameters);
    }

    private function load(array $parameters) {
        $this->id = $parameters['id'];
        $this->type = $parameters['type'];
        $this->title = $parameters['title'];
        $this->description = $parameters['description'];
        $this->location = $parameters['location'];
        $this->address = $parameters['address'];
        $this->url = $parameters['url'];
        $this->start_date = new Time($parameters['start_time']);
        $this->end_date = new Time($parameters['end_time']);
        $this->modification_date = new Time($parameters['modification_time']);
        $this->creator = new Creator($parameters['creator']);
    }

    public function getId() {
        return $this->id;
    }

    public function getStartTime() {
        return $this->start_date;
    }

    public function getEndTime() {
        return $this->end_date;
    }

    public function getModificationTime() {
        return $this->modification_date;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function getLocation() {
        return $this->location;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getType() {
        return $this->type;
    }

}