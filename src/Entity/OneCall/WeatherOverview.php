<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;

class WeatherOverview
{
    private Coordinate $coordinate;

    private Timezone $timezone;

    private \DateTimeImmutable $dateTime;

    private string $overview;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate($data);

        $this->timezone = new Timezone([
            'timezone_offset' => \DateTimeImmutable::createFromFormat('P', $data['tz'])->getOffset()
        ]);

        $this->dateTime = \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s P',
            sprintf('%s 00:00:00 %s', $data['date'], $data['tz'])
        );

        $this->overview = $data['weather_overview'];
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

    public function getOverview(): string
    {
        return $this->overview;
    }
}