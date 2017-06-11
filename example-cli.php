<?php
require_once 'src/PostcodeFactory.php';
require_once 'src/CrimeData.php';

use johnmccuk\PostcodeFactory;
use johnmccuk\CrimeData;
use GuzzleHttp\Client;

date_default_timezone_set('UTC');

$postcodeFactory = new PostcodeFactory(getcwd() . '/data/postcodes.txt');
$extendedData = $postcodeFactory->extendPostcodeFactory(new GuzzleHttp\Client(), $postcodeFactory->retrievePostcodes());
//$crimeData = new CrimeData();
//$crimeData->calculateCrimeData(new GuzzleHttp\Client(), $extendedData, new DateTime('2016-01-01'));
//var_dump($extendedData->toArray());
$first = $extendedData->first();
var_dump($first->getPostcode());
var_dump($first->getCrimeCounts());
var_dump($first->mostCommonCrime);
/*
//$data = $postcodeList->retrievePostcodeList();
$data = $postcodeList->retrieveExtendedPostcodeList(new GuzzleHttp\Client());
//$webServiceRequest = new WebServiceRequest(new GuzzleHttp\Client());
//$result = $webServiceRequest->getResponse('get', '');
var_dump($data->last());

//load postcode list

//get postcode coor``dinates

//call police crime API

//output csv file
*/