<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource\Util;

use function DeepCopy\deep_copy;

trait UnitSystemTrait
{
    public function withUnitSystem(string $unitSystem): static
    {
        $clone = deep_copy($this, true);
        $clone->api->addQueryDefault('units', $unitSystem);

        return $clone;
    }
}