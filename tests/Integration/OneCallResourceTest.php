<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Integration;

use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Weather;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherMoment;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherSummary;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;
use ProgrammatorDev\OpenWeatherMap\Test\MockResponse;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestItemResponseTrait;

class OneCallResourceTest extends AbstractTest
{
    use TestItemResponseTrait;

    public static function provideItemResponseData(): \Generator
    {
        yield 'get weather' => [
            Weather::class,
            MockResponse::ONE_CALL_WEATHER,
            'oneCall',
            'getWeather',
            [50, 50]
        ];
        yield 'get weather by date' => [
            WeatherMoment::class,
            MockResponse::ONE_CALL_TIMEMACHINE,
            'oneCall',
            'getWeatherByDate',
            [50, 50, new \DateTime()]
        ];
        yield 'get weather summary by date' => [
            WeatherSummary::class,
            MockResponse::ONE_CALL_DAY_SUMMARY,
            'oneCall',
            'getWeatherSummaryByDate',
            [50, 50, new \DateTime()]
        ];
    }
}