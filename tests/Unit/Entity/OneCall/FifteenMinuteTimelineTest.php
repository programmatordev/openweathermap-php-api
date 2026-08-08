<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\FifteenMinuteTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\FifteenMinuteTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class FifteenMinuteTimelineTest extends TestCase
{
    public function testHydratesCapturedTimeline(): void
    {
        $timeline = FifteenMinuteTimeline::fromArray(
            Fixture::json('one-call/fifteen-minute/success.json'),
        );

        self::assertSame(38.7223, $timeline->coordinates()?->latitude());
        self::assertSame(-9.1393, $timeline->coordinates()?->longitude());
        self::assertSame('Europe/Lisbon', $timeline->timezone()?->identifier());
        self::assertSame(3600, $timeline->timezone()?->offsetSeconds());
        self::assertCount(50, $timeline->periods());
        self::assertContainsOnlyInstancesOf(Period::class, $timeline->periods());
        self::assertSame(1785670200, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame(1785714300, $timeline->periods()[49]->dateTime()?->getTimestamp());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = FifteenMinuteTimeline::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertNull($missing->timezone());
        self::assertSame([], $missing->periods());

        $timeline = FifteenMinuteTimeline::fromArray([
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

        FifteenMinuteTimeline::fromArray($data);
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
            ['data' => [['pressure' => '1015.75']]],
            '"pressure" expected int|float, string received.',
        ];
    }
}
