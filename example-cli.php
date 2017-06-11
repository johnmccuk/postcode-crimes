<?php
require_once 'src/PostcodeFactory.php';
require_once 'src/CrimeData.php';

use johnmccuk\PostcodeFactory;
use johnmccuk\CrimeData;
use GuzzleHttp\Client;

date_default_timezone_set('UTC');

$postcodeFactory = new PostcodeFactory(getcwd() . '/data/postcodes.txt');
$postcodes = $postcodeFactory->extendPostcodeFactory(new GuzzleHttp\Client(), $postcodeFactory->retrievePostcodes());

$postcodeFactory->exportToCsvFile($postcodes, getcwd() . '/data/postcodes.csv');