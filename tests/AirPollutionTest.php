<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirQuality;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Component;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\CurrentAirPollution;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class AirPollutionTest extends AbstractTest
{
    public function testAirPollutionGetCurrent()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::AIR_POLLUTION_CURRENT
            )
        );

        $response = $this->getApi()->getAirPollution()->getCurrent(38.7077507, -9.1365919);
        $this->assertCurrentResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testAirPollutionGetCurrentWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getAirPollution()->getCurrent($latitude, $longitude);
    }

    public function testAirPollutionGetCurrentByLocationName()
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
                body: MockResponse::AIR_POLLUTION_CURRENT
            )
        );

        $response = $this->getApi()->getAirPollution()->getCurrentByLocationName('lisbon, pt');
        $this->assertCurrentResponse($response);
    }

    public function testAirPollutionGetForecast()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::AIR_POLLUTION_FORECAST
            )
        );

        $response = $this->getApi()->getAirPollution()->getForecast(38.7077507, -9.1365919);
        $this->assertForecastResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testAirPollutionGetForecastWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getAirPollution()->getForecast($latitude, $longitude);
    }

    public function testAirPollutionGetForecastByLocationName()
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
                body: MockResponse::AIR_POLLUTION_FORECAST
            )
        );

        $response = $this->getApi()->getAirPollution()->getForecastByLocationName('lisbon, pt');
        $this->assertForecastResponse($response);
    }

    public function testAirPollutionGetHistory()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::AIR_POLLUTION_HISTORY
            )
        );

        $utcTimezone = new \DateTimeZone('UTC');

        $response = $this->getApi()->getAirPollution()->getHistory(
            38.7077507,
            -9.1365919,
            new \DateTimeImmutable('-5 days', $utcTimezone),
            new \DateTimeImmutable('-4 days', $utcTimezone)
        );
        $this->assertHistoryResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testAirPollutionGetHistoryWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);

        $startDate = new \DateTimeImmutable('-5 days');
        $endDate = new \DateTimeImmutable('-4 days');

        $this->getApi()->getAirPollution()->getHistory($latitude, $longitude, $startDate, $endDate);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidPastDateData')]
    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidDateRangeData')]
    public function testAirPollutionGetHistoryWithInvalidDates(
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->getApi()->getAirPollution()->getHistory(38.7077507, -9.1365919, $startDate, $endDate);
    }

    public function testAirPollutionGetHistoryByLocationName()
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
                body: MockResponse::AIR_POLLUTION_HISTORY
            )
        );

        $utcTimezone = new \DateTimeZone('UTC');

        $response = $this->getApi()->getAirPollution()->getHistoryByLocationName(
            'lisbon, pt',
            new \DateTimeImmutable('-5 days', $utcTimezone),
            new \DateTimeImmutable('-4 days', $utcTimezone)
        );
        $this->assertHistoryResponse($response);
    }

    private function assertCurrentResponse(CurrentAirPollution $response): void
    {
        $this->assertInstanceOf(CurrentAirPollution::class, $response);

        $location = $response->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(null, $location->getId());
        $this->assertSame(null, $location->getName());
        $this->assertSame(null, $location->getState());
        $this->assertSame(null, $location->getCountryCode());
        $this->assertSame(null, $location->getLocalNames());
        $this->assertSame(null, $location->getPopulation());
        $this->assertSame(null, $location->getTimezone());
        $this->assertSame(null, $location->getSunriseAt());
        $this->assertSame(null, $location->getSunsetAt());

        $coordinate = $location->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $dateTime = $response->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-06-23 17:21:57', $dateTime->format('Y-m-d H:i:s'));

        $airQuality = $response->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(3, $airQuality->getIndex());
        $this->assertSame('Moderate', $airQuality->getQualitativeName());

        $components = $response->getComponents();
        $this->assertInstanceOf(Component::class, $components);
        $this->assertSame(196.93, $components->getCarbonMonoxide());
        $this->assertSame(0.65, $components->getNitrogenMonoxide());
        $this->assertSame(3.98, $components->getNitrogenDioxide());
        $this->assertSame(107.29, $components->getOzone());
        $this->assertSame(1.46, $components->getSulphurDioxide());
        $this->assertSame(8.58, $components->getFineParticulateMatter());
        $this->assertSame(13.5, $components->getCoarseParticulateMatter());
        $this->assertSame(2.03, $components->getAmmonia());
    }

    private function assertForecastResponse(AirPollutionList $response): void
    {
        $this->assertInstanceOf(AirPollutionList::class, $response);

        $location = $response->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(null, $location->getId());
        $this->assertSame(null, $location->getName());
        $this->assertSame(null, $location->getState());
        $this->assertSame(null, $location->getCountryCode());
        $this->assertSame(null, $location->getLocalNames());
        $this->assertSame(null, $location->getPopulation());
        $this->assertSame(null, $location->getTimezone());
        $this->assertSame(null, $location->getSunriseAt());
        $this->assertSame(null, $location->getSunsetAt());

        $coordinate = $location->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $list = $response->getList();
        $this->assertContainsOnlyInstancesOf(AirPollution::class, $list);

        $dateTime = $list[0]->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-06-23 17:00:00', $dateTime->format('Y-m-d H:i:s'));

        $airQuality = $list[0]->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(3, $airQuality->getIndex());
        $this->assertSame('Moderate', $airQuality->getQualitativeName());

        $components = $list[0]->getComponents();
        $this->assertInstanceOf(Component::class, $components);
        $this->assertSame(196.93, $components->getCarbonMonoxide());
        $this->assertSame(0.65, $components->getNitrogenMonoxide());
        $this->assertSame(3.98, $components->getNitrogenDioxide());
        $this->assertSame(107.29, $components->getOzone());
        $this->assertSame(1.46, $components->getSulphurDioxide());
        $this->assertSame(8.58, $components->getFineParticulateMatter());
        $this->assertSame(13.5, $components->getCoarseParticulateMatter());
        $this->assertSame(2.03, $components->getAmmonia());
    }

    private function assertHistoryResponse(AirPollutionList $response): void
    {
        $this->assertInstanceOf(AirPollutionList::class, $response);

        $location = $response->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(null, $location->getId());
        $this->assertSame(null, $location->getName());
        $this->assertSame(null, $location->getState());
        $this->assertSame(null, $location->getCountryCode());
        $this->assertSame(null, $location->getLocalNames());
        $this->assertSame(null, $location->getPopulation());
        $this->assertSame(null, $location->getTimezone());
        $this->assertSame(null, $location->getSunriseAt());
        $this->assertSame(null, $location->getSunsetAt());

        $coordinate = $location->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $list = $response->getList();
        $this->assertContainsOnlyInstancesOf(AirPollution::class, $list);

        $dateTime = $list[0]->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-06-18 18:00:00', $dateTime->format('Y-m-d H:i:s'));

        $airQuality = $list[0]->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(2, $airQuality->getIndex());
        $this->assertSame('Fair', $airQuality->getQualitativeName());

        $components = $list[0]->getComponents();
        $this->assertInstanceOf(Component::class, $components);
        $this->assertSame(220.3, $components->getCarbonMonoxide());
        $this->assertSame(0.12, $components->getNitrogenMonoxide());
        $this->assertSame(3.3, $components->getNitrogenDioxide());
        $this->assertSame(87.26, $components->getOzone());
        $this->assertSame(1.25, $components->getSulphurDioxide());
        $this->assertSame(1.62, $components->getFineParticulateMatter());
        $this->assertSame(2.94, $components->getCoarseParticulateMatter());
        $this->assertSame(0.38, $components->getAmmonia());
    }
}