<?php

namespace ProgrammatorDev\OpenWeatherMap\Enum;

enum Unit: string
{
    case KELVIN = 'K';
    case CELSIUS = '°C';
    case FAHRENHEIT = '°F';
    case METERS_PER_SECOND = 'm/s';
    case MILES_PER_HOUR = 'mph';
    case HECTOPASCAL = 'hPa';
    case PERCENT = '%';
    case METER = 'm';
    case DEGREE = '°';
    case MILLIMETER = 'mm';
    case MILLIMETERS_PER_HOUR = 'mm/h';
    case MICROGRAMS_PER_CUBIC_METER = 'µg/m³';

    public function symbol(): string
    {
        return $this->value;
    }
}
