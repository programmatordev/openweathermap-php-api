<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Assistant;

use ProgrammatorDev\OpenWeatherMap\Entity\Assistant\Answer;
use ProgrammatorDev\OpenWeatherMap\Entity\Assistant\WeatherData;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;

class AnswerTest extends AbstractTest
{
    public function testMethods()
    {
        $entity = new Answer([
            'answer' => 'Answer text',
            'data' => [
                'location' => [
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
                ]
            ],
            'session_id' => '777f049a-c96b-423f-baef-ca34ff725fe9'
        ]);

        $this->assertSame('Answer text', $entity->getAnswer());
        $this->assertSame('777f049a-c96b-423f-baef-ca34ff725fe9', $entity->getSessionId());
        $this->assertContainsOnlyInstancesOf(WeatherData::class, $entity->getData());
    }
}