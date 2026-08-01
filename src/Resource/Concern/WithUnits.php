<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource\Concern;

use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

trait WithUnits
{
    public function withUnits(Units $units): static
    {
        return $this->withConfig([
            OpenWeatherMap::OPTION_UNITS => $units,
        ]);
    }

    protected function resolvedUnits(): Units
    {
        return $this->runtime->config()->get(OpenWeatherMap::OPTION_UNITS);
    }
}
