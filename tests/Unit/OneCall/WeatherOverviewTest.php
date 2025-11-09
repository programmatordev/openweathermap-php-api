<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherOverview;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;

class WeatherOverviewTest extends AbstractTest
{
    public function testMethods()
    {
        $entity = new WeatherOverview([
            'lat' => 50,
            'lon' => 50,
            'tz' => '+00:00',
            'date' => '2025-01-01',
            'weather_overview' => 'Weather overview text'
        ]);

        $this->assertInstanceOf(Coordinate::class, $entity->getCoordinate());
        $this->assertInstanceOf(Timezone::class, $entity->getTimezone());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getDateTime());
        $this->assertSame('Weather overview text', $entity->getOverview());
    }
}