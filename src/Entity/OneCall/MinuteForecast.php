<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

class MinuteForecast
{
    private \DateTimeImmutable $dateTime;

    private float $precipitation;

    public function __construct(array $data)
    {
        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt'], new \DateTimeZone('UTC'));
        $this->precipitation = $data['precipitation'];
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getPrecipitation(): float
    {
        return $this->precipitation;
    }
}