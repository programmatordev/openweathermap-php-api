<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\DataProvider;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidDateRangeException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidLanguageException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidMeasurementSystemException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidNumResultsException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidPastDateException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidCoordinateException;

class InvalidParamDataProvider
{
    public static function provideInvalidCoordinateData(): \Generator
    {
        yield 'latitude lower than -90' => [-91, -9.1365919, InvalidCoordinateException::class];
        yield 'latitude greater than 90' => [91, -9.1365919, InvalidCoordinateException::class];
        yield 'longitude lower than -180' => [38.7077507, -181, InvalidCoordinateException::class];
        yield 'longitude greater than 180' => [38.7077507, 181, InvalidCoordinateException::class];
    }

    public static function provideInvalidPastDateData(): \Generator
    {
        yield 'invalid past date' => [
            new \DateTimeImmutable('1 days'),
            InvalidPastDateException::class
        ];
    }

    public static function provideInvalidDateRangeData(): \Generator
    {
        yield 'start date greater than end date' => [
            new \DateTimeImmutable('-4 days'),
            new \DateTimeImmutable('-5 days'),
            InvalidDateRangeException::class
        ];
    }

    public static function provideInvalidNumResultsData(): \Generator
    {
        yield 'equal to zero num results' => [0,  InvalidNumResultsException::class];
        yield 'negative num results' => [-1,  InvalidNumResultsException::class];
    }

    public static function provideInvalidMeasurementSystemData(): \Generator
    {
        yield 'not allowed measurement system' => ['invalid', InvalidMeasurementSystemException::class];
    }

    public static function provideInvalidLanguageData(): \Generator
    {
        yield 'not allowed language' => ['invalid', InvalidLanguageException::class];
    }
}