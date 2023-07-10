<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\DataProvider;

use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;

class InvalidParamDataProvider
{
    public static function provideInvalidCoordinateData(): \Generator
    {
        yield 'latitude lower than -90' => [-91, -9.1365919, ValidationException::class];
        yield 'latitude greater than 90' => [91, -9.1365919, ValidationException::class];
        yield 'longitude lower than -180' => [38.7077507, -181, ValidationException::class];
        yield 'longitude greater than 180' => [38.7077507, 181, ValidationException::class];
    }

    public static function provideInvalidPastDateData(): \Generator
    {
        yield 'invalid past date' => [
            new \DateTimeImmutable('1 days'),
            ValidationException::class
        ];
    }

    public static function provideInvalidDateRangeData(): \Generator
    {
        yield 'start date greater than end date' => [
            new \DateTimeImmutable('-4 days'),
            new \DateTimeImmutable('-5 days'),
            ValidationException::class
        ];
    }

    public static function provideInvalidNumResultsData(): \Generator
    {
        yield 'equal to zero num results' => [0,  ValidationException::class];
        yield 'negative num results' => [-1,  ValidationException::class];
    }

    public static function provideInvalidMeasurementSystemData(): \Generator
    {
        yield 'not allowed measurement system' => ['invalid', ValidationException::class];
    }

    public static function provideInvalidLanguageData(): \Generator
    {
        yield 'not allowed language' => ['invalid', ValidationException::class];
    }
}