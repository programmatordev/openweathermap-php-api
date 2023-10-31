<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCallEndpoint;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Icon;
use ProgrammatorDev\OpenWeatherMap\Entity\MoonPhase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteForecast;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneCall;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Weather;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherAggregate;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Temperature;
use ProgrammatorDev\OpenWeatherMap\Entity\Timezone;
use ProgrammatorDev\OpenWeatherMap\Entity\WeatherCondition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointInvalidResponseTrait;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointSuccessResponseTrait;

class OneCallEndpointTest extends AbstractTest
{
    use TestEndpointSuccessResponseTrait;
    use TestEndpointInvalidResponseTrait;

    public static function provideEndpointSuccessResponseData(): \Generator
    {
        yield 'get weather' => [
            MockResponse::ONE_CALL_WEATHER,
            'oneCall',
            'getWeather',
            [50, 50],
            'assertGetWeatherResponse'
        ];
        yield 'get history moment' => [
            MockResponse::ONE_CALL_HISTORY_MOMENT,
            'oneCall',
            'getHistoryMoment',
            [50, 50, new \DateTime('yesterday')],
            'assertGetHistoryMomentResponse'
        ];
        yield 'get history aggregate' => [
            MockResponse::ONE_CALL_HISTORY_AGGREGATE,
            'oneCall',
            'getHistoryAggregate',
            [50, 50, new \DateTime('yesterday')],
            'assertGetHistoryAggregateResponse'
        ];
    }

    public static function provideEndpointInvalidResponseData(): \Generator
    {
        yield 'get weather, latitude lower than -90' => ['oneCall', 'getWeather', [-91, 50]];
        yield 'get weather, latitude greater than 90' => ['oneCall', 'getWeather', [91, 50]];
        yield 'get weather, longitude lower than -180' => ['oneCall', 'getWeather', [50, -181]];
        yield 'get weather, longitude greater than 180' => ['oneCall', 'getWeather', [50, 181]];

        yield 'get history moment, latitude lower than -90' => ['oneCall', 'getHistoryMoment', [-91, 50, new \DateTime('yesterday')]];
        yield 'get history moment, latitude greater than 90' => ['oneCall', 'getHistoryMoment', [91, 50, new \DateTime('yesterday')]];
        yield 'get history moment, longitude lower than -180' => ['oneCall', 'getHistoryMoment', [50, -181, new \DateTime('yesterday')]];
        yield 'get history moment, longitude greater than 180' => ['oneCall', 'getHistoryMoment', [50, 181, new \DateTime('yesterday')]];
        yield 'get history moment, invalid past date' => ['oneCall', 'getHistoryMoment', [50, 50, new \DateTime('tomorrow')]];

        yield 'get history aggregate, latitude lower than -90' => ['oneCall', 'getHistoryAggregate', [-91, 50, new \DateTime('yesterday')]];
        yield 'get history aggregate, latitude greater than 90' => ['oneCall', 'getHistoryAggregate', [91, 50, new \DateTime('yesterday')]];
        yield 'get history aggregate, longitude lower than -180' => ['oneCall', 'getHistoryAggregate', [50, -181, new \DateTime('yesterday')]];
        yield 'get history aggregate, longitude greater than 180' => ['oneCall', 'getHistoryAggregate', [50, 181, new \DateTime('yesterday')]];
        yield 'get history aggregate, invalid past date' => ['oneCall', 'getHistoryAggregate', [50, 50, new \DateTime('tomorrow')]];
    }

    public function testOneCallMethodsExist()
    {
        $this->assertSame(true, method_exists(OneCallEndpoint::class, 'withUnitSystem'));
        $this->assertSame(true, method_exists(OneCallEndpoint::class, 'withLanguage'));
        $this->assertSame(true, method_exists(OneCallEndpoint::class, 'withCacheTtl'));
    }

    private function assertGetWeatherResponse(OneCall $oneCall): void
    {
        // Coordinate
        $coordinate = $oneCall->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        // Timezone
        $timezone = $oneCall->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame('Europe/Lisbon', $timezone->getIdentifier());
        $this->assertSame(3600, $timezone->getOffset());

        // Current
        $current = $oneCall->getCurrent();
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
        $this->assertSame('2023-07-03 11:35:39', $current->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-07-03 05:16:08', $current->getSunriseAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-07-03 20:04:57', $current->getSunsetAt()->format('Y-m-d H:i:s'));

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

        // MinutelyForecast
        $minutelyForecast = $oneCall->getMinutelyForecast();
        $this->assertContainsOnlyInstancesOf(MinuteForecast::class, $minutelyForecast);
        $this->assertSame(0.0, $minutelyForecast[0]->getPrecipitation());
        $this->assertSame('2023-07-03 11:36:00', $minutelyForecast[0]->getDateTime()->format('Y-m-d H:i:s'));

        // HourlyForecast
        $hourlyForecast = $oneCall->getHourlyForecast();
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
        $this->assertSame('2023-07-03 11:00:00', $hourlyForecast[0]->getDateTime()->format('Y-m-d H:i:s'));

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

        // DailyForecast
        $dailyForecast = $oneCall->getDailyForecast();
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
        $this->assertSame('2023-07-03 12:00:00', $dailyForecast[0]->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-07-03 05:16:08', $dailyForecast[0]->getSunriseAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-07-03 20:04:57', $dailyForecast[0]->getSunsetAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-07-03 20:45:00', $dailyForecast[0]->getMoonriseAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-07-03 04:44:00', $dailyForecast[0]->getMoonsetAt()->format('Y-m-d H:i:s'));

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

        $dailyForecastMoonPhase = $dailyForecast[0]->getMoonPhase();
        $this->assertInstanceOf(MoonPhase::class, $dailyForecastMoonPhase);
        $this->assertSame(0.5, $dailyForecastMoonPhase->getValue());
        $this->assertSame('Full moon', $dailyForecastMoonPhase->getName());
        $this->assertSame('FULL_MOON', $dailyForecastMoonPhase->getSysName());

        // Alerts
        $alerts = $oneCall->getAlerts();
        $this->assertContainsOnlyInstancesOf(Alert::class, $alerts);
        $this->assertSame('NWS Portland (Northwest Oregon and Southwest Washington)', $alerts[0]->getSenderName());
        $this->assertSame('Heat Advisory', $alerts[0]->getEventName());
        $this->assertStringStartsWith('...HEAT ADVISORY REMAINS', $alerts[0]->getDescription());
        $this->assertIsArray($alerts[0]->getTags());
        $this->assertSame('2023-07-04 17:00:00', $alerts[0]->getStartsAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-07-06 06:00:00', $alerts[0]->getEndsAt()->format('Y-m-d H:i:s'));
    }

    private function assertGetHistoryMomentResponse(WeatherLocation $weatherLocation): void
    {
        $this->assertInstanceOf(WeatherLocation::class, $weatherLocation);

        $this->assertSame(null, $weatherLocation->getMoonriseAt());
        $this->assertSame(null, $weatherLocation->getMoonsetAt());
        $this->assertSame(null, $weatherLocation->getMoonPhase());
        $this->assertSame(17.48, $weatherLocation->getTemperature());
        $this->assertSame(17.16, $weatherLocation->getTemperatureFeelsLike());
        $this->assertSame(null, $weatherLocation->getDescription());
        $this->assertSame(1019, $weatherLocation->getAtmosphericPressure());
        $this->assertSame(72, $weatherLocation->getHumidity());
        $this->assertSame(12.38, $weatherLocation->getDewPoint());
        $this->assertSame(null, $weatherLocation->getUltraVioletIndex());
        $this->assertSame(20, $weatherLocation->getCloudiness());
        $this->assertSame(9999, $weatherLocation->getVisibility());
        $this->assertSame(null, $weatherLocation->getPrecipitationProbability());
        $this->assertSame(null, $weatherLocation->getRain());
        $this->assertSame(null, $weatherLocation->getSnow());
        $this->assertSame('2023-01-01 00:00:00', $weatherLocation->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-01-01 07:54:31', $weatherLocation->getSunriseAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2023-01-01 17:25:02', $weatherLocation->getSunsetAt()->format('Y-m-d H:i:s'));

        $wind = $weatherLocation->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(16.54, $wind->getSpeed());
        $this->assertSame(337, $wind->getDirection());
        $this->assertSame(16.54, $wind->getGust());

        $weatherConditions = $weatherLocation->getWeatherConditions();
        $this->assertContainsOnlyInstancesOf(WeatherCondition::class, $weatherConditions);
        $this->assertSame(801, $weatherConditions[0]->getId());
        $this->assertSame('Clouds', $weatherConditions[0]->getName());
        $this->assertSame('few clouds', $weatherConditions[0]->getDescription());
        $this->assertSame('CLOUDS', $weatherConditions[0]->getSysName());

        $weatherConditionsIcon = $weatherConditions[0]->getIcon();
        $this->assertInstanceOf(Icon::class, $weatherConditionsIcon);
        $this->assertSame('02n', $weatherConditionsIcon->getId());
        $this->assertSame('https://openweathermap.org/img/wn/02n@4x.png', $weatherConditionsIcon->getImageUrl());

        $coordinate = $weatherLocation->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $timezone = $weatherLocation->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame('Europe/Lisbon', $timezone->getIdentifier());
        $this->assertSame(0, $timezone->getOffset());
    }

    private function assertGetHistoryAggregateResponse(WeatherAggregate $weatherAggregate): void
    {
        $this->assertInstanceOf(WeatherAggregate::class, $weatherAggregate);

        $this->assertSame(75, $weatherAggregate->getCloudiness());
        $this->assertSame(71, $weatherAggregate->getHumidity());
        $this->assertSame(2.53, $weatherAggregate->getPrecipitation());
        $this->assertSame(1017, $weatherAggregate->getAtmosphericPressure());
        $this->assertSame('2023-01-01 00:00:00', $weatherAggregate->getDateTime()->format('Y-m-d H:i:s'));

        $coordinate = $weatherAggregate->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7077507, $coordinate->getLatitude());
        $this->assertSame(-9.1365919, $coordinate->getLongitude());

        $timezone = $weatherAggregate->getTimezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertSame(null, $timezone->getIdentifier());
        $this->assertSame(0, $timezone->getOffset());

        $temperature = $weatherAggregate->getTemperature();
        $this->assertInstanceOf(Temperature::class, $temperature);
        $this->assertSame(17.23, $temperature->getMorning());
        $this->assertSame(18.26, $temperature->getDay());
        $this->assertSame(13.9, $temperature->getEvening());
        $this->assertSame(17.39, $temperature->getNight());
        $this->assertSame(12.52, $temperature->getMin());
        $this->assertSame(18.29, $temperature->getMax());

        $wind = $weatherAggregate->getWind();
        $this->assertInstanceOf(Wind::class, $wind);
        $this->assertSame(26.38, $wind->getSpeed());
        $this->assertSame(225, $wind->getDirection());
        $this->assertSame(null, $wind->getGust());
    }
}