<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

class AirPollution
{
    private \DateTimeImmutable $dateTime;

    private AirQuality $airQuality;

    private Component $components;

    public function __construct(array $data)
    {
        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt'], new \DateTimeZone('UTC'));
        $this->airQuality = new AirQuality($data['main']);
        $this->components = new Component($data['components']);
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
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