<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Method;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Weather;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherCollection;
use ProgrammatorDev\OpenWeatherMap\Resource\Util\LanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Resource\Util\UnitSystemTrait;
use Psr\Http\Client\ClientExceptionInterface;

class WeatherResource extends Resource
{
    use LanguageTrait;
    use UnitSystemTrait;

    private const NUM_RESULTS = 40;

    /**
     * Get access to current weather data
     *
     * @throws ClientExceptionInterface
     */
    public function getCurrent(float $latitude, float $longitude): Weather
    {
        $data = $this->api->request(
            method: Method::GET,
            path: '/data/2.5/weather',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
            ]
        );

        return new Weather($data);
    }

    /**
     * Get access to 5-day weather forecast data with 3-hour steps
     *
     * @throws ClientExceptionInterface
     */
    public function getForecast(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): WeatherCollection
    {
        $data = $this->api->request(
            method: Method::GET,
            path: '/data/2.5/forecast',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'cnt' => $numResults
            ]
        );

        return new WeatherCollection($data);
    }
}