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

    private Weather $current;

    private ?array $minutelyForecast;

    private array $hourlyForecast;

    private array $dailyForecast;

    private ?array $alerts;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate($data);
        $this->timezone = new Timezone($data);
        $this->current = new Weather($data['current']);
        $this->minutelyForecast = !empty($data['minutely'])
            ? $this->createEntityList($data['minutely'], MinuteForecast::class)
            : null;
        $this->hourlyForecast = $this->createEntityList($data['hourly'], Weather::class);
        $this->dailyForecast = $this->createEntityList($data['daily'], Weather::class);
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

    public function getCurrent(): Weather
    {
        return $this->current;
    }

    /**
     * @return MinuteForecast[]|null
     */
    public function getMinutelyForecast(): ?array
    {
        return $this->minutelyForecast;
    }

    /**
     * @return Weather[]
     */
    public function getHourlyForecast(): array
    {
        return $this->hourlyForecast;
    }

    /**
     * @return Weather[]
     */
    public function getDailyForecast(): array
    {
        return $this->dailyForecast;
    }

    /**
     * @return Alert[]|null
     */
    public function getAlerts(): ?array
    {
        return $this->alerts;
    }
}