<?php
require_once('src/PostcodeFactory.php');
require_once 'vendor/autoload.php';

use johnmccuk\PostcodeFactory;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class PostcodeFactoryTest extends TestCase
{
    public function __construct()
    {
        $this->mockedPostcodeFactory = $this->getMockBuilder('johnmccuk\PostcodeFactory')
            ->setConstructorArgs([$this->path])
            ->setMethods(['retrieveFileData'])
            ->getMock();

        $this->mockedPostcodeFactory->method('retrieveFileData')
             ->willReturn(['M32 0JG', 'OX49 5NU', 'NE30 1DP']);
    }

    public function testPostcodeFactoryIstheRightClass()
    {
        $postcodeFactory = new PostcodeFactory('some/path');
        $this->assertEquals('johnmccuk\PostcodeFactory', get_class($postcodeFactory));
    }

    /**
     * @expectedException Exception
     */
    public function testRetrievePostcodesThrowsExceptionIfFileMissing()
    {
        $postcodeFactory = new PostcodeFactory('some/path');
        $result = $postcodeFactory->retrievePostcodes();
    }

    public function testPostcodeFactoryReturnsACollctionClass()
    {
        $result = $this->mockedPostcodeFactory->retrievePostcodes();
        $this->assertEquals('Doctrine\Common\Collections\ArrayCollection', get_class($result));
    }

    public function testPostcodeFactoryReturnsCorrectValues()
    {
        $result = $this->mockedPostcodeFactory->retrievePostcodes();
        $this->assertEquals('M320JG', $result->first());
        $this->assertEquals('NE301DP', $result->last());
    }

    public function testPostcodeFactoryApi()
    {
        $mockedGuzzle = $this->getMockBuilder('\GuzzleHttp\Client')
        ->setMethods(['post'])
        ->getMock();

        $mockedResponseText = '
        {
            "status": 200,
            "result": [
                {
                    "query": "M320JG",
                    "result": {
                        "postcode": "M320JG",
                        "longitude": -2.30283674284007,
                        "latitude": 53.4556572899372
                    }
                },
                {
                    "query": "OX495NU",
                    "result": {
                        "postcode": "OX495NU",
                        "longitude": -1.06977254466896,
                        "latitude": 51.6559271444373
                    }
                },
                {
                    "query": "NE301DP",
                    "result": {
                        "postcode": "NE301DP",
                        "longitude": -1.43926900515621,
                        "latitude": 55.0113051910514
                    }
                }
            ]
        }
        ';

        $mockedGuzzle->method('post')
             ->willReturn(new \GuzzleHttp\Psr7\Response(200, [], $mockedResponseText));

        $result = $this->mockedPostcodeFactory->generatePostcodeCrimeData($mockedGuzzle, $this->mockedPostcodeFactory->retrievePostcodes(), new DateTime('2016-01-01'), new DateTime('2016-03-31'));

        $this->assertEquals(3, $result->count());

        /*
        * @todo
        * Found a bug, the factory creates a johnmccuk\PostcodeCrimeData class which calls the live API
        * TODO mock the API call within the johnmccuk\PostcodeCrimeData objects
        */

        $first = $result->first();
        $this->assertEquals('johnmccuk\PostcodeCrimeData', get_class($first));
        $this->assertEquals('M320JG', $first->getPostcode());
        $this->assertEquals('-2.30283674284007', $first->getLongitude());
        $this->assertEquals('53.4556572899372', $first->getLatitude());

        $to = $first->getFromDate();
        $this->assertEquals('DateTime', get_class($to));
        $this->assertEquals('2016-01-01', $to->format('Y-m-d'));

        $last = $first->getToDate();
        $this->assertEquals('DateTime', get_class($last));
        $this->assertEquals('2016-03-31', $last->format('Y-m-d'));

        $last = $result->last();
        $this->assertEquals('johnmccuk\PostcodeCrimeData', get_class($last));
        $this->assertEquals('NE301DP', $last->getPostcode());
        $this->assertEquals('-1.43926900515621', $last->getLongitude());
        $this->assertEquals('55.0113051910514', $last->getLatitude());

        $to = $last->getFromDate();
        $this->assertEquals('DateTime', get_class($to));
        $this->assertEquals('2016-01-01', $to->format('Y-m-d'));

        $last = $last->getToDate();
        $this->assertEquals('DateTime', get_class($last));
        $this->assertEquals('2016-03-31', $last->format('Y-m-d'));
    }
}
