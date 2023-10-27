<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\LanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\UnitSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherAggregate;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneCall;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiErrorException;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

class OneCallEndpoint extends AbstractEndpoint
{
    use UnitSystemTrait;
    use LanguageTrait;

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getWeather(float $latitude, float $longitude): OneCall
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/3.0/onecall',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new OneCall($data);
    }

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getHistoryMoment(float $latitude, float $longitude, \DateTimeInterface $dateTime): WeatherLocation
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::lessThan(new \DateTime('now'))->assert($dateTime, 'dateTime');

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/3.0/onecall/timemachine',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'dt' => $dateTime->setTimezone(new \DateTimeZone('UTC'))->getTimestamp(),
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
    public function getHistoryAggregate(float $latitude, float $longitude, \DateTimeInterface $date): WeatherAggregate
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::lessThan(new \DateTime('now'))->assert($date, 'date');

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/3.0/onecall/day_summary',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'date' => $date->format('Y-m-d'),
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new WeatherAggregate($data);
    }
}