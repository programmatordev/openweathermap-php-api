<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;

class AirPollutionList
{
    use CreateEntityListTrait;

    private Location $location;

    private array $list;

    public function __construct(array $data)
    {
        $this->location = new Location([
            'lat' => $data['coord']['lat'],
            'lon' => $data['coord']['lon']
        ]);

        $this->list = $this->createEntityList($data['list'], AirPollution::class);
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @return AirPollution[]
     */
    public function getList(): array
    {
        return $this->list;
    }
}