<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Geocoding;
use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCall;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Weather;

class OpenWeatherMap
{
    public function __construct(
        private readonly Config $config
    ) {}

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getOneCall(): OneCall
    {
        return new OneCall($this);
    }

    public function getWeather(): Weather
    {
        return new Weather($this);
    }

    public function getAirPollution(): AirPollution
    {
        return new AirPollution($this);
    }

    public function getGeocoding(): Geocoding
    {
        return new Geocoding($this);
    }
}