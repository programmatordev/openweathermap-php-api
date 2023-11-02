<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Util\EntityListTrait;

class AirPollutionLocationList
{
    use EntityListTrait;

    private Coordinate $coordinate;

    /** @var AirPollution[] */
    private array $list;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate($data['coord']);
        $this->list = $this->createEntityList(AirPollution::class, $data['list']);
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getList(): array
    {
        return $this->list;
    }
}