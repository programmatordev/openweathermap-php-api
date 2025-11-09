<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Integration;

use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Weather;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherCollection;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;
use ProgrammatorDev\OpenWeatherMap\Test\MockResponse;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestItemResponseTrait;

class WeatherResourceTest extends AbstractTest
{
    use TestItemResponseTrait;

    public static function provideItemResponseData(): \Generator
    {
        yield 'get current' => [
            Weather::class,
            MockResponse::WEATHER_CURRENT,
            'weather',
            'getCurrent',
            [50, 50]
        ];
        yield 'get forecast' => [
            WeatherCollection::class,
            MockResponse::WEATHER_FORECAST,
            'weather',
            'getForecast',
            [50, 50]
        ];
    }
}