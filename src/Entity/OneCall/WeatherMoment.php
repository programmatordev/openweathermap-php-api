<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;

class WeatherMoment extends Weather
{
    private Coordinate $coordinate;

    private Timezone $timezone;

    public function __construct(array $data)
    {
        parent::__construct($data['data'][0]);

        $this->coordinate = new Coordinate($data);
        $this->timezone = new Timezone($data);
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getTimezone(): Timezone
    {
        return $this->timezone;
    }
}