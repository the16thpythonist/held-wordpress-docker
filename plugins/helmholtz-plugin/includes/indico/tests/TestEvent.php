<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 29.05.18
 * Time: 15:41
 */

use PHPUnit\Framework\TestCase;
use Indico\Event;
use Indico\Creator;
use Indico\Time;

final class TestEvent extends TestCase
{
    public function testCreatorInstance()
    {
        $params = array(
            'full_name' => 'TEUFEL, jonas',
            'id' => '16',
            'affiliation' => 'KIT'
        );
        // Create a new Creator object
        $creator = new Creator($params);
        $this->assertInstanceOf(
            Creator::class,
            $creator
        );
        $this->assertEquals(
            $creator->getId(),
            $params['id']
        );
        $this->assertEquals(
            $creator->getAffiliation(),
            $params['affiliation']
        );
        $this->assertEquals(
            $creator->getFullName(),
            $params['full_name']
        );
    }

    public function testTimeInstance() {
        $params = array(
            'date' => '2007-01-23',
            'time' => '09:00:00',
            'timezone' => 'Europe/Berlin'
        );
        $time = new Time($params);
        $this->assertEquals(
            $time->getTime(),
            $params['time']
        );
        $this->assertEquals(
            $time->getDate(),
            $params['date']
        );
        $this->assertEquals(
            $time->getTimezone(),
            $params['timezone']
        );
    }

    public function testEventInstance() {
        $params = array(
            'id' => '1',
            'type' => 'meeting',
            'title' => 'A generic meeting',
            'description' => 'nothing to see here',
            'location' => 'KIT',
            'address' => 'Karlsruhe',
            'url' => 'http://google.com',
            'start_date' => array(
                'date' => '2007-01-23',
                'time' => '09:00:00',
                'timezone' => 'Europe/Berlin'
            ),
            'end_date' => array(
                'date' => '2007-01-23',
                'time' => '09:00:00',
                'timezone' => 'Europe/Berlin'
            ),
            'modification_date' => array(
                'date' => '2007-01-23',
                'time' => '09:00:00',
                'timezone' => 'Europe/Berlin'
            ),
            'creator' => array(
                'full_name' => 'TEUFEL, jonas',
                'id' => '16',
                'affiliation' => 'KIT'
            )
        );

        $event = new Event($params);

        $this->assertEquals(
            $event->getTitle(),
            $params['title']
        );
        $this->assertEquals(
            $event->getUrl(),
            $params['url']
        );
        $this->assertInstanceOf(
            Creator::class,
            $event->getCreator()
        );
        $this->assertInstanceOf(
            Time::class,
            $event->getEndTime()
        );
    }

}