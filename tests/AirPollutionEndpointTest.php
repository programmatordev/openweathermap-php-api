<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocationList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirQuality;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointInvalidResponseTrait;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointSuccessResponseTrait;

class AirPollutionEndpointTest extends AbstractTest
{
    use TestEndpointSuccessResponseTrait;
    use TestEndpointInvalidResponseTrait;

    public static function provideEndpointSuccessResponseData(): \Generator
    {
        yield 'get current' => [
            MockResponse::AIR_POLLUTION_CURRENT,
            'airPollution',
            'getCurrent',
            [50, 50],
            'assertGetCurrentResponse'
        ];
        yield 'get forecast' => [
            MockResponse::AIR_POLLUTION_FORECAST,
            'airPollution',
            'getForecast',
            [50, 50],
            'assertGetForecastResponse'
        ];
        yield 'get history' => [
            MockResponse::AIR_POLLUTION_HISTORY,
            'airPollution',
            'getHistory',
            [50, 50, new \DateTime('yesterday'), new \DateTime('today')],
            'assertGetHistoryResponse'
        ];
    }

    public static function provideEndpointInvalidResponseData(): \Generator
    {
        yield 'get current, latitude lower than -90' => ['airPollution', 'getCurrent', [-91, 50]];
        yield 'get current, latitude greater than 90' => ['airPollution', 'getCurrent', [91, 50]];
        yield 'get current, longitude lower than -180' => ['airPollution', 'getCurrent', [50, -181]];
        yield 'get current, longitude greater than 180' => ['airPollution', 'getCurrent', [50, 181]];

        yield 'get forecast, latitude lower than -90' => ['airPollution', 'getForecast', [-91, 50]];
        yield 'get forecast, latitude greater than 90' => ['airPollution', 'getForecast', [91, 50]];
        yield 'get forecast, longitude lower than -180' => ['airPollution', 'getForecast', [50, -181]];
        yield 'get forecast, longitude greater than 180' => ['airPollution', 'getForecast', [50, 181]];

        yield 'get history, latitude lower than -90' => ['airPollution', 'getHistory',
            [-91, 50, new \DateTime('yesterday'), new \DateTime('today')]
        ];
        yield 'get history, latitude greater than 90' => ['airPollution', 'getHistory',
            [91, 50, new \DateTime('yesterday'), new \DateTime('today')]
        ];
        yield 'get history, longitude lower than -180' => ['airPollution', 'getHistory',
            [50, -181, new \DateTime('yesterday'), new \DateTime('today')]
        ];
        yield 'get history, longitude greater than 180' => ['airPollution', 'getHistory',
            [50, 181, new \DateTime('yesterday'), new \DateTime('today')]
        ];
        yield 'get history, future end date' => ['airPollution', 'getHistory',
            [50, 50, new \DateTime('yesterday'), new \DateTime('tomorrow')]
        ];
        yield 'get history, end date before start date' => ['airPollution', 'getHistory',
            [50, 50, new \DateTime('yesterday'), new \DateTime('-2 days')]
        ];
    }

    public function testAirPollutionMethodsExist()
    {
        $this->assertSame(false, method_exists(AirPollutionEndpoint::class, 'withUnitSystem'));
        $this->assertSame(false, method_exists(AirPollutionEndpoint::class, 'withLanguage'));
        $this->assertSame(true, method_exists(AirPollutionEndpoint::class, 'withCacheTtl'));
    }

    private function assertGetCurrentResponse(AirPollutionLocation $airPollutionLocation): void
    {
        $this->assertSame(196.93, $airPollutionLocation->getCarbonMonoxide());
        $this->assertSame(0.65, $airPollutionLocation->getNitrogenMonoxide());
        $this->assertSame(3.98, $airPollutionLocation->getNitrogenDioxide());
        $this->assertSame(107.29, $airPollutionLocation->getOzone());
        $this->assertSame(1.46, $airPollutionLocation->getSulphurDioxide());
        $this->assertSame(8.58, $airPollutionLocation->getFineParticulateMatter());
        $this->assertSame(13.5, $airPollutionLocation->getCoarseParticulateMatter());
        $this->assertSame(2.03, $airPollutionLocation->getAmmonia());
        $this->assertSame('2023-06-23 17:21:57', $airPollutionLocation->getDateTime()->format('Y-m-d H:i:s'));

        $coordinate = $airPollutionLocation->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $airQuality = $airPollutionLocation->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(3, $airQuality->getIndex());
        $this->assertSame('Moderate', $airQuality->getQualitativeName());
    }

    private function assertGetForecastResponse(AirPollutionLocationList $airPollutionLocationList): void
    {
        $coordinate = $airPollutionLocationList->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $list = $airPollutionLocationList->getList();
        $this->assertContainsOnlyInstancesOf(AirPollution::class, $list);
        $this->assertSame(196.93, $list[0]->getCarbonMonoxide());
        $this->assertSame(0.65, $list[0]->getNitrogenMonoxide());
        $this->assertSame(3.98, $list[0]->getNitrogenDioxide());
        $this->assertSame(107.29, $list[0]->getOzone());
        $this->assertSame(1.46, $list[0]->getSulphurDioxide());
        $this->assertSame(8.58, $list[0]->getFineParticulateMatter());
        $this->assertSame(13.5, $list[0]->getCoarseParticulateMatter());
        $this->assertSame(2.03, $list[0]->getAmmonia());
        $this->assertSame('2023-06-23 17:00:00', $list[0]->getDateTime()->format('Y-m-d H:i:s'));

        $airQuality = $list[0]->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(3, $airQuality->getIndex());
        $this->assertSame('Moderate', $airQuality->getQualitativeName());
    }

    private function assertGetHistoryResponse(AirPollutionLocationList $airPollutionLocationList): void
    {
        $coordinate = $airPollutionLocationList->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7078, $coordinate->getLatitude());
        $this->assertSame(-9.1366, $coordinate->getLongitude());

        $list = $airPollutionLocationList->getList();
        $this->assertContainsOnlyInstancesOf(AirPollution::class, $list);
        $this->assertSame(220.3, $list[0]->getCarbonMonoxide());
        $this->assertSame(0.12, $list[0]->getNitrogenMonoxide());
        $this->assertSame(3.3, $list[0]->getNitrogenDioxide());
        $this->assertSame(87.26, $list[0]->getOzone());
        $this->assertSame(1.25, $list[0]->getSulphurDioxide());
        $this->assertSame(1.62, $list[0]->getFineParticulateMatter());
        $this->assertSame(2.94, $list[0]->getCoarseParticulateMatter());
        $this->assertSame(0.38, $list[0]->getAmmonia());
        $this->assertSame('2023-06-18 18:00:00', $list[0]->getDateTime()->format('Y-m-d H:i:s'));

        $airQuality = $list[0]->getAirQuality();
        $this->assertInstanceOf(AirQuality::class, $airQuality);
        $this->assertSame(2, $airQuality->getIndex());
        $this->assertSame('Fair', $airQuality->getQualitativeName());
    }
}