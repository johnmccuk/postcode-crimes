<?php
namespace johnmccuk;

require_once 'vendor/autoload.php';

use \Exception;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Class for loading, validating and formatting postcodes from a file
 *
 * Note: validation not required due to specifications
 * @class PostcodeLoader
 * @since 07/06/2017
 * @author John McCracken <johnmccuk@gmail.com>
 * @link https://github.com/johnmccuk/postcode-crimes
 */
class PostcodeLoader
{
    protected $postcodes;

    /**
    * @method __construct
    * @param string $path
    */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
    * Retrieve and return a Collection of formatted postcodes
    * @method retrievePostcodes
    * @return Doctrine\Common\Collections\ArrayCollection
    */
    public function retrievePostcodes()
    {
        if (empty($this->postcode)) {
            foreach ($this->retrieveFileData() as $key => $postcode) {
                $this->postcodes[] = $this->formatPostcode($postcode);
            }
        }

        return new ArrayCollection($this->postcodes);
    }

    /**
    * Retrieve postcode data from a specified file
    * @method retrieveFileData
    * @return array
    */
    public function retrieveFileData()
    {
        if (file_exists($this->path) === false) {
            throw new Exception('File doesnt exist '. $path);
        }
        return file($this->path, FILE_IGNORE_NEW_LINES);
    }

    /**
    * Format the postcode correctly
    * @method formatPostcode
    * @param string $postcode
    * @return string
    */
    public function formatPostcode($postcode)
    {
        return trim(strtoupper(str_replace(' ', '', $postcode)));
    }

    /**
    * Retrieve and return a Collection of formatted postcodes
    * @method extendPostcodeData
    * @param GuzzleHttp\Client $client
    * @param Doctrine\Common\Collections\ArrayCollection $postcode
    * @return Doctrine\Common\Collections\ArrayCollection
    */
    public function extendPostcodeData(\GuzzleHttp\Client $client, \Doctrine\Common\Collections\ArrayCollection $postcodes)
    {
        $response = $client->post(
            'api.postcodes.io/postcodes',
            ['json' => ["postcodes" => $postcodes->toArray()]]
        );

        if ($response->getStatusCode() != "200") {
            throw new Exception('Invalid API call '. $response->getBody()->getContents());
        }

        $responseValues = json_decode($response->getBody()->getContents(), true);
        $data = [];
        foreach ($responseValues['result'] as $key => $responseValue) {
            $data[] = $responseValue['result'];
        }

        return new ArrayCollection($data);
    }
}
