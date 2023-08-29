<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCallEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\WeatherEndpoint;

class OpenWeatherMap
{
    public OneCallEndpoint $oneCall;

    public WeatherEndpoint $weather;

    public AirPollutionEndpoint $airPollution;

    public GeocodingEndpoint $geocoding;

    public function __construct(public readonly Config $config)
    {
        $this->oneCall = new OneCallEndpoint($this);
        $this->weather = new WeatherEndpoint($this);
        $this->airPollution = new AirPollutionEndpoint($this);
        $this->geocoding = new GeocodingEndpoint($this);
    }
}