<?php

namespace ProgrammatorDev\OpenWeatherMap\Enum;

/**
 * Current layers supported by Weather Maps API 1.0.
 *
 * @see https://openweathermap.org/api/weathermaps
 */
enum MapLayer: string
{
    case CLOUDS = 'clouds_new';
    case PRECIPITATION = 'precipitation_new';
    case PRESSURE = 'pressure_new';
    case WIND = 'wind_new';
    case TEMPERATURE = 'temp_new';
}
