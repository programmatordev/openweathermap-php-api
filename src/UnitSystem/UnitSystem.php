<?php

namespace ProgrammatorDev\OpenWeatherMap\UnitSystem;

use ProgrammatorDev\OpenWeatherMap\Helper\ReflectionHelper;

class UnitSystem
{
    public const METRIC = 'metric';
    public const IMPERIAL = 'imperial';
    public const STANDARD = 'standard';

    public static function getOptions(): array
    {
        return ReflectionHelper::getClassConstants(self::class);
    }
}