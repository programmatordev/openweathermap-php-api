<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Wind
{
    private float $speed;

    private int $direction;

    private ?float $gust;

    public function __construct(array $data)
    {
        $this->speed = $data['speed'];
        $this->direction = $data['deg'];

        $this->gust = $data['gust'] ?? null;
    }

    public function getSpeed(): float
    {
        return $this->speed;
    }

    /**
     * Wind direction, in degrees
     */
    public function getDirection(): int
    {
        return $this->direction;
    }

    public function getGust(): float
    {
        return $this->gust;
    }
}