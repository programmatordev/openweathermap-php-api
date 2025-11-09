<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Temperature;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherSummary;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;

class WeatherSummaryTest extends AbstractTest
{
    public function testMethods()
    {
        $entity = new WeatherSummary([
            'lat' => 50,
            'lon' => 50,
            'tz' => '+00:00',
            'date' => '2024-01-01',
            'cloud_cover' => [
                'afternoon' => 10
            ],
            'humidity' => [
                'afternoon' => 10
            ],
            'precipitation' => [
                'total' => 1
            ],
            'temperature' => [
                'morning' => 10,
                'afternoon' => 15,
                'evening' => 15,
                'night' => 10,
                'min' => 10,
                'max' => 15
            ],
            'pressure' => [
                'afternoon' => 1000
            ],
            'wind' => [
                'max' => [
                    'speed' => 10,
                    'direction' => 10
                ]
            ]
        ]);

        $this->assertInstanceOf(Coordinate::class, $entity->getCoordinate());
        $this->assertInstanceOf(Timezone::class, $entity->getTimezone());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getDateTime());
        $this->assertSame(10, $entity->getCloudiness());
        $this->assertSame(10, $entity->getHumidity());
        $this->assertSame(1.0, $entity->getPrecipitation());
        $this->assertInstanceOf(Temperature::class, $entity->getTemperature());
        $this->assertSame(1000, $entity->getAtmosphericPressure());
        $this->assertInstanceOf(Wind::class, $entity->getWind());
    }
}