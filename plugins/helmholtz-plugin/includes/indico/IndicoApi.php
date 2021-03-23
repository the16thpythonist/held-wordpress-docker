<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 29.05.18
 * Time: 17:13
 */

namespace Indico;


class IndicoApi
{

    private $translator;
    private $event_class;

    private $client;

    private $key;
    private $url;

    public function __construct($event_class=Event::class, $translator_class=IndicoEventTranslator::class)
    {
        /* @var $translator IndicoEventTranslator */
        $this->event_class = $event_class;
        $translator = new $translator_class();
        $this->translator = $translator;

        $this->client = new \GuzzleHttp\Client(array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json'
            )
        ));
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getCategory($category_id) {
        // Getting the raw data
        $data = $this->fetchCategory($category_id);
        $results = $data['results'];

        $events = array();
        foreach ($results as $result){
            // Translate the result array
            $this->translator->setSource($result);
            $params = $this->translator->translate();

            $event = new $this->event_class($params);
            $events[] = $event;
        }
        return $events;

    }

    private function fetchCategory($category_id) {
        $uri = $this->url . '/export/categ/' . $category_id . '.json';
        $options = array();
        $options['query']['ak'] = $this->key;

        $response = $this->client->get($uri, $options);
        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $json = json_decode($body, true);
            return $json;
        } else {
            return array();
        }

    }


}