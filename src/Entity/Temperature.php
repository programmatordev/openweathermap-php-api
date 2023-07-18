<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Temperature
{
    private float $morning;

    private float $day;

    private float $evening;

    private float $night;

    private ?float $min;

    private ?float $max;

    public function __construct(array $data)
    {
        $this->morning = $data['morn'];
        $this->day = $data['day'];
        $this->evening = $data['eve'];
        $this->night = $data['night'];
        $this->min = $data['min'] ?? null;
        $this->max = $data['max'] ?? null;
    }

    public function getMorning(): float
    {
        return $this->morning;
    }

    public function getDay(): float
    {
        return $this->day;
    }

    public function getEvening(): float
    {
        return $this->evening;
    }

    public function getNight(): float
    {
        return $this->night;
    }

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }
}