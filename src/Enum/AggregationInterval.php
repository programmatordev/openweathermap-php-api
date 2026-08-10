<?php

namespace ProgrammatorDev\OpenWeatherMap\Enum;

/**
 * Intervals supported by the Weather Stations measurement aggregation endpoint.
 *
 * @see https://openweathermap.org/api/stations#measurement
 */
enum AggregationInterval: string
{
    case MINUTE = 'm';
    case HOUR = 'h';
    case DAY = 'd';
}
