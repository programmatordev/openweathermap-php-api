<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;

class AirPollutionLocationList
{
    use CreateEntityListTrait;

    private Coordinate $coordinate;

    private array $list;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate($data['coord']);
        $this->list = $this->createEntityList($data['list'], AirPollution::class);
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    /**
     * @return AirPollution[]
     */
    public function getList(): array
    {
        return $this->list;
    }
}