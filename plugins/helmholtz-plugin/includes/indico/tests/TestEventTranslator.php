<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 29.05.18
 * Time: 16:17
 */

use PHPUnit\Framework\TestCase;

use Indico\IndicoEventTranslator;

const DATA = "{
			\"startDate\": {
				\"date\": \"2007-01-23\",
				\"tz\": \"Europe\/Berlin\",
				\"time\": \"09:00:00\"
			},
			\"endDate\": {
				\"date\": \"2007-01-23\",
				\"tz\": \"Europe\/Berlin\",
				\"time\": \"10:30:00\"
			},
			\"creator\": {
				\"_type\": \"Avatar\",
				\"emailHash\": \"565152bc6c27c9be450b7187f4937f94\",
				\"affiliation\": \"XFEL\",
				\"_fossil\": \"conferenceChairMetadata\",
				\"fullName\": \"HAAS, Tobias\",
				\"id\": \"231\"
			},
			\"hasAnyProtection\": false,
			\"roomFullname\": null,
			\"modificationDate\": {
				\"date\": \"2017-11-22\",
				\"tz\": \"UTC\",
				\"time\": \"05:20:50.407426\"
			},
			\"timezone\": \"Europe\/Berlin\",
			\"id\": \"259\",
			\"category\": \"FH\",
			\"title\": \"DESY EUDET JRA1 Meeting\",
			\"location\": \"DESY Hamburg\",
			\"_fossil\": \"conferenceMetadata\",
			\"type\": \"meeting\",
			\"categoryId\": \"15\",
			\"_type\": \"Conference\",
			\"description\": \"\",
			\"roomMapURL\": \"\",
			\"material\": [],
			\"visibility\": {
				\"id\": \"\",
				\"name\": \"Everywhere\"
			},
			\"address\": \"\",
			\"creationDate\": {
				\"date\": \"2007-01-22\",
				\"tz\": \"UTC\",
				\"time\": \"17:35:47.389436\"
			},
			\"room\": \"3a\",
			\"chairs\": [
				{
					\"_type\": \"ConferenceChair\",
					\"emailHash\": \"15a632a225d1f0dddc15df85512e4fc3\",
					\"affiliation\": \"DESY\",
					\"_fossil\": \"conferenceChairMetadata\",
					\"fullName\": \"Dr. Haas, Tobias\",
					\"id\": 0
				}
			],
			\"url\": \"https:\/\/indico.desy.de\/indico\/event\/259\/\"
		}";

final class TestEventTranslator extends TestCase
{

    public function testTranslation() {
        $data = json_decode(DATA, true);

        //var_dump($data);

        $translator = new IndicoEventTranslator();
        $translator->setSource($data);
        $translated = $translator->translate();
        $this->assertEquals(
            $translated['id'],
            $data['id']
        );
    }

}