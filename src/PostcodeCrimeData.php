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
    protected $postcode;
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

        $this->postcode = $postcode;
        $this->client = $client;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getPostcode()
    {
        return $this->postcode['postcode'];
    }

    public function getLongitude()
    {
        return $this->postcode['longitude'];
    }

    public function getLatitude()
    {
        return $this->postcode['latitude'];
    }

    public function getFromDate()
    {
        return $this->fromDate;
    }

    public function getToDate()
    {
        return $this->toDate;
    }

    //protected function getPreviousYEar
}
