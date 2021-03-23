<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 29.05.18
 * Time: 17:40
 */

use PHPUnit\Framework\TestCase;

use Indico\IndicoApi;
use Indico\Event;

final class TestIndicoApi extends TestCase
{

    public function testIndicoApiCall() {
        $key = "829e3826-39ad-4be3-b50f-1d25397e67bd";
        $url = "https://indico.desy.de/indico";

        $api = new IndicoApi();
        $api->setKey($key);
        $api->setUrl($url);

        $events = $api->getCategory(15);
        $this->assertInstanceOf(
            Event::class,
            $events[0]
        );

    }

}