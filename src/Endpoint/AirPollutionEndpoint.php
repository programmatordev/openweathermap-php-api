<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocationList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocation;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

class AirPollutionEndpoint extends AbstractEndpoint
{
    private string $urlAirPollution = 'https://api.openweathermap.org/data/2.5/air_pollution';

    private string $urlAirPollutionForecast = 'https://api.openweathermap.org/data/2.5/air_pollution/forecast';

    private string $urlAirPollutionHistory = 'https://api.openweathermap.org/data/2.5/air_pollution/history';

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws ValidationException
     */
    public function getCurrent(float $latitude, float $longitude): AirPollutionLocation
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollution,
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new AirPollutionLocation($data);
    }

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws ValidationException
     */
    public function getForecast(float $latitude, float $longitude): AirPollutionLocationList
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollutionForecast,
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new AirPollutionLocationList($data);
    }

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws ValidationException
     */
    public function getHistory(
        float $latitude,
        float $longitude,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): AirPollutionLocationList
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::lessThan(new \DateTime('now'))->assert($endDate, 'endDate');
        Validator::lessThan($endDate)->assert($startDate, 'startDate');

        $timezoneUtc = new \DateTimeZone('UTC');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollutionHistory,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startDate->setTimezone($timezoneUtc)->getTimestamp(),
                'end' => $endDate->setTimezone($timezoneUtc)->getTimestamp()
            ]
        );

        return new AirPollutionLocationList($data);
    }
}