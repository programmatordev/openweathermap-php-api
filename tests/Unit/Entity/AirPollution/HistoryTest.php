<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\History;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\History\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class HistoryTest extends TestCase
{
    public function testHydratesCompleteCapturedHistory(): void
    {
        $history = History::fromArray(
            Fixture::json('air-pollution/history/success.json'),
        );

        self::assertSame(38.7223, $history->coordinates()?->latitude());
        self::assertSame(-9.1393, $history->coordinates()?->longitude());
        self::assertCount(25, $history->periods());
        self::assertContainsOnlyInstancesOf(Period::class, $history->periods());
        self::assertSame(1782864000, $history->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame(1782950400, $history->periods()[24]->dateTime()?->getTimestamp());
        self::assertSame(AirQualityIndex::FAIR, $history->periods()[0]->airQualityIndex());
        self::assertSame(80.71, $history->periods()[0]->components()?->carbonMonoxide());
    }

    public function testHydratesCapturedEmptyHistory(): void
    {
        $history = History::fromArray(
            Fixture::json('air-pollution/history/empty.json'),
        );

        self::assertSame(38.7223, $history->coordinates()?->latitude());
        self::assertSame(-9.1393, $history->coordinates()?->longitude());
        self::assertSame([], $history->periods());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = History::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertSame([], $missing->periods());

        $history = History::fromArray([
            'coord' => [
                'lat' => null,
                'unknown' => new \stdClass(),
            ],
            'list' => [
                [],
                ['dt' => null, 'unknown' => new \stdClass()],
            ],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($history->coordinates()?->latitude());
        self::assertNull($history->coordinates()?->longitude());
        self::assertCount(2, $history->periods());
        self::assertNull($history->periods()[0]->dateTime());
        self::assertNull($history->periods()[1]->airQualityIndex());

        self::assertSame([], History::fromArray(['list' => null])->periods());
        self::assertNull(History::fromArray(['coord' => null])->coordinates());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        History::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'coordinates' => [
            ['coord' => 'invalid'],
            '"coord" expected array, string received.',
        ];
        yield 'latitude' => [
            ['coord' => ['lat' => '38.7']],
            '"lat" expected int|float, string received.',
        ];
        yield 'periods' => [
            ['list' => 'invalid'],
            '"list" expected array, string received.',
        ];
        yield 'period member' => [
            ['list' => ['invalid']],
            '"list.0" expected array, string received.',
        ];
        yield 'period field' => [
            ['list' => [['main' => 'invalid']]],
            '"main" expected array, string received.',
        ];
    }
}
