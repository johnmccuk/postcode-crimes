# postcode-crimes
Demo which accepts a txt file of postcodes and returns the most common crime and monthly average from the supplied date range year.

## Requirements
***PHP Curl***

See your specific operating system package manager

Has been tested on php 5.6

***Composer*** 

`curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer`


## Install

```
git clone https://github.com/johnmccuk/postcode-crimes.git
cd postcode-crimes
composer install
```

***Note:*** If you dont want the development libraries use `composer install --no-dev`

## Run

From the command line

`php -f example-cli.php`

## Example
```
require_once 'src/PostcodeFactory.php';
require_once 'src/CrimeData.php';

use johnmccuk\PostcodeFactory;
use johnmccuk\CrimeData;
use GuzzleHttp\Client;

date_default_timezone_set('UTC');

$postcodeFactory = new PostcodeFactory(getcwd() . '/data/postcodes.txt');
$postcodes = $postcodeFactory->generatePostcodeCrimeData(new GuzzleHttp\Client(), $postcodeFactory->retrievePostcodes(), new DateTime('2016-01-01'), new DateTime('2016-12-31'));

$postcodeFactory->exportToCsvFile($postcodes, getcwd() . '/data/postcodes.csv');
```

## Test

`vendor/bin/phpunit tests/`
