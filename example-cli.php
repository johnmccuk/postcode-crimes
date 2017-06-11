<?php
require_once 'src/PostcodeFactory.php';
require_once 'src/CrimeData.php';

use johnmccuk\PostcodeFactory;
use johnmccuk\CrimeData;
use GuzzleHttp\Client;

date_default_timezone_set('UTC');

$postcodeFactory = new PostcodeFactory(getcwd() . '/data/postcodes.txt');
$postcodes = $postcodeFactory->extendPostcodeFactory(new GuzzleHttp\Client(), $postcodeFactory->retrievePostcodes(), new DateTime('2016-01-01'), new DateTime('2016-12-31'));

$postcodeFactory->exportToCsvFile($postcodes, getcwd() . '/data/postcodes.csv');
