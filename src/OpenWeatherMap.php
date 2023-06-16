<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\Endpoint\Geocoding;

class OpenWeatherMap
{
    public function __construct(private Config $config) {}

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getGeocoding(): Geocoding
    {
        return new Geocoding($this);
    }
}