<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Util\EntityTrait;

class AirPollutionCollection
{
    use EntityTrait;

    private int $numResults;

    private Coordinate $coordinate;

    /** @var AirPollutionData[] */
    private array $data;

    public function __construct(array $data)
    {
        $this->numResults = count($data['list']);
        $this->coordinate = new Coordinate($data['coord']);
        $this->data = $this->createEntityList(AirPollutionData::class, $data['list']);
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getData(): array
    {
        return $this->data;
    }
}