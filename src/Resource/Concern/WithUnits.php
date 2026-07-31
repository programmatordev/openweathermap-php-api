<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource\Concern;

use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

trait WithUnits
{
    // Null means this resource inherits the API-wide unit system.
    private ?Units $unitsOverride = null;

    public function withUnits(Units $units): static
    {
        $clone = clone $this;
        $clone->unitsOverride = $units;

        return $clone;
    }

    protected function resolvedUnits(): Units
    {
        return $this->unitsOverride ?? $this->runtime->config()->get(OpenWeatherMap::OPTION_UNITS);
    }
}
