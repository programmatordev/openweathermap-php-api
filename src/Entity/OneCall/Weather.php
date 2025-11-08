<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Helper\EntityHelper;

class Weather
{
    private Coordinate $coordinate;

    private Timezone $timezone;

    private WeatherData $current;

    /** @var ?MinuteData[] */
    private ?array $minutelyForecast;

    /** @var HourData[] */
    private array $hourlyForecast;

    /** @var DayData[] */
    private array $dailyForecast;

    /** @var ?Alert[] */
    private ?array $alerts;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate([
            'lat' => $data['lat'],
            'lon' => $data['lon']
        ]);

        $this->timezone = new Timezone([
            'timezone' => $data['timezone'],
            'timezone_offset' => $data['timezone_offset']
        ]);

        $this->current = new WeatherData($data['current']);

        $this->minutelyForecast = isset($data['minutely'])
            ? EntityHelper::createEntityList(MinuteData::class, $data['minutely'])
            : null;

        $this->hourlyForecast = EntityHelper::createEntityList(HourData::class, $data['hourly']);
        $this->dailyForecast = EntityHelper::createEntityList(DayData::class, $data['daily']);

        $this->alerts = isset($data['alerts'])
            ? EntityHelper::createEntityList(Alert::class, $data['alerts'])
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

    public function getCurrent(): WeatherData
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