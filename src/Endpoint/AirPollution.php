<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\CurrentAirPollution;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidDateRangeException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidPastDateException;
use ProgrammatorDev\OpenWeatherMap\Exception\OutOfRangeCoordinateException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateDateRangeTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidatePastDateTrait;

class AirPollution extends AbstractEndpoint
{
    use ValidateCoordinateTrait;
    use ValidateDateRangeTrait;
    use ValidatePastDateTrait;

    private string $urlCurrentAirPollution = 'https://api.openweathermap.org/data/2.5/air_pollution';

    private string $urlAirPollutionForecast = 'https://api.openweathermap.org/data/2.5/air_pollution/forecast';

    private string $urlAirPollutionHistory = 'https://api.openweathermap.org/data/2.5/air_pollution/history';

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     */
    public function getCurrent(float $latitude, float $longitude): CurrentAirPollution
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlCurrentAirPollution,
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new CurrentAirPollution($data);
    }

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     */
    public function getCurrentByLocationName(string $locationName): CurrentAirPollution
    {
        // Get first result (most relevant)
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getCurrent(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude()
        );
    }

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     */
    public function getForecast(float $latitude, float $longitude): AirPollutionList
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollutionForecast,
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new AirPollutionList($data);
    }

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     */
    public function getForecastByLocationName(string $locationName): AirPollutionList
    {
        // Get first result (most relevant)
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getForecast(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude()
        );
    }

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     * @throws InvalidDateRangeException
     * @throws InvalidPastDateException
     */
    public function getHistory(
        float $latitude,
        float $longitude,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate
    ): AirPollutionList
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validatePastDate('startDate', $startDate);
        $this->validatePastDate('endDate', $endDate);
        $this->validateDateRange($startDate, $endDate);

        $utcTimezone = new \DateTimeZone('UTC');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollutionHistory,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startDate->setTimezone($utcTimezone)->getTimestamp(),
                'end' => $endDate->setTimezone($utcTimezone)->getTimestamp()
            ]
        );

        return new AirPollutionList($data);
    }

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     * @throws InvalidDateRangeException
     * @throws InvalidPastDateException
     */
    public function getHistoryByLocationName(
        string $locationName,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate
    ): AirPollutionList
    {
        // Get first result (most relevant)
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getHistory(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude(),
            $startDate,
            $endDate
        );
    }
}