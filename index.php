<?php

use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

require_once 'vendor/autoload.php';

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => '4c8a0178591d875f493c3058921ecd9e'
    ])
);

dd($openWeatherMap->getGeocoding()->getCoordinatesByLocationName('lisbon'));
//dd($openWeatherMap->getGeocoding()->getCoordinatesByZipCode('1750-312', 'pt'));
//dd($openWeatherMap->getGeocoding()->getLocationNameByCoordinates(38.7077507, -9.1365919));