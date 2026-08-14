<?php

namespace ProgrammatorDev\OpenWeatherMap\Enum;

/**
 * OpenWeather's Air Quality Index scale.
 *
 * @see https://openweathermap.org/api/air-pollution
 */
enum AirQualityIndex: int
{
    case GOOD = 1;
    case FAIR = 2;
    case MODERATE = 3;
    case POOR = 4;
    case VERY_POOR = 5;
}
