<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCallEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\WeatherEndpoint;

class OpenWeatherMap
{
    public function __construct(
        private readonly Config $config
    ) {}

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getOneCall(): OneCallEndpoint
    {
        return new OneCallEndpoint($this);
    }

    public function getWeather(): WeatherEndpoint
    {
        return new WeatherEndpoint($this);
    }

    public function getAirPollution(): AirPollutionEndpoint
    {
        return new AirPollutionEndpoint($this);
    }

    public function getGeocoding(): GeocodingEndpoint
    {
        return new GeocodingEndpoint($this);
    }
}