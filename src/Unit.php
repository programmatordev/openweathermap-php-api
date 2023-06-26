<?php

namespace ProgrammatorDev\OpenWeatherMap;

class Unit
{
    public const CELSIUS = 'metric';
    public const FAHRENHEIT = 'imperial';
    public const KELVIN = 'standard';

    public static function getList(): array
    {
        return [
            self::CELSIUS,
            self::FAHRENHEIT,
            self::KELVIN
        ];
    }
}