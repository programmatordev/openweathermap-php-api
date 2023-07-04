<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;

class HistoryDaySummary
{
    private Coordinate $coordinate;

    private Timezone $timezone;

    private \DateTimeImmutable $dateTime;

    private int $cloudiness;

    private int $humidity;

    private float $precipitation;

    private Temperature $temperature;

    private int $atmosphericPressure;

    private Wind $wind;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate($data);
        $this->timezone = new Timezone([
            'timezone_offset' => \DateTimeImmutable::createFromFormat('P', $data['tz'])->getOffset()
        ]);
        $this->dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $data['date'], new \DateTimeZone('UTC'))
            ->setTime(0, 0);
        $this->cloudiness = round($data['cloud_cover']['afternoon']);
        $this->humidity = round($data['humidity']['afternoon']);
        $this->precipitation = $data['precipitation']['total'];
        $this->temperature = new Temperature([
            'morn' => $data['temperature']['morning'],
            'day' => $data['temperature']['afternoon'],
            'eve' => $data['temperature']['evening'],
            'night' => $data['temperature']['night'],
            'min' => $data['temperature']['min'],
            'max' => $data['temperature']['max']
        ]);
        $this->atmosphericPressure = round($data['pressure']['afternoon']);
        $this->wind = new Wind([
            'speed' => $data['wind']['max']['speed'],
            'deg' => round($data['wind']['max']['direction'])
        ]);
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getTimezone(): Timezone
    {
        return $this->timezone;
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getCloudiness(): int
    {
        return $this->cloudiness;
    }

    public function getHumidity(): int
    {
        return $this->humidity;
    }

    public function getPrecipitation(): float
    {
        return $this->precipitation;
    }

    public function getTemperature(): Temperature
    {
        return $this->temperature;
    }

    public function getAtmosphericPressure(): int
    {
        return $this->atmosphericPressure;
    }

    public function getWind(): Wind
    {
        return $this->wind;
    }
}