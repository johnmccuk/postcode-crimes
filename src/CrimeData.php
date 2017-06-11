<?php
namespace johnmccuk;

require_once 'vendor/autoload.php';

use \Exception;
use \Datetime;
use \DateInterval;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Retrieve crime data for specified postcodes
 *
 * @class CrimeData
 * @since 07/06/2017
 * @author John McCracken <johnmccuk@gmail.com>
 * @link https://github.com/johnmccuk/postcode-crimes
 */
class CrimeData
{
    protected $postcodes;
    
    /**
    * @method __construct
    * @param GuzzleHttp\Client $client
    */
    public function __construct()
    {

    }

    public function calculateCrimeData(\GuzzleHttp\Client $client, \Doctrine\Common\Collections\ArrayCollection $postcodes, \DateTime $date)
    {
        $data = [];

        foreach ($postcodes as $key => $postcode) {
            $data[$postcode['postcode']] = $this->getYearTotalByLocation($client, $postcode, $date);
        }
    }

    public function getYearTotalByLocation(\GuzzleHttp\Client $client, array $postcode, \DateTime $date)
    {
        $baseUrl = 'https://data.police.uk/api/crimes-street/all-crime?';

        $returnData = [];

        for ($i=0; $i < 12; $i++) {

            $requestParams = [
                'lat' => $postcode['latitude'],
                'lng' => $postcode['longitude'],
                'date' => $date->format('Y-m'),
            ];
            $returnData[$date->format('m-Y')] = $this->getMonthlyTotals($client, $requestParams);
   
            $date->add(new DateInterval('P1M'));
        }



        $url = $baseUrl. http_build_query($requestParams);

        $response = $client->get($url);

        if ($response->getStatusCode() != "200") {
            throw new Exception('Invalid API call '. $response->getBody()->getContents());
        }

        $responseValues = json_decode($response->getBody()->getContents(), true);



        var_dump($responseValues);exit;
        //?lat={{LATITUDE}}lng={{LATITUDE}}&date=2013-01'
    }

    public function getMonthlyTotals(\GuzzleHttp\Client $client, $data)
    {

    }
}
