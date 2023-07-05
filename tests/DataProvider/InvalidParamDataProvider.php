<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\DataProvider;

class InvalidParamDataProvider
{
    public static function provideInvalidCoordinateData(): \Generator
    {
        yield 'latitude lower than -90' => [-91, -9.1365919, \UnexpectedValueException::class];
        yield 'latitude greater than 90' => [91, -9.1365919, \UnexpectedValueException::class];
        yield 'longitude lower than -180' => [38.7077507, -181, \UnexpectedValueException::class];
        yield 'longitude greater than 180' => [38.7077507, 181, \UnexpectedValueException::class];
    }

    public static function provideInvalidPastDateData(): \Generator
    {
        yield 'invalid past date' => [
            new \DateTimeImmutable('1 days'),
            \UnexpectedValueException::class
        ];
    }

    public static function provideInvalidDateRangeData(): \Generator
    {
        yield 'start date greater than end date' => [
            new \DateTimeImmutable('-4 days'),
            new \DateTimeImmutable('-5 days'),
            \UnexpectedValueException::class
        ];
    }

    public static function provideInvalidNumResultsData(): \Generator
    {
        yield 'equal to zero num results' => [0,  \UnexpectedValueException::class];
        yield 'negative num results' => [-1,  \UnexpectedValueException::class];
    }

    public static function provideInvalidMeasurementSystemData(): \Generator
    {
        yield 'not allowed measurement system' => ['invalid', \UnexpectedValueException::class];
    }

    public static function provideInvalidLanguageData(): \Generator
    {
        yield 'not allowed language' => ['invalid', \UnexpectedValueException::class];
    }
}