<?php
require_once('src/PostcodeCrimeData.php');
require_once 'vendor/autoload.php';

use johnmccuk\PostcodeCrimeData;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class PostcodeCrimeDataTest extends TestCase
{
    public function __construct()
    {
        $postcode = [
            'postcode' => 'M320JG',
            'longitude' => '-2.30283674284007',
            'latitude' => '53.4556572899372'
        ];

        $from = new DateTime('2015-01-01');
        $to = new DateTime('2015-12-31');

        $mockedGuzzle = $this->getMockBuilder('\GuzzleHttp\Client')
        ->setMethods(['get'])
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
        

        $this->mockedPostcodeCrimeData = $this->getMockBuilder('johnmccuk\PostcodeCrimeData')
            ->setConstructorArgs([$client, $postcode, $from, $to])
   //         ->setMethods(['retrieveFileData'])
            ->getMock();

    //    $this->mockedPostcodeFactory->method('retrieveFileData')
    //         ->willReturn(['M32 0JG', 'OX49 5NU', 'NE30 1DP']);
    }
}
