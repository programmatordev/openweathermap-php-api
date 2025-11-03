<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

class HourData extends BaseWeather
{
    private float $temperature;

    private float $temperatureFeelsLike;

    private int $visibility;

    private int $precipitationProbability;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->temperature = $data['temp'];
        $this->temperatureFeelsLike = $data['feels_like'];
        $this->visibility = $data['visibility'];
        $this->precipitationProbability = round($data['pop'] * 100);
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getTemperatureFeelsLike(): float
    {
        return $this->temperatureFeelsLike;
    }

    public function getVisibility(): int
    {
        return $this->visibility;
    }

    public function getPrecipitationProbability(): int
    {
        return $this->precipitationProbability;
    }
}