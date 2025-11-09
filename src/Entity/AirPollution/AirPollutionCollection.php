<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Helper\EntityHelper;

class AirPollutionCollection
{
    private int $numResults;

    private Coordinate $coordinate;

    /** @var AirPollutionData[] */
    private array $data;

    public function __construct(array $data)
    {
        $this->numResults = count($data['list']);
        $this->coordinate = new Coordinate($data['coord']);
        $this->data = EntityHelper::createEntityList(AirPollutionData::class, $data['list']);
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