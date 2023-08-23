<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProviderExternal;
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
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class WeatherEndpointTest extends AbstractTest
{
    public function testWeatherGetCurrent()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::WEATHER_CURRENT
            )
        );

        $response = $this->givenApi()->getWeather()->getCurrent(50, 50);
        $this->assertCurrentResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testWeatherGetCurrentWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->getWeather()->getCurrent($latitude, $longitude);
    }

    public function testWeatherGetForecast()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::WEATHER_FORECAST
            )
        );

        $response = $this->givenApi()->getWeather()->getForecast(50, 50, 1);
        $this->assertForecastResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testWeatherGetForecastWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->getWeather()->getForecast($latitude, $longitude, 10);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidNumResultsData')]
    public function testWeatherGetForecastWithInvalidNumResults(int $numResults, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->getWeather()->getForecast(50, 50, $numResults);
    }

    public function testWeatherMethodsWithExist()
    {
        $weatherEndpoint = $this->givenApi()->getWeather();

        $this->assertSame(true, method_exists($weatherEndpoint, 'withLanguage'));
        $this->assertSame(true, method_exists($weatherEndpoint, 'withUnitSystem'));
    }

    private function assertCurrentResponse(WeatherLocation $response): void
    {
        $this->assertInstanceOf(WeatherLocation::class, $response);

        $this->assertSame(27.12, $response->getTemperature());
        $this->assertSame(28.16, $response->getTemperatureFeelsLike());
        $this->assertSame(22.76, $response->getMinTemperature());
        $this->assertSame(29.9, $response->getMaxTemperature());
        $this->assertSame(59, $response->getHumidity());
        $this->assertSame(0, $response->getCloudiness());
        $this->assertSame(10000, $response->getVisibility());
        $this->assertSame(null, $response->getPrecipitationProbability());

        $weatherConditions = $response->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $weatherConditions);
        $this->assertSame(800, $weatherConditions[0]->getId());
        $this->assertSame('Clear', $weatherConditions[0]->getName());
        $this->assertSame('clear sky', $weatherConditions[0]->getDescription());
        $this->assertSame('CLEAR', $weatherConditions[0]->getSysName());

        $weatherConditionsIcon = $weatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $weatherConditionsIcon);
        $this->assertSame('01d', $weatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/01d@4x.png', $weatherConditionsIcon->getImageUrl());

        $wind = $response->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(9.26, $wind->getSpeed());
        $this->assertSame(360, $wind->getDirection());
        $this->assertSame(2.34, $wind->getGust());

        $rain = $response->getRain();
        $this->assertInstanceOf(Rain::class, $rain);
        $this->assertSame(0.17, $rain->getLastOneHourVolume());
        $this->assertSame(0.81, $rain->getLastThreeHoursVolume());

        $snow = $response->getSnow();
        $this->assertInstanceOf(Snow::class, $snow);
        $this->assertSame(0.14, $snow->getLastOneHourVolume());
        $this->assertSame(0.46, $snow->getLastThreeHoursVolume());

        $atmosphericPressure = $response->getAtmosphericPressure();
        $this->assertInstanceOf(AtmosphericPressure::class, $atmosphericPressure);
        $this->assertSame(1013, $atmosphericPressure->getPressure());
        $this->assertSame(1013, $atmosphericPressure->getSeaLevelPressure());
        $this->assertSame(997, $atmosphericPressure->getGroundLevelPressure());

        $dateTime = $response->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-06-28 10:45:33', $dateTime->format('Y-m-d H:i:s'));

        $location = $response->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(6930126, $location->getId());
        $this->assertSame('Chiado', $location->getName());
        $this->assertSame('PT', $location->getCountryCode());
        $this->assertSame(null, $location->getLocalNames());
        $this->assertSame(null, $location->getPopulation());

        $coordinate = $location->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $timezone = $location->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame(null, $timezone->getIdentifier());
        $this->assertSame(3600, $timezone->getOffset());

        $sunriseAt = $location->getSunriseAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $sunriseAt);
        $this->assertSame('2023-06-28 05:13:56', $sunriseAt->format('Y-m-d H:i:s'));

        $sunsetAt = $location->getSunsetAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $sunsetAt);
        $this->assertSame('2023-06-28 20:05:18', $sunsetAt->format('Y-m-d H:i:s'));
    }

    private function assertForecastResponse(WeatherLocationList $response): void
    {
        $this->assertInstanceOf(WeatherLocationList::class, $response);

        $this->assertSame(1, $response->getNumResults());

        $list = $response->getList();
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

        $dateTime = $weather->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-06-28 18:00:00', $dateTime->format('Y-m-d H:i:s'));

        $location = $response->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(6930126, $location->getId());
        $this->assertSame('Chiado', $location->getName());
        $this->assertSame('PT', $location->getCountryCode());
        $this->assertSame(null, $location->getLocalNames());
        $this->assertSame(500000, $location->getPopulation());

        $coordinate = $location->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $timezone = $location->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame(null, $timezone->getIdentifier());
        $this->assertSame(3600, $timezone->getOffset());

        $sunriseAt = $location->getSunriseAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $sunriseAt);
        $this->assertSame('2023-06-28 05:13:56', $sunriseAt->format('Y-m-d H:i:s'));

        $sunsetAt = $location->getSunsetAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $sunsetAt);
        $this->assertSame('2023-06-28 20:05:18', $sunsetAt->format('Y-m-d H:i:s'));
    }
}