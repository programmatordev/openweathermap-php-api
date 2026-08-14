<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall\MinuteTimeline;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PeriodTest extends TestCase
{
    public function testHydratesCapturedDryPeriod(): void
    {
        $period = self::fromFixture('one-call/one-minute/success.json');

        self::assertSame(1785669780, $period->dateTime()?->getTimestamp());
        self::assertSame('UTC', $period->dateTime()?->getTimezone()->getName());
        self::assertSame(0.0, $period->precipitation());
        self::assertSame(Unit::MILLIMETERS_PER_HOUR, $period->precipitationUnit());
        self::assertSame('0 mm/h', $period->precipitationWithUnit());
        self::assertSame([], $period->alertIds());
    }

    public function testHydratesCapturedPrecipitationAndAlertIds(): void
    {
        $period = self::fromFixture('one-call/one-minute/precipitation-alerts.json');

        self::assertSame(5.9626, $period->precipitation());
        self::assertSame('5.9626 mm/h', $period->precipitationWithUnit());
        self::assertSame([
            'urn:oid:2.49.0.0.152.0.2026.7.31.14.20.43:f1076d7511a15522d5a6e41917020bc0',
        ], $period->alertIds());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Period::fromArray([]);

        self::assertNull($missing->dateTime());
        self::assertNull($missing->precipitation());
        self::assertNull($missing->precipitationWithUnit());
        self::assertSame([], $missing->alertIds());

        $period = Period::fromArray([
            'dt' => null,
            'precipitation' => null,
            'alerts' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($period->dateTime());
        self::assertNull($period->precipitation());
        self::assertSame([], $period->alertIds());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Period::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'forecast time' => [
            ['dt' => '1785669780'],
            '"dt" expected int, string received.',
        ];
        yield 'precipitation' => [
            ['precipitation' => '5.9626'],
            '"precipitation" expected int|float, string received.',
        ];
        yield 'alert IDs' => [
            ['alerts' => 'invalid'],
            '"alerts" expected array, string received.',
        ];
        yield 'alert ID member' => [
            ['alerts' => [123]],
            '"alerts.0" expected string, int received.',
        ];
    }

    private static function fromFixture(string $path): Period
    {
        $response = Fixture::json($path);

        return Period::fromArray($response['data'][0]);
    }
}
