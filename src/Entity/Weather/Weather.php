<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\OpenWeatherMap\Entity\Rain;
use ProgrammatorDev\OpenWeatherMap\Entity\Snow;
use ProgrammatorDev\OpenWeatherMap\Entity\WeatherCondition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;

class Weather
{
    use CreateEntityListTrait;

    private float $temperature;

    private float $temperatureFeelsLike;

    private float $minTemperature;

    private float $maxTemperature;

    private int $humidity;

    private int $cloudiness;

    private int $visibility;

    private array $weatherConditions;

    private Wind $wind;

    private ?Rain $rain;

    private ?Snow $snow;

    private AtmosphericPressure $atmosphericPressure;

    private \DateTimeImmutable $dateTime;

    public function __construct(array $data)
    {
        $this->temperature = $data['main']['temp'];
        $this->temperatureFeelsLike = $data['main']['feels_like'];
        $this->minTemperature = $data['main']['temp_min'];
        $this->maxTemperature = $data['main']['temp_max'];
        $this->humidity = $data['main']['humidity'];
        $this->cloudiness = $data['clouds']['all'];
        $this->visibility = $data['visibility'];
        $this->weatherConditions = $this->createEntityList($data['weather'], WeatherCondition::class);
        $this->wind = new Wind($data['wind']);
        $this->atmosphericPressure = new AtmosphericPressure($data['main']);
        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt'], new \DateTimeZone('UTC'));

        $this->rain = (isset($data['rain']) || isset($data['pop']))
            ? new Rain([
                'pop' => $data['pop'] ?? null,
                '1h' => $data['rain']['1h'] ?? null,
                '3h' => $data['rain']['3h'] ?? null
            ])
            : null;
        $this->snow = (isset($data['snow']))
            ? new Snow($data['snow'])
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

    public function getMinTemperature(): float
    {
        return $this->minTemperature;
    }

    public function getMaxTemperature(): float
    {
        return $this->maxTemperature;
    }

    public function getHumidity(): int
    {
        return $this->humidity;
    }

    public function getCloudiness(): int
    {
        return $this->cloudiness;
    }

    public function getVisibility(): int
    {
        return $this->visibility;
    }

    /**
     * @return WeatherCondition[]
     */
    public function getWeatherConditions(): array
    {
        return $this->weatherConditions;
    }

    public function getWind(): Wind
    {
        return $this->wind;
    }

    public function getRain(): ?Rain
    {
        return $this->rain;
    }

    public function getSnow(): ?Snow
    {
        return $this->snow;
    }

    public function getAtmosphericPressure(): AtmosphericPressure
    {
        return $this->atmosphericPressure;
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }
}