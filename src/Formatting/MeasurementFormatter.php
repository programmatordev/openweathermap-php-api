<?php

namespace ProgrammatorDev\OpenWeatherMap\Formatting;

use ProgrammatorDev\OpenWeatherMap\Enum\Unit;

final class MeasurementFormatter
{
    private function __construct() {}

    public static function format(int|float|null $value, Unit $unit): ?string
    {
        if ($value === null) {
            return null;
        }

        // PHP 8 numeric string conversion is locale-independent
        // and does not impose formatter-specific rounding or decimal padding.
        return sprintf('%s %s', $value, $unit->symbol());
    }
}
