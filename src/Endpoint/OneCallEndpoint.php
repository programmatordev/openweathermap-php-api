<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithMeasurementSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneCall;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidCoordinateException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidNumResultsException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;

class OneCallEndpoint extends AbstractEndpoint
{
    use WithMeasurementSystemTrait;
    use WithLanguageTrait;
    use ValidateCoordinateTrait;

    private string $urlOneCall = 'https://api.openweathermap.org/data/3.0/onecall';

    /**
     * @throws Exception
     * @throws InvalidCoordinateException
     */
    public function getWeather(float $latitude, float $longitude): OneCall
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCall,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->getMeasurementSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new OneCall($data);
    }

    /**
     * @throws Exception
     * @throws InvalidCoordinateException
     * @throws InvalidNumResultsException
     */
    public function getWeatherByLocationName(string $locationName): OneCall
    {
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getWeather(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude()
        );
    }
}