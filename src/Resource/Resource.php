<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Resource\Util\CacheTrait;

class Resource
{
    use CacheTrait;

    public function __construct(protected OpenWeatherMap $api) {}
}