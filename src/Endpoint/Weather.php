<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithMeasurementSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\CurrentWeather;
use ProgrammatorDev\OpenWeatherMap\Exception\OutOfRangeCoordinateException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;

class Weather extends AbstractEndpoint
{
    use WithMeasurementSystemTrait;
    use WithLanguageTrait;
    use ValidateCoordinateTrait;

    private string $urlCurrentWeather = 'https://api.openweathermap.org/data/2.5/weather';

    private string $urlWeatherForecast = 'https://api.openweathermap.org/data/2.5/forecast';

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     */
    public function getCurrent(float $latitude, float $longitude): CurrentWeather
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlCurrentWeather,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->measurementSystem,
                'lang' => $this->language
            ]
        );

        return new CurrentWeather($data);
    }

    /**
     * @throws Exception
     * @throws OutOfRangeCoordinateException
     */
    public function getCurrentByLocationName(string $locationName): CurrentWeather
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
    public function getForecast(float $latitude, float $longitude)
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlWeatherForecast,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->measurementSystem,
                'lang' => $this->language
            ]
        );

        dd($data);
    }
}