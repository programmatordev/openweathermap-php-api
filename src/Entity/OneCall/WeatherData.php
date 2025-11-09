<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\BaseWeather;

class WeatherData extends BaseWeather
{
    private float $temperature;

    private float $temperatureFeelsLike;

    private ?int $visibility;

    private ?\DateTimeImmutable $sunriseAt;

    private ?\DateTimeImmutable $sunsetAt;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->temperature = $data['temp'];
        $this->temperatureFeelsLike = $data['feels_like'];
        $this->visibility = $data['visibility'] ?? null;

        $this->sunriseAt = isset($data['sunrise'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunrise'])
            : null;

        $this->sunsetAt = isset($data['sunset'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunset'])
            : null;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getTemperatureFeelsLike(): float
    {
        return $this->temperatureFeelsLike;
    }

    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    /**
     * Sunrise date in UTC
     */
    public function getSunriseAt(): ?\DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    /**
     * Sunset date in UTC
     */
    public function getSunsetAt(): ?\DateTimeImmutable
    {
        return $this->sunsetAt;
    }
}