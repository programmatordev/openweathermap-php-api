<?php

namespace ProgrammatorDev\OpenWeatherMap\MeasurementSystem;

class MeasurementSystem
{
    public const METRIC = 'metric';
    public const IMPERIAL = 'imperial';
    public const STANDARD = 'standard';

    public static function getList(): array
    {
        return [
            self::METRIC,
            self::IMPERIAL,
            self::STANDARD
        ];
    }
}