<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneDayTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneDayTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class OneDayTimelineTest extends TestCase
{
    public function testHydratesCapturedTimeline(): void
    {
        $timeline = OneDayTimeline::fromArray(
            Fixture::json('one-call/one-day/success.json'),
        );

        self::assertSame(38.7223, $timeline->coordinates()?->latitude());
        self::assertSame(-9.1393, $timeline->coordinates()?->longitude());
        self::assertSame('Europe/Lisbon', $timeline->timezone()?->identifier());
        self::assertSame(3600, $timeline->timezone()?->offsetSeconds());
        self::assertCount(10, $timeline->periods());
        self::assertContainsOnlyInstancesOf(Period::class, $timeline->periods());
        self::assertSame(1785628800, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame(1786406400, $timeline->periods()[9]->dateTime()?->getTimestamp());
    }

    public function testHydratesCapturedMixedHistoricalAndForecastTimeline(): void
    {
        $timeline = OneDayTimeline::fromArray(
            Fixture::json('one-call/one-day/history.json'),
        );

        self::assertCount(10, $timeline->periods());
        self::assertSame(1785456000, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertNull($timeline->periods()[0]->precipitationProbability());
        self::assertSame(1785628800, $timeline->periods()[2]->dateTime()?->getTimestamp());
        self::assertSame(0.0, $timeline->periods()[2]->precipitationProbability());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = OneDayTimeline::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertNull($missing->timezone());
        self::assertSame([], $missing->periods());

        $timeline = OneDayTimeline::fromArray([
            'lat' => null,
            'timezone_offset' => null,
            'data' => [
                [],
                ['dt' => null, 'unknown' => new \stdClass()],
            ],
            'prev' => null,
            'next' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($timeline->coordinates()?->latitude());
        self::assertNull($timeline->coordinates()?->longitude());
        self::assertNull($timeline->timezone()?->identifier());
        self::assertNull($timeline->timezone()?->offsetSeconds());
        self::assertCount(2, $timeline->periods());
        self::assertNull($timeline->periods()[0]->dateTime());
        self::assertNull($timeline->periods()[1]->temperature());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        OneDayTimeline::fromArray($data);
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
        yield 'periods' => [
            ['data' => 'invalid'],
            '"data" expected array, string received.',
        ];
        yield 'period member' => [
            ['data' => ['invalid']],
            '"data.0" expected array, string received.',
        ];
        yield 'period field' => [
            ['data' => [['temp' => 'invalid']]],
            '"temp" expected array, string received.',
        ];
    }
}
