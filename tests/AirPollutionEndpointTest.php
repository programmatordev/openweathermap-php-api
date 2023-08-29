<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocationList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirQuality;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class AirPollutionEndpointTest extends AbstractTest
{
    // --- CURRENT ---

    public function testAirPollutionGetCurrent()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::AIR_POLLUTION_CURRENT
            )
        );

        $response = $this->givenApi()->airPollution()->getCurrent(50, 50);
        $this->assertCurrentResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testAirPollutionGetCurrentWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->airPollution()->getCurrent($latitude, $longitude);
    }

    // --- FORECAST ---

    public function testAirPollutionGetForecast()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::AIR_POLLUTION_FORECAST
            )
        );

        $response = $this->givenApi()->airPollution()->getForecast(50, 50);
        $this->assertForecastResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testAirPollutionGetForecastWithInvalidCoordinate(float $latitude, float $longitude, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->airPollution()->getForecast($latitude, $longitude);
    }

    // --- HISTORY ---

    public function testAirPollutionGetHistory()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::AIR_POLLUTION_HISTORY
            )
        );

        $utcTimezone = new \DateTimeZone('UTC');

        $response = $this->givenApi()->airPollution()->getHistory(
            50,
            50,
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

        $this->givenApi()->airPollution()->getHistory($latitude, $longitude, $startDate, $endDate);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidPastDateData')]
    public function testAirPollutionGetHistoryWithInvalidPastStartDate(
        \DateTimeImmutable $startDate,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->givenApi()->airPollution()->getHistory(
            50,
            50,
            $startDate,
            new \DateTimeImmutable('-5 days', new \DateTimeZone('UTC'))
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidPastDateData')]
    public function testAirPollutionGetHistoryWithInvalidPastEndDate(
        \DateTimeImmutable $endDate,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->givenApi()->airPollution()->getHistory(
            50,
            50,
            new \DateTimeImmutable('-5 days', new \DateTimeZone('UTC')),
            $endDate
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidDateRangeData')]
    public function testAirPollutionGetHistoryWithInvalidDateRange(
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->givenApi()->airPollution()->getHistory(50, 50, $startDate, $endDate);
    }

    // --- ASSERT METHODS EXIST ---

    public function testAirPollutionMethodsExist()
    {
        $this->assertSame(false, method_exists(AirPollutionEndpoint::class, 'withUnitSystem'));
        $this->assertSame(false, method_exists(AirPollutionEndpoint::class, 'withLanguage'));
        $this->assertSame(true, method_exists(AirPollutionEndpoint::class, 'withCacheTtl'));
    }

    // --- ASSERT RESPONSES ---

    private function assertCurrentResponse(AirPollutionLocation $response): void
    {
        $this->assertInstanceOf(AirPollutionLocation::class, $response);

        $this->assertSame(196.93, $response->getCarbonMonoxide());
        $this->assertSame(0.65, $response->getNitrogenMonoxide());
        $this->assertSame(3.98, $response->getNitrogenDioxide());
        $this->assertSame(107.29, $response->getOzone());
        $this->assertSame(1.46, $response->getSulphurDioxide());
        $this->assertSame(8.58, $response->getFineParticulateMatter());
        $this->assertSame(13.5, $response->getCoarseParticulateMatter());
        $this->assertSame(2.03, $response->getAmmonia());

        $coordinate = $response->getCoordinate();
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
    }

    private function assertForecastResponse(AirPollutionLocationList $response): void
    {
        $this->assertInstanceOf(AirPollutionLocationList::class, $response);

        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $list = $response->getList();
        $this->assertContainsOnlyInstancesOf(AirPollution::class, $list);

        $this->assertSame(196.93, $list[0]->getCarbonMonoxide());
        $this->assertSame(0.65, $list[0]->getNitrogenMonoxide());
        $this->assertSame(3.98, $list[0]->getNitrogenDioxide());
        $this->assertSame(107.29, $list[0]->getOzone());
        $this->assertSame(1.46, $list[0]->getSulphurDioxide());
        $this->assertSame(8.58, $list[0]->getFineParticulateMatter());
        $this->assertSame(13.5, $list[0]->getCoarseParticulateMatter());
        $this->assertSame(2.03, $list[0]->getAmmonia());

        $dateTime = $list[0]->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-06-23 17:00:00', $dateTime->format('Y-m-d H:i:s'));

        $airQuality = $list[0]->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(3, $airQuality->getIndex());
        $this->assertSame('Moderate', $airQuality->getQualitativeName());
    }

    private function assertHistoryResponse(AirPollutionLocationList $response): void
    {
        $this->assertInstanceOf(AirPollutionLocationList::class, $response);

        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $list = $response->getList();
        $this->assertContainsOnlyInstancesOf(AirPollution::class, $list);

        $this->assertSame(220.3, $list[0]->getCarbonMonoxide());
        $this->assertSame(0.12, $list[0]->getNitrogenMonoxide());
        $this->assertSame(3.3, $list[0]->getNitrogenDioxide());
        $this->assertSame(87.26, $list[0]->getOzone());
        $this->assertSame(1.25, $list[0]->getSulphurDioxide());
        $this->assertSame(1.62, $list[0]->getFineParticulateMatter());
        $this->assertSame(2.94, $list[0]->getCoarseParticulateMatter());
        $this->assertSame(0.38, $list[0]->getAmmonia());

        $dateTime = $list[0]->getDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTime);
        $this->assertSame('2023-06-18 18:00:00', $dateTime->format('Y-m-d H:i:s'));

        $airQuality = $list[0]->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(2, $airQuality->getIndex());
        $this->assertSame('Fair', $airQuality->getQualitativeName());
    }
}