<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.05.18
 * Time: 17:28
 */

namespace Indico;


class Creator
{
    private $default = array(
        'full_name' => '',
        'id' => '0',
        'affiliation' => ''
    );

    private $full_name;
    private $id;
    private $affiliation;

    public function __construct(array $data)
    {
        $parameters = array_replace($this->default, $data);
        $this->load($parameters);
    }

    private function load(array $parameters) {
        $this->full_name = $parameters['name'];
        $this->affiliation = $parameters['affiliation'];
        $this->id = $parameters['id'];
    }

    public function getId() {
        return $this->id;
    }

    public function getFullName() {
        return $this->full_name;
    }

    public function getAffiliation() {
        return $this->affiliation;
    }

}