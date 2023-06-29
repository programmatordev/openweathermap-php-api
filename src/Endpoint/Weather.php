<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithMeasurementSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\CurrentWeather;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherList;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidNumResultsException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidCoordinateException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateNumResultsTrait;

class Weather extends AbstractEndpoint
{
    use WithMeasurementSystemTrait;
    use WithLanguageTrait;
    use ValidateCoordinateTrait;
    use ValidateNumResultsTrait;

    private string $urlWeather = 'https://api.openweathermap.org/data/2.5/weather';

    private string $urlWeatherForecast = 'https://api.openweathermap.org/data/2.5/forecast';

    /**
     * @throws Exception
     * @throws InvalidCoordinateException
     */
    public function getCurrent(float $latitude, float $longitude): CurrentWeather
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlWeather,
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
     * @throws InvalidCoordinateException
     */
    public function getCurrentByLocationName(string $locationName): CurrentWeather
    {
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getCurrent(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude()
        );
    }

    /**
     * @throws Exception
     * @throws InvalidCoordinateException
     * @throws InvalidNumResultsException
     */
    public function getForecast(float $latitude, float $longitude, ?int $numResults = null): WeatherList
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateNumResults($numResults);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlWeatherForecast,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'cnt' => $numResults,
                'units' => $this->measurementSystem,
                'lang' => $this->language
            ]
        );

        return new WeatherList($data);
    }

    /**
     * @throws Exception
     * @throws InvalidCoordinateException
     * @throws InvalidNumResultsException
     */
    public function getForecastByLocationName(string $locationName, ?int $numResults = null): WeatherList
    {
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getForecast(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude(),
            $numResults
        );
    }
}