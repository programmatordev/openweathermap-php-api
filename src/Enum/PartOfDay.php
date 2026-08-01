<?php

namespace ProgrammatorDev\OpenWeatherMap\Enum;

/**
 * Backed by the `sys.pod` codes documented by the 5 Day / 3 Hour Forecast API.
 *
 * @see https://openweathermap.org/api/forecast5
 */
enum PartOfDay: string
{
    case DAY = 'd';
    case NIGHT = 'n';
}
