<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirQuality;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class AirQualityTest extends TestCase
{
    public function testHydratesCapturedAirQuality(): void
    {
        $response = Fixture::json('air-pollution/current/good.json');
        $airQuality = AirQuality::fromArray($response['list'][0]);

        self::assertSame(AirQualityIndex::GOOD, $airQuality->airQualityIndex());

        $components = $airQuality->components();

        self::assertSame(96.56, $components?->carbonMonoxide());
        self::assertSame('96.56 µg/m³', $components?->carbonMonoxideWithUnit());
        self::assertSame(0.01, $components?->nitrogenMonoxide());
        self::assertSame('0.01 µg/m³', $components?->nitrogenMonoxideWithUnit());
        self::assertSame(7.17, $components?->nitrogenDioxide());
        self::assertSame('7.17 µg/m³', $components?->nitrogenDioxideWithUnit());
        self::assertSame(29.69, $components?->ozone());
        self::assertSame('29.69 µg/m³', $components?->ozoneWithUnit());
        self::assertSame(1.03, $components?->sulphurDioxide());
        self::assertSame('1.03 µg/m³', $components?->sulphurDioxideWithUnit());
        self::assertSame(5.89, $components?->fineParticulateMatter());
        self::assertSame('5.89 µg/m³', $components?->fineParticulateMatterWithUnit());
        self::assertSame(7.62, $components?->coarseParticulateMatter());
        self::assertSame('7.62 µg/m³', $components?->coarseParticulateMatterWithUnit());
        self::assertSame(0.65, $components?->ammonia());
        self::assertSame('0.65 µg/m³', $components?->ammoniaWithUnit());
        self::assertSame(
            Unit::MICROGRAMS_PER_CUBIC_METER,
            $components?->fineParticulateMatterUnit(),
        );
    }

    #[DataProvider('airQualityIndexes')]
    public function testHydratesEveryDocumentedAirQualityIndex(
        int $value,
        AirQualityIndex $expected,
    ): void {
        $airQuality = AirQuality::fromArray([
            'main' => ['aqi' => $value],
        ]);

        self::assertSame($expected, $airQuality->airQualityIndex());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = AirQuality::fromArray([]);

        self::assertNull($missing->airQualityIndex());
        self::assertNull($missing->components());

        $airQuality = AirQuality::fromArray([
            'main' => ['aqi' => null, 'unknown' => true],
            'components' => [
                'co' => null,
                'unknown' => new \stdClass(),
            ],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($airQuality->airQualityIndex());
        self::assertNull($airQuality->components()?->carbonMonoxide());
        self::assertNull($airQuality->components()?->nitrogenMonoxide());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        AirQuality::fromArray($data);
    }

    public static function airQualityIndexes(): iterable
    {
        yield 'good' => [1, AirQualityIndex::GOOD];
        yield 'fair' => [2, AirQualityIndex::FAIR];
        yield 'moderate' => [3, AirQualityIndex::MODERATE];
        yield 'poor' => [4, AirQualityIndex::POOR];
        yield 'very poor' => [5, AirQualityIndex::VERY_POOR];
    }

    public static function invalidFields(): iterable
    {
        yield 'main' => [
            ['main' => 'invalid'],
            '"main" expected array, string received.',
        ];
        yield 'AQI type' => [
            ['main' => ['aqi' => '1']],
            '"main.aqi" expected int, string received.',
        ];
        yield 'AQI float' => [
            ['main' => ['aqi' => 1.0]],
            '"main.aqi" expected int, float received.',
        ];
        yield 'unsupported AQI' => [
            ['main' => ['aqi' => 6]],
            '"main.aqi" expected an integer from 1 through 5, "6" received.',
        ];
        yield 'components' => [
            ['components' => 'invalid'],
            '"components" expected array, string received.',
        ];
        yield 'component concentration' => [
            ['components' => ['co' => '96.56']],
            '"co" expected int|float, string received.',
        ];
    }
}
