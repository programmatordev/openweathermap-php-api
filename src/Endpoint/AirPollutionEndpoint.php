<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\ValidationTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocationList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocation;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiErrorException;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

class AirPollutionEndpoint extends AbstractEndpoint
{
    use ValidationTrait;

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getCurrent(float $latitude, float $longitude): AirPollutionLocation
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/2.5/air_pollution',
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new AirPollutionLocation($data);
    }

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getForecast(float $latitude, float $longitude): AirPollutionLocationList
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/2.5/air_pollution/forecast',
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new AirPollutionLocationList($data);
    }

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getHistory(
        float $latitude,
        float $longitude,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): AirPollutionLocationList
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateDateRange($startDate, $endDate);

        $timezoneUtc = new \DateTimeZone('UTC');

        $data = $this->sendRequest(
            method: 'GET',
            path: '/data/2.5/air_pollution/history',
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