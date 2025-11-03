<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

class DayData extends BaseWeather
{
    private Temperature $temperature;

    private Temperature $temperatureFeelsLike;

    private int $precipitationProbability;

    private string $summary;

    private MoonPhase $moonPhase;

    private \DateTimeImmutable $moonriseAt;

    private \DateTimeImmutable $moonsetAt;

    private \DateTimeImmutable $sunriseAt;

    private \DateTimeImmutable $sunsetAt;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->temperature = new Temperature($data['temp']);
        $this->temperatureFeelsLike = new Temperature($data['feels_like']);
        $this->precipitationProbability = round($data['pop'] * 100);
        $this->summary = $data['summary'];
        $this->moonPhase = new MoonPhase($data);
        $this->moonriseAt = \DateTimeImmutable::createFromFormat('U', $data['moonrise']);
        $this->moonsetAt = \DateTimeImmutable::createFromFormat('U', $data['moonset']);
        $this->sunriseAt = \DateTimeImmutable::createFromFormat('U', $data['sunrise']);
        $this->sunsetAt = \DateTimeImmutable::createFromFormat('U', $data['sunset']);
    }

    public function getTemperature(): Temperature
    {
        return $this->temperature;
    }

    public function getTemperatureFeelsLike(): Temperature
    {
        return $this->temperatureFeelsLike;
    }

    public function getPrecipitationProbability(): int
    {
        return $this->precipitationProbability;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getMoonPhase(): MoonPhase
    {
        return $this->moonPhase;
    }

    /**
     * Moonrise date in UTC
     */
    public function getMoonriseAt(): \DateTimeImmutable
    {
        return $this->moonriseAt;
    }

    /**
     * Moonset date in UTC
     */
    public function getMoonsetAt(): \DateTimeImmutable
    {
        return $this->moonsetAt;
    }

    /**
     * Sunrise date in UTC
     */
    public function getSunriseAt(): \DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    /**
     * Sunset date in UTC
     */
    public function getSunsetAt(): \DateTimeImmutable
    {
        return $this->sunsetAt;
    }
}