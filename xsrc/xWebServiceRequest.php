<?php
namespace johnmccuk;

require 'vendor/autoload.php';

use \Exception;
use GuzzleHttp\Client;

/**
 * Class for calling and validating a web request
 *
 * @class WebServiceRequest
 * @since 07/06/2017
 * @author John McCracken <johnmccuk@gmail.com>
 * @link https://github.com/johnmccuk/postcode-crimes
 */
class WebServiceRequest
{
    protected $validTypes = ['GET', 'POST', 'HEAD', 'PUT', 'DELETE'];
    
    /**
    * @method __construct
    * @param GuzzleHttp\Client $client
    */
    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    /**
    * Return the response TODO
    * @method getResponse
    * @param string $type 'GET' or 'POST'
    * @param string $url
    * @throws Exception on invalid type or url
    */
    public function getResponse($type = 'GET', $url = '')
    {
        if ($this->checkType($type) == false) {
            throw new Exception('Invalid Type - must be one of ' . implode(',', $this->validTypes));
        }

        if (empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new Exception('Invalid URL');
        }
        
        return true;
    }

    /**
    * Check the response Type is valid
    * Valid types are
    * Aware Guzzle takes other types, but for this example, these will do
    * @method checkType
    * @param string $type
    * @return boolean
    */
    protected function checkType($type)
    {
        return (in_array(strtoupper($type), $this->validTypes))  ? true : false;
    }
}
