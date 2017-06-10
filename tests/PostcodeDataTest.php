<?php
require_once('src/PostcodeData.php');
require_once 'vendor/autoload.php';

use johnmccuk\PostcodeData;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class PostcodeDataTest extends TestCase
{
    public function __construct()
    {
        $this->mockedPostcodeData = $this->getMockBuilder('johnmccuk\PostcodeData')
            ->setConstructorArgs([$this->path])
            ->setMethods(['retrieveFileData'])
            ->getMock();

        $this->mockedPostcodeData->method('retrieveFileData')
             ->willReturn(['M32 0JG', 'OX49 5NU', 'NE30 1DP']);
    }

    public function testPostcodeDataIstheRightClass()
    {
        $postcodeData = new PostcodeData('some/path');
        $this->assertEquals('johnmccuk\PostcodeData', get_class($postcodeData));
    }

    /**
     * @expectedException Exception
     */
    public function testRetrievePostcodesThrowsExceptionIfFileMissing()
    {
        $postcodeData = new PostcodeData('some/path');
        $result = $postcodeData->retrievePostcodes();
    }

    public function testPostcodeDataReturnsACollctionClass()
    {
        $result = $this->mockedPostcodeData->retrievePostcodes();
        $this->assertEquals('Doctrine\Common\Collections\ArrayCollection', get_class($result));
    }

    public function testPostcodeDataReturnsCorrectValues()
    {
        $result = $this->mockedPostcodeData->retrievePostcodes();
        $this->assertEquals('M320JG', $result->first());
        $this->assertEquals('NE301DP', $result->last());
    }

    public function testPostcodeDataApi()
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
        
        $result = $this->mockedPostcodeData->extendPostcodeData($mockedGuzzle, $this->mockedPostcodeData->retrievePostcodes());
        
        $this->assertEquals(3, $result->count());

        $first = $result->first();
        $this->assertEquals('M320JG', $first['postcode']);
        $this->assertEquals('-2.30283674284007', $first['longitude']);
        $this->assertEquals('53.4556572899372', $first['latitude']);

        $last = $result->last();
        $this->assertEquals('NE301DP', $last['postcode']);
        $this->assertEquals('-1.43926900515621', $last['longitude']);
        $this->assertEquals('55.0113051910514', $last['latitude']);
    }
}
