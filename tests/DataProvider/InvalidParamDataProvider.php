<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\DataProvider;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidDateRangeException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidNumResultsException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidPastDateException;
use ProgrammatorDev\OpenWeatherMap\Exception\OutOfRangeCoordinateException;

class InvalidParamDataProvider
{
    public static function provideInvalidCoordinateData(): \Generator
    {
        yield 'latitude lower than -90' => [-91, -9.1365919, OutOfRangeCoordinateException::class];
        yield 'latitude greater than 90' => [91, -9.1365919, OutOfRangeCoordinateException::class];
        yield 'longitude lower than -180' => [38.7077507, -181, OutOfRangeCoordinateException::class];
        yield 'longitude greater than 180' => [38.7077507, 181, OutOfRangeCoordinateException::class];
    }

    public static function provideInvalidPastDateData(): \Generator
    {
        yield 'invalid past start date' => [
            new \DateTimeImmutable('1 days'),
            new \DateTimeImmutable('-4 days'),
            InvalidPastDateException::class
        ];
        yield 'invalid past end date' => [
            new \DateTimeImmutable('-5 days'),
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
}