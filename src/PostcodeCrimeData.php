<?php
namespace johnmccuk;

require 'vendor/autoload.php';

use \Exception;
use \Datetime;

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

    //protected function getPreviousYEar
}
