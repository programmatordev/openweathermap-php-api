<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Assistant;

use ProgrammatorDev\OpenWeatherMap\Entity\BaseWeather;

class WeatherData extends BaseWeather
{
    private string $locationName;

    private float $temperature;

    private float $temperatureFeelsLike;

    private int $visibility;

    private \DateTimeImmutable $sunriseAt;

    private \DateTimeImmutable $sunsetAt;

    public function __construct(string $locationName, array $data)
    {
        parent::__construct($data);

        $this->locationName = $locationName;
        $this->temperature = $data['temp'];
        $this->temperatureFeelsLike = $data['feels_like'];
        $this->visibility = $data['visibility'];
        $this->sunriseAt = \DateTimeImmutable::createFromFormat('U', $data['sunrise']);
        $this->sunsetAt = \DateTimeImmutable::createFromFormat('U', $data['sunset']);
    }

    public function getLocationName(): string
    {
        return $this->locationName;
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

    public function getSunriseAt(): \DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    public function getSunsetAt(): \DateTimeImmutable
    {
        return $this->sunsetAt;
    }
}