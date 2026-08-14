<?php

namespace ProgrammatorDev\OpenWeatherMap\Hydration;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

final class UnitsResolver
{
    private function __construct() {}

    public static function fromContext(?Context $context): Units
    {
        $units = $context?->config()->get(
            OpenWeatherMap::OPTION_UNITS,
            Units::METRIC,
        ) ?? Units::METRIC;

        if (!$units instanceof Units) {
            throw new \LogicException(sprintf(
                'The hydration context "%s" value must be an instance of %s.',
                OpenWeatherMap::OPTION_UNITS,
                Units::class,
            ));
        }

        return $units;
    }
}
