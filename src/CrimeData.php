<?php
namespace johnmccuk;

require_once 'vendor/autoload.php';

use \Exception;
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
    protected $client;
    
    /**
    * @method __construct
    * @param GuzzleHttp\Client $client
    */
    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }
}
