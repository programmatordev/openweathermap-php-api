<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Util\EntityListTrait;

class OneCall
{
    use EntityListTrait;

    private Coordinate $coordinate;

    private Timezone $timezone;

    private Weather $current;

    /** @var ?MinuteForecast[] */
    private ?array $minutelyForecast;

    /** @var Weather[] */
    private array $hourlyForecast;

    /** @var Weather[] */
    private array $dailyForecast;

    /** @var ?Alert[] */
    private ?array $alerts;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate($data);
        $this->timezone = new Timezone($data);
        $this->current = new Weather($data['current']);
        $this->minutelyForecast = !empty($data['minutely']) ? $this->createEntityList(MinuteForecast::class, $data['minutely']) : null;
        $this->hourlyForecast = $this->createEntityList(Weather::class, $data['hourly']);
        $this->dailyForecast = $this->createEntityList(Weather::class, $data['daily']);
        $this->alerts = !empty($data['alerts']) ? $this->createEntityList(Alert::class, $data['alerts']) : null;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getTimezone(): Timezone
    {
        return $this->timezone;
    }

    public function getCurrent(): Weather
    {
        return $this->current;
    }

    public function getMinutelyForecast(): ?array
    {
        return $this->minutelyForecast;
    }

    public function getHourlyForecast(): array
    {
        return $this->hourlyForecast;
    }

    public function getDailyForecast(): array
    {
        return $this->dailyForecast;
    }

    public function getAlerts(): ?array
    {
        return $this->alerts;
    }
}