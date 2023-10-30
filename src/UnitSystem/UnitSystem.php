<?php

namespace ProgrammatorDev\OpenWeatherMap\UnitSystem;

use ProgrammatorDev\OpenWeatherMap\Util\ClassConstantsTrait;

class UnitSystem
{
    use ClassConstantsTrait;

    public const METRIC = 'metric';
    public const IMPERIAL = 'imperial';
    public const STANDARD = 'standard';

    public static function getList(): array
    {
        return (new UnitSystem)->getClassConstants(self::class);
    }
}