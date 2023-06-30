<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\OpenWeatherMap\Entity\Rain;
use ProgrammatorDev\OpenWeatherMap\Entity\Snow;
use ProgrammatorDev\OpenWeatherMap\Entity\WeatherCondition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;

class Weather
{
    use CreateEntityListTrait;

    private \DateTimeImmutable $dateTime;

    private ?\DateTimeImmutable $sunriseAt;

    private ?\DateTimeImmutable $sunsetAt;

    private float $temperature;

    private float $temperatureFeelsLike;

    private int $atmosphericPressure;

    private int $humidity;

    private float $dewPoint;

    private float $ultraVioletIndex;

    private int $cloudiness;

    private int $visibility;

    private Wind $wind;

    private ?Rain $rain;

    private ?Snow $snow;

    private array $weatherConditions;

    public function __construct(array $data)
    {
        $timezoneUtc = new \DateTimeZone('UTC');

        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt'], $timezoneUtc);
        $this->sunriseAt = !empty($data['sunrise'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunrise'], $timezoneUtc)
            : null;
        $this->sunsetAt = !empty($data['sunset'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunset'], $timezoneUtc)
            : null;
        $this->temperature = $data['temp'];
        $this->temperatureFeelsLike = $data['feels_like'];
        $this->atmosphericPressure = $data['pressure'];
        $this->humidity = $data['humidity'];
        $this->dewPoint = $data['dew_point'];
        $this->ultraVioletIndex = $data['uvi'];
        $this->cloudiness = $data['clouds'];
        $this->visibility = $data['visibility'];
        $this->wind = new Wind([
            'speed' => $data['wind_speed'],
            'deg' => $data['wind_deg'],
            'gust' => $data['wind_gust'] ?? null
        ]);
        $this->rain = (!empty($data['rain']) || isset($data['pop']))
            ? new Rain([
                'pop' => $data['pop'] ?? null,
                '1h' => $data['rain']['1h'] ?? null
            ])
            : null;
        $this->snow = !empty($data['snow'])
            ? new Snow($data['snow'])
            : null;
        $this->weatherConditions = $this->createEntityList($data['weather'], WeatherCondition::class);
    }


}