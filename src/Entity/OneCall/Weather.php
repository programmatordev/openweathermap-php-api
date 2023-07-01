<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Rain;
use ProgrammatorDev\OpenWeatherMap\Entity\Snow;
use ProgrammatorDev\OpenWeatherMap\Entity\WeatherCondition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;

class Weather
{
    use CreateEntityListTrait;

    private \DateTimeImmutable $dateTime;

    private ?\DateTimeImmutable $sunriseAt;

    private ?\DateTimeImmutable $sunsetAt;

    private ?\DateTimeImmutable $moonriseAt;

    private ?\DateTimeImmutable $moonsetAt;

    private ?MoonPhase $moonPhase;

    private Temperature|float $temperature;

    private Temperature|float $temperatureFeelsLike;

    private int $atmosphericPressure;

    private int $humidity;

    private float $dewPoint;

    private float $ultraVioletIndex;

    private int $cloudiness;

    private ?int $visibility;

    private Wind $wind;

    private ?int $precipitationProbability;

    private Rain|float|null $rain;

    private Snow|float|null $snow;

    private array $weatherConditions;

    public function __construct(array $data)
    {
        $timezoneUtc = new \DateTimeZone('UTC');

        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt'], $timezoneUtc);
        $this->sunriseAt = !empty($data['sunrise'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunrise'], $timezoneUtc)
            : null;
        $this->sunsetAt = !empty($data['sunset'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunset'], $timezoneUtc)
            : null;
        $this->moonriseAt = !empty($data['moonrise'])
            ? \DateTimeImmutable::createFromFormat('U', $data['moonrise'], $timezoneUtc)
            : null;
        $this->moonsetAt = !empty($data['moonset'])
            ? \DateTimeImmutable::createFromFormat('U', $data['moonset'], $timezoneUtc)
            : null;
        $this->moonPhase = !empty($data['moon_phase'])
            ? new MoonPhase($data)
            : null;
        $this->temperature = is_array($data['temp'])
            ? new Temperature($data['temp'])
            : $data['temp'];
        $this->temperatureFeelsLike = is_array($data['feels_like'])
            ? new Temperature($data['feels_like'])
            : $data['feels_like'];
        $this->atmosphericPressure = $data['pressure'];
        $this->humidity = $data['humidity'];
        $this->dewPoint = $data['dew_point'];
        $this->ultraVioletIndex = $data['uvi'];
        $this->cloudiness = $data['clouds'];
        $this->visibility = $data['visibility'] ?? null;
        $this->wind = new Wind([
            'speed' => $data['wind_speed'],
            'deg' => $data['wind_deg'],
            'gust' => $data['wind_gust'] ?? null
        ]);
        $this->precipitationProbability = isset($data['pop'])
            ? round($data['pop'] * 100)
            : null;
        $this->rain = !empty($data['rain'])
            ? is_array($data['rain']) ? new Rain($data['rain']) : $data['rain']
            : null;
        $this->snow = !empty($data['snow'])
            ? is_array($data['snow']) ? new Snow($data['snow']) : $data['snow']
            : null;
        $this->weatherConditions = $this->createEntityList($data['weather'], WeatherCondition::class);
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getSunriseAt(): ?\DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    public function getSunsetAt(): ?\DateTimeImmutable
    {
        return $this->sunsetAt;
    }

    public function getMoonriseAt(): ?\DateTimeImmutable
    {
        return $this->moonriseAt;
    }

    public function getMoonsetAt(): ?\DateTimeImmutable
    {
        return $this->moonsetAt;
    }

    public function getMoonPhase(): ?MoonPhase
    {
        return $this->moonPhase;
    }

    public function getTemperature(): Temperature|float
    {
        return $this->temperature;
    }

    public function getTemperatureFeelsLike(): Temperature|float
    {
        return $this->temperatureFeelsLike;
    }

    public function getAtmosphericPressure(): int
    {
        return $this->atmosphericPressure;
    }

    public function getHumidity(): int
    {
        return $this->humidity;
    }

    public function getDewPoint(): float
    {
        return $this->dewPoint;
    }

    public function getUltraVioletIndex(): float
    {
        return $this->ultraVioletIndex;
    }

    public function getCloudiness(): int
    {
        return $this->cloudiness;
    }

    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    public function getWind(): Wind
    {
        return $this->wind;
    }

    public function getPrecipitationProbability(): ?int
    {
        return $this->precipitationProbability;
    }

    public function getRain(): Rain|float|null
    {
        return $this->rain;
    }

    public function getSnow(): Snow|float|null
    {
        return $this->snow;
    }

    /**
     * @return WeatherCondition[]
     */
    public function getWeatherConditions(): array
    {
        return $this->weatherConditions;
    }
}