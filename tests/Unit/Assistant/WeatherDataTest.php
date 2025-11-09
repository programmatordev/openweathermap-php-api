<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Assistant;

use ProgrammatorDev\OpenWeatherMap\Entity\Assistant\WeatherData;
use ProgrammatorDev\OpenWeatherMap\Entity\Condition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;

class WeatherDataTest extends AbstractTest
{
    public function testMethods()
    {
        $entity = new WeatherData('locationName', [
            'clouds' => 100,
            'dew_point' => 10,
            'dt' => 1762622549,
            'feels_like' => 10,
            'humidity' => 10,
            'pressure' => 1000,
            'sunrise' => 1762602543,
            'sunset' => 1762638067,
            'temp' => 10,
            'uvi' => 1,
            'visibility' => 10000,
            'weather' => [
                [
                    'description' => 'description',
                    'icon' => '01d',
                    'id' => 200,
                    'main' => 'name'
                ]
            ],
            'wind_deg' => 10,
            'wind_speed' => 10,
            'wind_gust' => 10,
        ]);

        $this->assertSame('locationName', $entity->getLocationName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getDateTime());
        $this->assertSame(1000, $entity->getAtmosphericPressure());
        $this->assertSame(10, $entity->getHumidity());
        $this->assertSame(10.0, $entity->getDewPoint());
        $this->assertSame(1.0, $entity->getUltraVioletIndex());
        $this->assertSame(100, $entity->getCloudiness());
        $this->assertInstanceOf(Wind::class, $entity->getWind());
        $this->assertContainsOnlyInstancesOf(Condition::class, $entity->getConditions());
        $this->assertSame(10.0, $entity->getTemperature());
        $this->assertSame(10.0, $entity->getTemperatureFeelsLike());
        $this->assertSame(10000, $entity->getVisibility());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getSunriseAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getSunsetAt());
    }
}