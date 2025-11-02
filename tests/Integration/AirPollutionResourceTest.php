<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Integration;

use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionCollection;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;
use ProgrammatorDev\OpenWeatherMap\Test\MockResponse;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestItemResponseTrait;

class AirPollutionResourceTest extends AbstractTest
{
    use TestItemResponseTrait;

    public static function provideItemResponseData(): \Generator
    {
        yield 'get current' => [
            AirPollution::class,
            MockResponse::AIR_POLLUTION_CURRENT,
            'airPollution',
            'getCurrent',
            [50, 50]
        ];
        yield 'get forecast' => [
            AirPollutionCollection::class,
            MockResponse::AIR_POLLUTION_FORECAST,
            'airPollution',
            'getForecast',
            [50, 50]
        ];
        yield 'get history' => [
            AirPollutionCollection::class,
            MockResponse::AIR_POLLUTION_HISTORY,
            'airPollution',
            'getHistory',
            [50, 50, new \DateTime('-1 day'), new \DateTime('now')]
        ];
    }
}