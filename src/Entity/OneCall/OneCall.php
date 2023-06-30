<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;

class OneCall
{
    use CreateEntityListTrait;

    private Coordinate $coordinate;

    private Timezone $timezone;

    private Current $current;

    private array $minutelyForecast;

    private array $hourlyForecast;

    private array $dailyForecast;

    private ?array $alerts;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate($data);
        $this->timezone = new Timezone($data);
        $this->current = new Current($data['current']);
        $this->minutelyForecast = $this->createEntityList($data['minutely'], MinuteForecast::class);
        $this->hourlyForecast = $this->createEntityList($data['hourly'], HourForecast::class);
        $this->alerts = !empty($data['alerts'])
            ? $this->createEntityList($data['alerts'], Alert::class)
            : null;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getTimezone(): Timezone
    {
        return $this->timezone;
    }

    public function getCurrent(): Current
    {
        return $this->current;
    }

    /**
     * @return MinuteForecast[]
     */
    public function getMinutelyForecast(): array
    {
        return $this->minutelyForecast;
    }

    /**
     * @return Alert[]|null
     */
    public function getAlerts(): ?array
    {
        return $this->alerts;
    }
}