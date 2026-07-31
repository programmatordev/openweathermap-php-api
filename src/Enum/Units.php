<?php

namespace ProgrammatorDev\OpenWeatherMap\Enum;

enum Units: string
{
    case STANDARD = 'standard';
    case METRIC = 'metric';
    case IMPERIAL = 'imperial';

    public function temperatureUnit(): Unit
    {
        return match ($this) {
            self::STANDARD => Unit::KELVIN,
            self::METRIC => Unit::CELSIUS,
            self::IMPERIAL => Unit::FAHRENHEIT,
        };
    }

    public function speedUnit(): Unit
    {
        return match ($this) {
            self::STANDARD, self::METRIC => Unit::METERS_PER_SECOND,
            self::IMPERIAL => Unit::MILES_PER_HOUR,
        };
    }
}
