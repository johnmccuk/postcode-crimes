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
        $to = new DateTime('2015-03-31');

        $mockedGuzzle = $this->getMockBuilder('\GuzzleHttp\Client')
        ->setMethods(['get'])
        ->getMock();

        $mockedResponseText1 = '
            [
                {"category": "anti-social-behaviour"},
                {"category": "anti-social-behaviour"},
                {"category": "stealing-knickers-from-the washing-line"},
                {"category": "impersonating-a-police-dog"}
            ]
        ';

        $mockedResponseText2 = '
            [
                {"category": "anti-social-behaviour"},
                {"category": "stealing-knickers-from-the washing-line"},
                {"category": "impersonating-a-police-dog"}
            ]
        ';

        $mockedResponseText3 = '
            [
                {"category": "anti-social-behaviour"}
            ]
        ';

        $mockedGuzzle
        ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                 new \GuzzleHttp\Psr7\Response(200, [], $mockedResponseText1),
                 new \GuzzleHttp\Psr7\Response(200, [], $mockedResponseText2),
                 new \GuzzleHttp\Psr7\Response(200, [], $mockedResponseText3)
            );
        
            
        $this->PostcodeCrimeData = new PostcodeCrimeData($mockedGuzzle, $postcode, $from, $to);
    }

    public function testGetPostcodeReturnsCorrectCoreValues()
    {
        $this->assertEquals('M320JG', $this->PostcodeCrimeData->getPostcode());
        $this->assertEquals('53.4556572899372', $this->PostcodeCrimeData->getLatitude());
        $this->assertEquals('-2.30283674284007', $this->PostcodeCrimeData->getLongitude());
    }

    public function testCommonCrimeCorrect()
    {
        $this->assertEquals('anti-social-behaviour', $this->PostcodeCrimeData->mostCommonCrime);
    }

    public function testMostCommonCrimeAverage()
    {
        $this->assertEquals('1.3', $this->PostcodeCrimeData->mostCommonCrimeAverage);
    }
}
