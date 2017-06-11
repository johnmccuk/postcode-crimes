<?php
namespace johnmccuk;

require 'vendor/autoload.php';

use \Exception;
use \Datetime;
use \DateInterval;
use \DatePeriod;

/**
 * Class for holding a single postcodes related crime data data
 *
 * @class PostcodeCrimeData
 * @since 07/06/2017
 * @author John McCracken <johnmccuk@gmail.com>
 * @link https://github.com/johnmccuk/postcode-crimes
 */
class PostcodeCrimeData
{
    protected $postcodeData;
    protected $client;
    protected $crimeData;
    protected $commonCrimeMonthlyTotals = [];
    protected $crimeCounts = [];
    protected $mostCommonCrime;
    protected $mostCommonCrimeAverage;
    protected $fromDate;
    protected $toDate;

    /**
    * @method __construct
    * @param GuzzleHttp\Client $client
    * @param array $postcode
    * @param DateTime $fromDate
    * @param DateTime $toDate
    */
    public function __construct(\GuzzleHttp\Client $client, array $postcode, DateTime $fromDate, DateTime $toDate)
    {
        if (array_key_exists('postcode', $postcode) === false) {
            throw new Exception('invalid postcode');
        }
        if (array_key_exists('longitude', $postcode) === false || array_key_exists('latitude', $postcode) == false) {
            throw new Exception('invalid coordinates');
        }

        $this->postcodeData = $postcode;
        $this->client = $client;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;

        $this->retrieveCrimeData();
        $this->calculateCrimeStatistics();
    }

    /**
    * Getter for the objects postcode
    * @method getPostcode
    * @return string
    */
    public function getPostcode()
    {
        return $this->postcodeData['postcode'];
    }

    /**
    * Getter for the objects Longitude
    * @method getLongitude
    * @return string
    */
    public function getLongitude()
    {
        return $this->postcodeData['longitude'];
    }

    /**
    * Getter for the objects Latitude
    * @method getLatitude
    * @return string
    */
    public function getLatitude()
    {
        return $this->postcodeData['latitude'];
    }

    /**
    * Getter for the objects 'from' date range
    * @method getFromDate
    * @return DateTime
    */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
    * Getter for the objects 'to' date range
    * @method getToDate
    * @return DateTime
    */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
    * Getter for the objects 'crimeData' values
    * @method getCrimeData
    * @return array
    */
    public function getCrimeData()
    {
        return $this->crimeData;
    }

    /**
    * Getter for the objects 'mostCommonCrime' value
    * @method getMostCommonCrime
    * @return string
    */
    public function getMostCommonCrime()
    {
        return $this->mostCommonCrime;
    }

    /**
    * Getter for the objects 'mostCommonCrimeAverage' value
    * @method getMostCommonCrimeAverage
    * @return float
    */
    public function getMostCommonCrimeAverage()
    {
        return $this->mostCommonCrimeAverage;
    }

    /**
    * Iterate and calculate the crime data for each month
    * @method retrieveCrimeData
    * @return null
    */
    protected function retrieveCrimeData()
    {
        $startDate = clone $this->fromDate;
        $interval = new DateInterval('P1M');
        $daterange = new DatePeriod($startDate, $interval, $this->toDate);

        foreach ($daterange as $date) {
            try {
                $this->crimeData[] = $this->getMonthlyCrimes($date);
            } catch (Exception $e) {
                continue;
            }
        }
    }

    /**
    * Iterate and calculate the crime data for each month
    * @method getMonthlyCrimes
    * @param DateTime $date
    * @return array
    */
    protected function getMonthlyCrimes(DateTime $date)
    {
        $baseUrl = 'https://data.police.uk/api/crimes-street/all-crime?';

        $requestParams = [
            'lat' => $this->postcodeData['latitude'],
            'lng' => $this->postcodeData['longitude'],
            'date' => $date->format('Y-m'),
        ];

        $url = $baseUrl. http_build_query($requestParams);

        $response = $this->client->get($url);

        if ($response->getStatusCode() != "200") {
            throw new Exception('Invalid API call '. $response->getStatusCode());
        }
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
    * Calculate the relevant Crime statistics
    * @method calculateCrimeStatistics
    * @return null
    */
    protected function calculateCrimeStatistics()
    {
        foreach ($this->crimeData as $key => $monthlyData) {
            $this->countMonthlyCrimes($monthlyData);
        }

        arsort($this->crimeCounts);
        reset($this->crimeCounts);
        $this->mostCommonCrime = key($this->crimeCounts);

        $monthlyCounts = [];
        foreach ($this->crimeData as $key => $monthlyData) {
            $monthlyCounts[] = $this->countMonthlyCommonCrimesCount($monthlyData, $this->mostCommonCrime);
        }

        $this->mostCommonCrimeAverage = round(array_sum($monthlyCounts) / count($monthlyCounts), 1);
    }

    /**
    * from the passed array, calculate how many times each crime occurrs
    * @method countMonthlyCrimes
    * @param array $monthlyCrimes
    * @return null
    */
    protected function countMonthlyCrimes(array $monthlyCrimes)
    {
        foreach ($monthlyCrimes as $key => $crime) {
            if (array_key_exists($crime['category'], $this->crimeCounts) == false) {
                $this->crimeCounts[$crime['category']] = 1;
                continue;
            }

            $this->crimeCounts[$crime['category']]++;
        }
    }

    /**
    * Iterate and calculate the crime data for each month
    * @method countMonthlyCommonCrimesCount
    * @param array $monthlyCrimes
    * @param string $commonCrime crime category
    * @return integer
    */
    protected function countMonthlyCommonCrimesCount(array $monthlyCrimes, $commonCrime)
    {
        $count = 0;
        foreach ($monthlyCrimes as $key => $crime) {
            if ($crime['category'] == $commonCrime) {
                $count++;
            }
        }
        return $count;
    }
}
