<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCallEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\WeatherEndpoint;

class OpenWeatherMap
{
    public function __construct(private readonly Config $config) {}

    public function config(): Config
    {
        return $this->config;
    }

    public function oneCall(): OneCallEndpoint
    {
        return new OneCallEndpoint($this);
    }

    public function weather(): WeatherEndpoint
    {
        return new WeatherEndpoint($this);
    }

    public function airPollution(): AirPollutionEndpoint
    {
        return new AirPollutionEndpoint($this);
    }

    public function geocoding(): GeocodingEndpoint
    {
        return new GeocodingEndpoint($this);
    }
}