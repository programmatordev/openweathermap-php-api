<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Icon;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteForecast;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MoonPhase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneCall;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Temperature;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Weather;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherAggregate;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherMoment;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Entity\WeatherCondition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class OneCallEndpointTest extends AbstractTest
{
    public function testOneCallGetWeather()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::ONE_CALL_WEATHER
            )
        );

        $response = $this->getApi()->getOneCall()->getWeather(38.7077507, -9.1365919);
        $this->assertWeatherResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testOneCallGetWeatherWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getOneCall()->getWeather($latitude, $longitude);
    }

    public function testOneCallGetHistoryMoment()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::ONE_CALL_HISTORY_MOMENT
            )
        );

        $response = $this->getApi()->getOneCall()->getHistoryMoment(
            38.7077507,
            -9.1365919,
            new \DateTimeImmutable('2023-01-01 00:00:00')
        );
        $this->assertHistoryMomentResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testOneCallGetHistoryMomentWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getOneCall()->getHistoryMoment(
            $latitude,
            $longitude,
            new \DateTimeImmutable('2023-01-01 00:00:00')
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidPastDateData')]
    public function testOneCallGetHistoryMomentWithInvalidPastDate(\DateTimeImmutable $date, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getOneCall()->getHistoryMoment(38.7077507, -9.1365919, $date);
    }

    public function testOneCallGetHistoryAggregate()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::ONE_CALL_HISTORY_AGGREGATE
            )
        );

        $response = $this->getApi()->getOneCall()->getHistoryAggregate(
            38.7077507,
            -9.1365919,
            new \DateTimeImmutable('2023-01-01')
        );
        $this->assertHistoryAggregateResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testOneCallGetHistoryAggregateWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getOneCall()->getHistoryAggregate(
            $latitude,
            $longitude,
            new \DateTimeImmutable('2023-01-01')
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidPastDateData')]
    public function testOneCallGetHistoryAggregateWithInvalidPastDate(\DateTimeImmutable $date, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getOneCall()->getHistoryAggregate(38.7077507, -9.1365919, $date);
    }

    public function testOneCallMethodsWithExist()
    {
        $weatherEndpoint = $this->getApi()->getWeather();

        $this->assertSame(true, method_exists($weatherEndpoint, 'withLanguage'));
        $this->assertSame(true, method_exists($weatherEndpoint, 'withUnitSystem'));
    }

    private function assertWeatherResponse(OneCall $response): void
    {
        $this->assertInstanceOf(OneCall::class, $response);

        // Coordinate
        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        // Timezone
        $timezone = $response->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame('Europe/Lisbon', $timezone->getIdentifier());
        $this->assertSame(3600, $timezone->getOffset());

        // Current
        $current = $response->getCurrent();
        $this->assertInstanceOf(Weather::class, $current);
        $this->assertSame(null, $current->getMoonriseAt());
        $this->assertSame(null, $current->getMoonsetAt());
        $this->assertSame(null, $current->getMoonPhase());
        $this->assertSame(25.1, $current->getTemperature());
        $this->assertSame(25.21, $current->getTemperatureFeelsLike());
        $this->assertSame(null, $current->getDescription());
        $this->assertSame(1017, $current->getAtmosphericPressure());
        $this->assertSame(59, $current->getHumidity());
        $this->assertSame(16.53, $current->getDewPoint());
        $this->assertSame(9.78, $current->getUltraVioletIndex());
        $this->assertSame(20, $current->getCloudiness());
        $this->assertSame(10000, $current->getVisibility());
        $this->assertSame(null, $current->getPrecipitationProbability());
        $this->assertSame(null, $current->getRain());
        $this->assertSame(null, $current->getSnow());

        $currentWind = $current->getWind();
        $this->assertInstanceOf(Wind::class, $currentWind);
        $this->assertSame(7.2, $currentWind->getSpeed());
        $this->assertSame(10, $currentWind->getDirection());
        $this->assertSame(null, $currentWind->getGust());

        $currentWeatherConditions = $current->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $currentWeatherConditions);
        $this->assertSame(801, $currentWeatherConditions[0]->getId());
        $this->assertSame('Clouds', $currentWeatherConditions[0]->getName());
        $this->assertSame('few clouds', $currentWeatherConditions[0]->getDescription());
        $this->assertSame('CLOUDS', $currentWeatherConditions[0]->getSysName());

        $currentWeatherConditionsIcon = $currentWeatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $currentWeatherConditionsIcon);
        $this->assertSame('02d', $currentWeatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/02d@4x.png', $currentWeatherConditionsIcon->getImageUrl());

        $currentDateTime = $current->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $currentDateTime);
        $this->assertSame('2023-07-03 11:35:39', $currentDateTime->format('Y-m-d H:i:s'));

        $currentSunriseAt = $current->getSunriseAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $currentSunriseAt);
        $this->assertSame('2023-07-03 05:16:08', $currentSunriseAt->format('Y-m-d H:i:s'));

        $currentSunsetAt = $current->getSunsetAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $currentSunsetAt);
        $this->assertSame('2023-07-03 20:04:57', $currentSunsetAt->format('Y-m-d H:i:s'));

        // MinutelyForecast
        $minutelyForecast = $response->getMinutelyForecast();
        $this->assertContainsOnlyInstancesOf(MinuteForecast::class, $minutelyForecast);
        $this->assertSame(0.0, $minutelyForecast[0]->getPrecipitation());

        $minutelyForecastDateTime = $minutelyForecast[0]->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $minutelyForecastDateTime);
        $this->assertSame('2023-07-03 11:36:00', $minutelyForecastDateTime->format('Y-m-d H:i:s'));

        // HourlyForecast
        $hourlyForecast = $response->getHourlyForecast();
        $this->assertContainsOnlyInstancesOf(Weather::class, $hourlyForecast);
        $this->assertSame(null, $hourlyForecast[0]->getSunriseAt());
        $this->assertSame(null, $hourlyForecast[0]->getSunsetAt());
        $this->assertSame(null, $hourlyForecast[0]->getMoonriseAt());
        $this->assertSame(null, $hourlyForecast[0]->getMoonsetAt());
        $this->assertSame(null, $hourlyForecast[0]->getMoonPhase());
        $this->assertSame(25.38, $hourlyForecast[0]->getTemperature());
        $this->assertSame(25.44, $hourlyForecast[0]->getTemperatureFeelsLike());
        $this->assertSame(null, $hourlyForecast[0]->getDescription());
        $this->assertSame(1017, $hourlyForecast[0]->getAtmosphericPressure());
        $this->assertSame(56, $hourlyForecast[0]->getHumidity());
        $this->assertSame(15.97, $hourlyForecast[0]->getDewPoint());
        $this->assertSame(8.42, $hourlyForecast[0]->getUltraVioletIndex());
        $this->assertSame(16, $hourlyForecast[0]->getCloudiness());
        $this->assertSame(10000, $hourlyForecast[0]->getVisibility());
        $this->assertSame(0, $hourlyForecast[0]->getPrecipitationProbability());
        $this->assertSame(null, $hourlyForecast[0]->getRain());
        $this->assertSame(null, $hourlyForecast[0]->getSnow());

        $hourlyForecastWind = $hourlyForecast[0]->getWind();
        $this->assertInstanceOf(Wind::class, $hourlyForecastWind);
        $this->assertSame(4.94, $hourlyForecastWind->getSpeed());
        $this->assertSame(327, $hourlyForecastWind->getDirection());
        $this->assertSame(6.14, $hourlyForecastWind->getGust());

        $hourlyForecastWeatherConditions = $hourlyForecast[0]->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $hourlyForecastWeatherConditions);
        $this->assertSame(801, $hourlyForecastWeatherConditions[0]->getId());
        $this->assertSame('Clouds', $hourlyForecastWeatherConditions[0]->getName());
        $this->assertSame('few clouds', $hourlyForecastWeatherConditions[0]->getDescription());
        $this->assertSame('CLOUDS', $hourlyForecastWeatherConditions[0]->getSysName());

        $hourlyForecastWeatherConditionsIcon = $hourlyForecastWeatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $hourlyForecastWeatherConditionsIcon);
        $this->assertSame('02d', $hourlyForecastWeatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/02d@4x.png', $hourlyForecastWeatherConditionsIcon->getImageUrl());

        $hourlyForecastDateTIme = $hourlyForecast[0]->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $hourlyForecastDateTIme);
        $this->assertSame('2023-07-03 11:00:00', $hourlyForecastDateTIme->format('Y-m-d H:i:s'));

        // DailyForecast
        $dailyForecast = $response->getDailyForecast();
        $this->assertContainsOnlyInstancesOf(Weather::class, $dailyForecast);
        $this->assertSame('Expect a day of partly cloudy with clear spells', $dailyForecast[0]->getDescription());
        $this->assertSame(1017, $dailyForecast[0]->getAtmosphericPressure());
        $this->assertSame(59, $dailyForecast[0]->getHumidity());
        $this->assertSame(16.53, $dailyForecast[0]->getDewPoint());
        $this->assertSame(9.92, $dailyForecast[0]->getUltraVioletIndex());
        $this->assertSame(20, $dailyForecast[0]->getCloudiness());
        $this->assertSame(null, $dailyForecast[0]->getVisibility());
        $this->assertSame(0, $dailyForecast[0]->getPrecipitationProbability());
        $this->assertSame(null, $dailyForecast[0]->getRain());
        $this->assertSame(null, $dailyForecast[0]->getSnow());

        $dailyForecastTemperature = $dailyForecast[0]->getTemperature();
        $this->assertInstanceOf(Temperature::class, $dailyForecastTemperature);
        $this->assertSame(18.39, $dailyForecastTemperature->getMorning());
        $this->assertSame(25.1, $dailyForecastTemperature->getDay());
        $this->assertSame(24.67, $dailyForecastTemperature->getEvening());
        $this->assertSame(19.41, $dailyForecastTemperature->getNight());
        $this->assertSame(18.28, $dailyForecastTemperature->getMin());
        $this->assertSame(26.88, $dailyForecastTemperature->getMax());

        $dailyForecastTemperatureFeelsLike = $dailyForecast[0]->getTemperatureFeelsLike();
        $this->assertInstanceOf(Temperature::class, $dailyForecastTemperature);
        $this->assertSame(18.5, $dailyForecastTemperatureFeelsLike->getMorning());
        $this->assertSame(25.21, $dailyForecastTemperatureFeelsLike->getDay());
        $this->assertSame(24.52, $dailyForecastTemperatureFeelsLike->getEvening());
        $this->assertSame(19.5, $dailyForecastTemperatureFeelsLike->getNight());
        $this->assertSame(null, $dailyForecastTemperatureFeelsLike->getMin());
        $this->assertSame(null, $dailyForecastTemperatureFeelsLike->getMax());

        $dailyForecastWind = $dailyForecast[0]->getWind();
        $this->assertInstanceOf(Wind::class, $dailyForecastWind);
        $this->assertSame(8.8, $dailyForecastWind->getSpeed());
        $this->assertSame(347, $dailyForecastWind->getDirection());
        $this->assertSame(13.04, $dailyForecastWind->getGust());

        $dailyForecastWeatherConditions = $dailyForecast[0]->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $dailyForecastWeatherConditions);
        $this->assertSame(801, $dailyForecastWeatherConditions[0]->getId());
        $this->assertSame('Clouds', $dailyForecastWeatherConditions[0]->getName());
        $this->assertSame('few clouds', $dailyForecastWeatherConditions[0]->getDescription());
        $this->assertSame('CLOUDS', $dailyForecastWeatherConditions[0]->getSysName());

        $dailyForecastWeatherConditionsIcon = $dailyForecastWeatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $dailyForecastWeatherConditionsIcon);
        $this->assertSame('02d', $dailyForecastWeatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/02d@4x.png', $dailyForecastWeatherConditionsIcon->getImageUrl());

        $dailyForecastDateTime = $dailyForecast[0]->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dailyForecastDateTime);
        $this->assertSame('2023-07-03 12:00:00', $dailyForecastDateTime->format('Y-m-d H:i:s'));

        $dailyForecastSunriseAt = $dailyForecast[0]->getSunriseAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dailyForecastSunriseAt);
        $this->assertSame('2023-07-03 05:16:08', $dailyForecastSunriseAt->format('Y-m-d H:i:s'));

        $dailyForecastSunsetAt = $dailyForecast[0]->getSunsetAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dailyForecastSunsetAt);
        $this->assertSame('2023-07-03 20:04:57', $dailyForecastSunsetAt->format('Y-m-d H:i:s'));

        $dailyForecastMoonriseAt = $dailyForecast[0]->getMoonriseAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dailyForecastMoonriseAt);
        $this->assertSame('2023-07-03 20:45:00', $dailyForecastMoonriseAt->format('Y-m-d H:i:s'));

        $dailyForecastMoonsetAt = $dailyForecast[0]->getMoonsetAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dailyForecastMoonsetAt);
        $this->assertSame('2023-07-03 04:44:00', $dailyForecastMoonsetAt->format('Y-m-d H:i:s'));

        $dailyForecastMoonPhase = $dailyForecast[0]->getMoonPhase();
        $this->assertInstanceOf(MoonPhase::class, $dailyForecastMoonPhase);
        $this->assertSame(0.5, $dailyForecastMoonPhase->getValue());
        $this->assertSame('Full moon', $dailyForecastMoonPhase->getName());
        $this->assertSame('FULL_MOON', $dailyForecastMoonPhase->getSysName());

        // Alerts
        $alerts = $response->getAlerts();
        $this->assertContainsOnlyInstancesOf(Alert::class, $alerts);
        $this->assertSame('NWS Portland (Northwest Oregon and Southwest Washington)', $alerts[0]->getSenderName());
        $this->assertSame('Heat Advisory', $alerts[0]->getEventName());
        $this->assertStringStartsWith('...HEAT ADVISORY REMAINS', $alerts[0]->getDescription());
        $this->assertIsArray($alerts[0]->getTags());

        $alertsStartsAt = $alerts[0]->getStartsAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $alertsStartsAt);
        $this->assertSame('2023-07-04 17:00:00', $alertsStartsAt->format('Y-m-d H:i:s'));

        $alertsEndsAt = $alerts[0]->getEndsAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $alertsEndsAt);
        $this->assertSame('2023-07-06 06:00:00', $alertsEndsAt->format('Y-m-d H:i:s'));
    }

    private function assertHistoryMomentResponse(WeatherMoment $response): void
    {
        $this->assertInstanceOf(WeatherMoment::class, $response);

        $this->assertSame(null, $response->getMoonriseAt());
        $this->assertSame(null, $response->getMoonsetAt());
        $this->assertSame(null, $response->getMoonPhase());
        $this->assertSame(17.48, $response->getTemperature());
        $this->assertSame(17.16, $response->getTemperatureFeelsLike());
        $this->assertSame(null, $response->getDescription());
        $this->assertSame(1019, $response->getAtmosphericPressure());
        $this->assertSame(72, $response->getHumidity());
        $this->assertSame(12.38, $response->getDewPoint());
        $this->assertSame(null, $response->getUltraVioletIndex());
        $this->assertSame(20, $response->getCloudiness());
        $this->assertSame(9999, $response->getVisibility());
        $this->assertSame(null, $response->getPrecipitationProbability());
        $this->assertSame(null, $response->getRain());
        $this->assertSame(null, $response->getSnow());

        $wind = $response->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(16.54, $wind->getSpeed());
        $this->assertSame(337, $wind->getDirection());
        $this->assertSame(16.54, $wind->getGust());

        $weatherConditions = $response->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $weatherConditions);
        $this->assertSame(801, $weatherConditions[0]->getId());
        $this->assertSame('Clouds', $weatherConditions[0]->getName());
        $this->assertSame('few clouds', $weatherConditions[0]->getDescription());
        $this->assertSame('CLOUDS', $weatherConditions[0]->getSysName());

        $weatherConditionsIcon = $weatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $weatherConditionsIcon);
        $this->assertSame('02n', $weatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/02n@4x.png', $weatherConditionsIcon->getImageUrl());

        $dateTime = $response->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));

        $sunriseAt = $response->getSunriseAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $sunriseAt);
        $this->assertSame('2023-01-01 07:54:31', $sunriseAt->format('Y-m-d H:i:s'));

        $sunsetAt = $response->getSunsetAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $sunsetAt);
        $this->assertSame('2023-01-01 17:25:02', $sunsetAt->format('Y-m-d H:i:s'));

        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $timezone = $response->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame('Europe/Lisbon', $timezone->getIdentifier());
        $this->assertSame(0, $timezone->getOffset());
    }

    private function assertHistoryAggregateResponse(WeatherAggregate $response): void
    {
        $this->assertInstanceOf(WeatherAggregate::class, $response);

        $this->assertSame(75, $response->getCloudiness());
        $this->assertSame(71, $response->getHumidity());
        $this->assertSame(2.53, $response->getPrecipitation());
        $this->assertSame(1017, $response->getAtmosphericPressure());

        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7077507, $coordinate->getLatitude());
        $this->assertSame(-9.1365919, $coordinate->getLongitude());

        $timezone = $response->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame(null, $timezone->getIdentifier());
        $this->assertSame(0, $timezone->getOffset());

        $dateTime = $response->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));

        $temperature = $response->getTemperature();
        $this->assertInstanceOf(Temperature::class, $temperature);
        $this->assertSame(17.23, $temperature->getMorning());
        $this->assertSame(18.26, $temperature->getDay());
        $this->assertSame(13.9, $temperature->getEvening());
        $this->assertSame(17.39, $temperature->getNight());
        $this->assertSame(12.52, $temperature->getMin());
        $this->assertSame(18.29, $temperature->getMax());

        $wind = $response->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(26.38, $wind->getSpeed());
        $this->assertSame(225, $wind->getDirection());
        $this->assertSame(null, $wind->getGust());
    }
}