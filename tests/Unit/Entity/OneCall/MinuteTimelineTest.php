<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class MinuteTimelineTest extends TestCase
{
    #[DataProvider('capturedTimelines')]
    public function testHydratesCapturedTimeline(
        string $fixture,
        float $latitude,
        float $longitude,
        string $timezone,
        int $timezoneOffset,
    ): void {
        $timeline = MinuteTimeline::fromArray(Fixture::json($fixture));

        self::assertSame($latitude, $timeline->coordinates()?->latitude());
        self::assertSame($longitude, $timeline->coordinates()?->longitude());
        self::assertSame($timezone, $timeline->timezone()?->identifier());
        self::assertSame($timezoneOffset, $timeline->timezone()?->offsetSeconds());
        self::assertCount(60, $timeline->periods());
        self::assertContainsOnlyInstancesOf(Period::class, $timeline->periods());
        self::assertSame(1785669780, $timeline->periods()[0]->forecastAt()?->getTimestamp());
        self::assertSame(1785673320, $timeline->periods()[59]->forecastAt()?->getTimestamp());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = MinuteTimeline::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertNull($missing->timezone());
        self::assertSame([], $missing->periods());

        $timeline = MinuteTimeline::fromArray([
            'lat' => null,
            'timezone' => null,
            'timezone_offset' => null,
            'data' => [
                [],
                ['dt' => null, 'unknown' => new \stdClass()],
            ],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($timeline->coordinates()?->latitude());
        self::assertNull($timeline->coordinates()?->longitude());
        self::assertNull($timeline->timezone()?->identifier());
        self::assertNull($timeline->timezone()?->offsetSeconds());
        self::assertCount(2, $timeline->periods());
        self::assertNull($timeline->periods()[0]->forecastAt());
        self::assertNull($timeline->periods()[1]->precipitation());

        self::assertSame([], MinuteTimeline::fromArray(['data' => null])->periods());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        MinuteTimeline::fromArray($data);
    }

    public static function capturedTimelines(): iterable
    {
        yield 'dry' => [
            'one-call/one-minute/success.json',
            38.7223,
            -9.1393,
            'Europe/Lisbon',
            3600,
        ];
        yield 'precipitation with alerts' => [
            'one-call/one-minute/precipitation-alerts.json',
            -38.4,
            -71.58,
            'America/Santiago',
            -14400,
        ];
    }

    public static function invalidFields(): iterable
    {
        yield 'latitude' => [
            ['lat' => '38.7'],
            '"lat" expected int|float, string received.',
        ];
        yield 'timezone' => [
            ['timezone' => 1],
            '"timezone" expected string, int received.',
        ];
        yield 'timezone offset' => [
            ['timezone_offset' => '3600'],
            '"timezone_offset" expected int, string received.',
        ];
        yield 'periods' => [
            ['data' => 'invalid'],
            '"data" expected array, string received.',
        ];
        yield 'period member' => [
            ['data' => ['invalid']],
            '"data.0" expected array, string received.',
        ];
        yield 'period field' => [
            ['data' => [['precipitation' => '0.5']]],
            '"precipitation" expected int|float, string received.',
        ];
    }
}
