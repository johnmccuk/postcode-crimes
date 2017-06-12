<?php
namespace johnmccuk;

require_once 'vendor/autoload.php';
require_once 'src/PostcodeCrimeData.php';

use \Exception;
use \Datetime;
use \Doctrine\Common\Collections\ArrayCollection;
use johnmccuk\PostcodeCrimeData;

/**
 * Class for creating, manipulating a Collection of Postcode Crime data
 *
 * Accepts a path to a file containing postcodes (one per line).
 *
 * Note: validation not required due to specifications.
 *
 * @class PostcodeFactory
 * @since 07/06/2017
 * @author John McCracken <johnmccuk@gmail.com>
 * @link https://github.com/johnmccuk/postcode-crimes
 */
class PostcodeFactory
{
    /*
    * @var array $postcodes
    * @var string $path
    */
    protected $postcodes;
    protected $path;

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
    * @throws Exception on file not existing
    */
    public function retrieveFileData()
    {
        if (file_exists($this->path) === false) {
            throw new Exception('File doesnt exist '. $path);
        }
        return file($this->path, FILE_IGNORE_NEW_LINES);
    }

    /**
    * Format the postcode to a uniform format
    *
    * Removes the spaces and makes uppercase
    * @method formatPostcode
    * @param string $postcode
    * @return string
    */
    public function formatPostcode($postcode)
    {
        return trim(strtoupper(str_replace(' ', '', $postcode)));
    }

    /**
    * Retrieve and return a Collection of PostcodeCrimeData objects
    * @method generatePostcodeCrimeData
    * @param GuzzleHttp\Client $client
    * @param Doctrine\Common\Collections\ArrayCollection $postcode
    * @param DateTime $fromDate
    * @param DateTime $toDate
    * @return Doctrine\Common\Collections\ArrayCollection
    */
    public function generatePostcodeCrimeData(\GuzzleHttp\Client $client, \Doctrine\Common\Collections\ArrayCollection $postcodes, DateTime $fromDate, DateTime $toDate)
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
            try {
                $data[] = new PostcodeCrimeData($client, $responseValue['result'], $fromDate, $toDate);
            } catch (Exception $e) {
                continue;
            }
        }

        return new ArrayCollection($data);
    }

    /**
    * Exports Postcode Statistics to a specified filepath
    * @method exportToCsvFile
    * @param Doctrine\Common\Collections\ArrayCollection $postcodes
    * @param string $filepath
    * @return boolean
    */
    public function exportToCsvFile(\Doctrine\Common\Collections\ArrayCollection $postcodes, $filepath)
    {
        try {
            $values = [['postcode', 'category', 'average']];

            foreach ($postcodes as $key => $postcode) {
                if (get_class($postcode) != 'PostcodeCrimeData') {
                    continue;
                }
                $values[] = [
                    $postcode->getPostcode(),
                    $postcode->getMostCommonCrime(),
                    $postcode->getMostCommonCrimeAverage()
                ];
            }

            $fp = fopen($filepath, 'w');

            foreach ($values as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
        } catch (Exception $e) {
            if (isset($fp)) {
                fclose($fp);
            }
            return false;
        }
        return true;
    }
}
