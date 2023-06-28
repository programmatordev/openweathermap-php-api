<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Icon;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Rain;
use ProgrammatorDev\OpenWeatherMap\Entity\Snow;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\AtmosphericPressure;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\CurrentWeather;
use ProgrammatorDev\OpenWeatherMap\Entity\WeatherCondition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class WeatherTest extends AbstractTest
{
    public function testWeatherGetCurrent()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::WEATHER_CURRENT
            )
        );

        $response = $this->getApi()->getWeather()->getCurrent(38.7077507, -9.1365919);
        $this->assertCurrentResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testWeatherGetCurrentWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getWeather()->getCurrent($latitude, $longitude);
    }

    public function testWeatherGetCurrentByLocationName()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::GEOCODING_DIRECT
            )
        );
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::WEATHER_CURRENT
            )
        );

        $response = $this->getApi()->getWeather()->getCurrentByLocationName('lisbon, pt');
        $this->assertCurrentResponse($response);
    }

    public function testWeatherWithMethodsExist()
    {
        $weatherEndpoint = $this->getApi()->getWeather();

        $this->assertSame(true, method_exists($weatherEndpoint, 'withLanguage'));
        $this->assertSame(true, method_exists($weatherEndpoint, 'withMeasurementSystem'));
    }

    private function assertCurrentResponse(CurrentWeather $response): void
    {
        $this->assertInstanceOf(CurrentWeather::class, $response);

        $this->assertSame(27.12, $response->getTemperature());
        $this->assertSame(28.16, $response->getTemperatureFeelsLike());
        $this->assertSame(22.76, $response->getMinTemperature());
        $this->assertSame(29.9, $response->getMaxTemperature());
        $this->assertSame(59, $response->getHumidity());
        $this->assertSame(0, $response->getCloudiness());
        $this->assertSame(10000, $response->getVisibility());

        $weatherConditions = $response->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $weatherConditions);

        $weatherCondition = $weatherConditions[0];
        $this->assertSame(800, $weatherCondition->getId());
        $this->assertSame('Clear', $weatherCondition->getGroup());
        $this->assertSame('Clear', $weatherCondition->getMain());
        $this->assertSame('clear sky', $weatherCondition->getDescription());

        $icon = $weatherCondition->getIcon();
        $this->assertInstanceOf(Icon::class, $icon);
        $this->assertSame('01d', $icon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/01d@4x.png', $icon->getImageUrl());

        $wind = $response->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(9.26, $wind->getSpeed());
        $this->assertSame(360, $wind->getDirection());
        $this->assertSame(2.34, $wind->getGust());

        $rain = $response->getRain();
        $this->assertInstanceOf(Rain::class, $rain);
        $this->assertSame(0.17, $rain->getLastOneHourVolume());
        $this->assertSame(0.81, $rain->getLastThreeHoursVolume());
        $this->assertSame(null, $rain->getPrecipitationProbability());

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