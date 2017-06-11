# postcode-crimes
Demo which accepts a txt file of postcodes and returns the most common crime and monthly average from the supplied date range year.

## Requirements
***PHP Curl***

See your specific operating system package manager

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

## Test

`vendor/bin/phpunit tests/`
