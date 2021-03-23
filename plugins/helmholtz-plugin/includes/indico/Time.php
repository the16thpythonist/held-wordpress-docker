<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.05.18
 * Time: 17:19
 */

namespace Indico;

class Time
{

    private $default = array(
        'date' => '',
        'time' => '',
        'timezone' => '',
    );

    private $time;
    private $date;
    private $timezone;

    public function __construct(array $data)
    {
        // Replacing the default values with the parameters
        $parameters = array_replace($this->default, $data);
        $this->load($parameters);

    }

    private function load(array $parameters) {
        $this->time = $parameters['time'];
        $this->date = $parameters['date'];
        $this->timezone = $parameters['timezone'];
    }

    public function getTime() {
        return $this->time;
    }

    public function getDate() {
        return $this->date;
    }

    public function getDateTime() {
        return $this->getDate() . ' ' . $this->getTime();
    }

    public function getTimezone() {
        return $this->timezone;
    }

}