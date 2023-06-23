<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\CurrentAirPollution;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;

class AirPollution extends AbstractEndpoint
{
    use ValidateCoordinateTrait;

    private string $urlCurrentAirPollution = 'https://api.openweathermap.org/data/2.5/air_pollution';

    private string $urlAirPollutionForecast = 'https://api.openweathermap.org/data/2.5/air_pollution/forecast';

    private string $urlAirPollutionHistory = 'https://api.openweathermap.org/data/2.5/air_pollution/history';

    /**
     * @throws Exception
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
     */
    public function getForecast(float $latitude, float $longitude)
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

        dd($data);
    }

    /**
     * @throws Exception
     */
    public function getHistory(float $latitude, float $longitude, \DateTime $startDate, \DateTime $endDate)
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollutionHistory,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startDate->setTimezone(new \DateTimeZone('UTC'))->getTimestamp(),
                'end' => $endDate->setTimezone(new \DateTimeZone('UTC'))->getTimestamp()
            ]
        );

        dd($data);
    }
}