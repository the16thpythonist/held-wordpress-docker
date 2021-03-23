<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.05.18
 * Time: 18:01
 */

namespace Indico;


class IndicoEventTranslator
{

    private $event_class;
    private $data;

    public function __construct($event_class=Event::class)
    {
        $this->data = array();
        $this->event_class = $event_class;
    }

    public function setSource(array $data) {
        $this->data = $data;
    }

    public function translate() {
        return $this->map(
            array(
                'id' => 'id',
                'type' => 'type',
                'title' => 'title',
                'description' => 'description',
                'location' => 'location',
                'address' => 'address',
                'url' => 'url',
                'creator' => function($target){return $this->creatorArray($target, 'creator');},
                'startDate' => function($target){return $this->timeArray($target, 'start_time');},
                'endDate' => function($target){return $this->timeArray($target, 'end_time');},
                'modificationDate' => function($target){return $this->timeArray($target, 'modification_time');},
            )
        );
    }

    private function map($mapping) {
        $mapped_array = array();
        foreach ($mapping as $old_key => $new_key) {
            $target = $this->query($old_key);
            if (is_string($new_key)) {
                $mapped_array[$new_key] = $target;
            } else {
                // It has to be a function if it isnt a string
                $result = $new_key($target);
                $key = $result['key'];
                $value = $result['value'];
                $mapped_array[$key] = $value;
            }
        }
        return$mapped_array;
    }

    public function query($query, $default='') {
        // Splitting the query for the individual keys
        $keys = explode('/', $query);

        $current = $this->data;
        foreach ($keys as $key) {
            try {
                $current = $current[$key];
            } catch (\Exception $e) {
                return $default;
            }
        }
        return $current;
    }

    private function timeArray(array $target, $key) {
        $_data = $this->data;
        $this->setSource($target);
        $result = array(
            'key' => $key,
            'value' => array(
                'time' => $this->query('time'),
                'date' => $this->query('date'),
                'timezone' => $this->query('tz')
            )
        );
        $this->setSource($_data);
        return $result;
    }

    private function creatorArray(array $target, $key) {
        $_data = $this->data;
        $this->setSource($target);
        $result = array(
            'key' => $key,
            'value' => array(
                'name' => $this->query('fullName'),
                'id' => $this->query('id'),
                'affiliation' => $this->query('affiliation')
            )
        );
        $this->setSource($_data);
        return $result;
    }



}