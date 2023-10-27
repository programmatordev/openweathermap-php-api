<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\LanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\UnitSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherLocationList;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiErrorException;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

class WeatherEndpoint extends AbstractEndpoint
{
    use UnitSystemTrait;
    use LanguageTrait;

    private const NUM_RESULTS = 40;

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getCurrent(float $latitude, float $longitude): WeatherLocation
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/2.5/weather',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new WeatherLocation($data);
    }

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getForecast(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): WeatherLocationList
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::greaterThan(0)->assert($numResults, 'numResults');

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/2.5/forecast',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'cnt' => $numResults,
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new WeatherLocationList($data);
    }
}