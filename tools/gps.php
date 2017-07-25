<?php
define('NO_HTTP_AUTH',true);
include_once('/var/www/kuzmahome/config.php');

/*$_REQUEST = array(
	'latitude' => 55.7839283,
    'longitude' => 37.6275364,
    'accuracy' => 82.5,
    'altitude' => 0.0,
    'provider' => 'network',
    'bearing' => '0.0',
    'speed' => 0.0,
    'time' => '2017-06-16T09:00:45.40Z',
    'battlevel' => 93,
    'charging' => 0,
    'secret' => 'msergeev:hKjpTg3VCg',
    'deviceid' => '863657033386189'
);*/
\MSergeev\Packages\Kuzmahome\Lib\Gps::parseGpsData($_REQUEST);
