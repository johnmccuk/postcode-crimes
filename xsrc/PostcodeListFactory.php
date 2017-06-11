<?php
namespace johnmccuk;

require_once 'vendor/autoload.php';
require_once 'src/PostcodeListFactory.php';

use \Exception;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Class for retrieving and holding Postcode lists
 *
 * @class PostcodeListFactory
 * @since 07/06/2017
 * @author John McCracken <johnmccuk@gmail.com>
 * @link https://github.com/johnmccuk/postcode-crimes
 */
class PostcodeListFactory
{
    protected $path;
    protected $extendedPostcodes = [];

    /**
    * @method __construct
    * @param string $path
    */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
    * Retrieve and load the Postcode list
    * @method retrieveFormatedPostcodeList
    * @param GuzzleHttp\Client $client
    * @return Doctrine\Common\Collections\ArrayCollection
    */
    public function retrieveExtendedPostcodeList(\GuzzleHttp\Client $client)
    {
        if (empty($this->extendedPostcodes)) {
            foreach ($this->retrieveFileData($this->path) as $key => $postcode) {
                $postcodes[] = $this->formatPostcode($postcode);
            }
            $this->extendedPostcodes = $this->retrieveExtendedPostcodeData($client, $postcodes);
        }
        return new ArrayCollection($this->extendedPostcodes);
    }

    /**
    * TODO
    * @method retrieveFileData
    * @param  string $path
    * @return Doctrine\Common\Collections\ArrayCollection
    */
    public function retrieveFileData($path)
    {
        if (file_exists($path) === false) {
            throw new Exception('File doesnt exist '. $path);
        }
        return file($path, FILE_IGNORE_NEW_LINES);
    }

    /**
    * TODO
    * @method retrieveExtendedPostcodeData
    * @param  GuzzleHttp\Client $client
    * @return array
    */
    public function retrieveExtendedPostcodeData(\GuzzleHttp\Client $client, $postcodes)
    {
        $response = $client->post(
            'api.postcodes.io/postcodes',
            ['json' => ["postcodes" => $postcodes]]
        );

        $responseValues = json_decode($response->getBody()->getContents(), true);

        foreach ($responseValues['result'] as $key => $responseValue) {
            $data[] = $responseValue['result'];
        }

        return $data;
    }

    /**
    * Format the postcode correctly
    * @method formatPostcode
    * @param string $path filepath to postcode file
    * @throws Exception on invalid type or url
    */
    protected function formatPostcode($postcode)
    {
        return trim(strtoupper(str_replace(' ', '', $postcode)));
    }
}
