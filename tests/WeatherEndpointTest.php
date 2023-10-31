<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Endpoint\WeatherEndpoint;
use ProgrammatorDev\OpenWeatherMap\Entity\AtmosphericPressure;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Icon;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Rain;
use ProgrammatorDev\OpenWeatherMap\Entity\Snow;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Weather;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherLocationList;
use ProgrammatorDev\OpenWeatherMap\Entity\WeatherCondition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointInvalidResponseTrait;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointSuccessResponseTrait;

class WeatherEndpointTest extends AbstractTest
{
    use TestEndpointSuccessResponseTrait;
    use TestEndpointInvalidResponseTrait;

    public static function provideEndpointSuccessResponseData(): \Generator
    {
        yield 'get current' => [
            MockResponse::WEATHER_CURRENT,
            'weather',
            'getCurrent',
            [50, 50],
            'assertGetCurrentResponse'
        ];
        yield 'get forecast' => [
            MockResponse::WEATHER_FORECAST,
            'weather',
            'getForecast',
            [50, 50],
            'assertGetForecastResponse'
        ];
    }

    public static function provideEndpointInvalidResponseData(): \Generator
    {
        yield 'get current, latitude lower than -90' => ['weather', 'getCurrent', [-91, 50]];
        yield 'get current, latitude greater than 90' => ['weather', 'getCurrent', [91, 50]];
        yield 'get current, longitude lower than -180' => ['weather', 'getCurrent', [50, -181]];
        yield 'get current, longitude greater than 180' => ['weather', 'getCurrent', [50, 181]];

        yield 'get forecast, latitude lower than -90' => ['weather', 'getForecast', [-91, 50]];
        yield 'get forecast, latitude greater than 90' => ['weather', 'getForecast', [91, 50]];
        yield 'get forecast, longitude lower than -180' => ['weather', 'getForecast', [50, -181]];
        yield 'get forecast, longitude greater than 180' => ['weather', 'getForecast', [50, 181]];
        yield 'get forecast, zero num results' => ['weather', 'getForecast', [50, 50, 0]];
        yield 'get forecast, negative num results' => ['weather', 'getForecast', [50, 50, -1]];
    }

    public function testWeatherMethodsExist()
    {
        $this->assertSame(true, method_exists(WeatherEndpoint::class, 'withUnitSystem'));
        $this->assertSame(true, method_exists(WeatherEndpoint::class, 'withLanguage'));
        $this->assertSame(true, method_exists(WeatherEndpoint::class, 'withCacheTtl'));
    }

    private function assertGetCurrentResponse(WeatherLocation $weatherLocation): void
    {
        $this->assertSame(27.12, $weatherLocation->getTemperature());
        $this->assertSame(28.16, $weatherLocation->getTemperatureFeelsLike());
        $this->assertSame(22.76, $weatherLocation->getMinTemperature());
        $this->assertSame(29.9, $weatherLocation->getMaxTemperature());
        $this->assertSame(59, $weatherLocation->getHumidity());
        $this->assertSame(0, $weatherLocation->getCloudiness());
        $this->assertSame(10000, $weatherLocation->getVisibility());
        $this->assertSame(null, $weatherLocation->getPrecipitationProbability());
        $this->assertSame('2023-06-28 10:45:33', $weatherLocation->getDateTime()->format('Y-m-d H:i:s'));

        $weatherConditions = $weatherLocation->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $weatherConditions);
        $this->assertSame(800, $weatherConditions[0]->getId());
        $this->assertSame('Clear', $weatherConditions[0]->getName());
        $this->assertSame('clear sky', $weatherConditions[0]->getDescription());
        $this->assertSame('CLEAR', $weatherConditions[0]->getSysName());

        $weatherConditionsIcon = $weatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $weatherConditionsIcon);
        $this->assertSame('01d', $weatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/01d@4x.png', $weatherConditionsIcon->getImageUrl());

        $wind = $weatherLocation->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(9.26, $wind->getSpeed());
        $this->assertSame(360, $wind->getDirection());
        $this->assertSame(2.34, $wind->getGust());

        $rain = $weatherLocation->getRain();
        $this->assertInstanceOf(Rain::class, $rain);
        $this->assertSame(0.17, $rain->getLastOneHourVolume());
        $this->assertSame(0.81, $rain->getLastThreeHoursVolume());

        $snow = $weatherLocation->getSnow();
        $this->assertInstanceOf(Snow::class, $snow);
        $this->assertSame(0.14, $snow->getLastOneHourVolume());
        $this->assertSame(0.46, $snow->getLastThreeHoursVolume());

        $atmosphericPressure = $weatherLocation->getAtmosphericPressure();
        $this->assertInstanceOf(AtmosphericPressure::class, $atmosphericPressure);
        $this->assertSame(1013, $atmosphericPressure->getPressure());
        $this->assertSame(1013, $atmosphericPressure->getSeaLevelPressure());
        $this->assertSame(997, $atmosphericPressure->getGroundLevelPressure());

        $location = $weatherLocation->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(6930126, $location->getId());
        $this->assertSame('Chiado', $location->getName());
        $this->assertSame('PT', $location->getCountryCode());
        $this->assertSame(null, $location->getLocalNames());
        $this->assertSame(null, $location->getPopulation());
        $this->assertSame('2023-06-28 05:13:56', $location->getSunriseAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-06-28 20:05:18', $location->getSunsetAt()->format('Y-m-d H:i:s'));

        $coordinate = $location->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $timezone = $location->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame(null, $timezone->getIdentifier());
        $this->assertSame(3600, $timezone->getOffset());
    }

    private function assertGetForecastResponse(WeatherLocationList $weatherLocationList): void
    {
        $this->assertSame(1, $weatherLocationList->getNumResults());

        $list = $weatherLocationList->getList();
        $this->assertContainsOnlyInstancesOf(Weather::class, $list);

        $weather = $list[0];
        $this->assertSame(26.2, $weather->getTemperature());
        $this->assertSame(26.2, $weather->getTemperatureFeelsLike());
        $this->assertSame(25.64, $weather->getMinTemperature());
        $this->assertSame(26.2, $weather->getMaxTemperature());
        $this->assertSame(56, $weather->getHumidity());
        $this->assertSame(0, $weather->getCloudiness());
        $this->assertSame(10000, $weather->getVisibility());
        $this->assertSame(0, $weather->getPrecipitationProbability());
        $this->assertSame('2023-06-28 18:00:00', $weather->getDateTime()->format('Y-m-d H:i:s'));

        $weatherConditions = $weather->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $weatherConditions);
        $this->assertSame(800, $weatherConditions[0]->getId());
        $this->assertSame('Clear', $weatherConditions[0]->getName());
        $this->assertSame('clear sky', $weatherConditions[0]->getDescription());
        $this->assertSame('CLEAR', $weatherConditions[0]->getSysName());

        $weatherConditionsIcon = $weatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $weatherConditionsIcon);
        $this->assertSame('01d', $weatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/01d@4x.png', $weatherConditionsIcon->getImageUrl());

        $wind = $weather->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(8.88, $wind->getSpeed());
        $this->assertSame(340, $wind->getDirection());
        $this->assertSame(13.77, $wind->getGust());

        $rain = $weather->getRain();
        $this->assertSame(null, $rain);

        $snow = $weather->getSnow();
        $this->assertSame(null, $snow);

        $atmosphericPressure = $weather->getAtmosphericPressure();
        $this->assertInstanceOf(AtmosphericPressure::class, $atmosphericPressure);
        $this->assertSame(1013, $atmosphericPressure->getPressure());
        $this->assertSame(1013, $atmosphericPressure->getSeaLevelPressure());
        $this->assertSame(1013, $atmosphericPressure->getGroundLevelPressure());

        $location = $weatherLocationList->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(6930126, $location->getId());
        $this->assertSame('Chiado', $location->getName());
        $this->assertSame('PT', $location->getCountryCode());
        $this->assertSame(null, $location->getLocalNames());
        $this->assertSame(500000, $location->getPopulation());
        $this->assertSame('2023-06-28 05:13:56', $location->getSunriseAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-06-28 20:05:18', $location->getSunsetAt()->format('Y-m-d H:i:s'));

        $coordinate = $location->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $timezone = $location->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame(null, $timezone->getIdentifier());
        $this->assertSame(3600, $timezone->getOffset());
    }
}