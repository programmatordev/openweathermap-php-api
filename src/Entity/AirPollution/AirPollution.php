<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

class AirPollution
{
    private AirQuality $airQuality;

    private Component $components;

    public function __construct(array $data)
    {
        $this->airQuality = new AirQuality($data['main']);
        $this->components = new Component($data['components']);
    }

    public function getAirQuality(): AirQuality
    {
        return $this->airQuality;
    }

    public function getComponents(): Component
    {
        return $this->components;
    }
}