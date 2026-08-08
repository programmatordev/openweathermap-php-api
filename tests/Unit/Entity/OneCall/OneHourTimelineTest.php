<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneHourTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneHourTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline\Pagination;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class OneHourTimelineTest extends TestCase
{
    public function testHydratesCapturedTimeline(): void
    {
        $timeline = OneHourTimeline::fromArray(
            Fixture::json('one-call/one-hour/success.json'),
        );

        self::assertSame(38.7223, $timeline->coordinates()?->latitude());
        self::assertSame(-9.1393, $timeline->coordinates()?->longitude());
        self::assertSame('Europe/Lisbon', $timeline->timezone()?->identifier());
        self::assertSame(3600, $timeline->timezone()?->offsetSeconds());
        self::assertCount(20, $timeline->periods());
        self::assertContainsOnlyInstancesOf(Period::class, $timeline->periods());
        self::assertInstanceOf(Pagination::class, $timeline->pagination());
        self::assertSame(1785668400, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame(1785736800, $timeline->periods()[19]->dateTime()?->getTimestamp());
        self::assertTrue($timeline->pagination()->hasPreviousPage());
        self::assertTrue($timeline->pagination()->hasNextPage());
        self::assertSame(
            'https://api.openweathermap.org/data/4.0/onecall/timeline/1h?'
            .'cnt=20&lat=38.7223&lon=-9.1393&start=1785596400'
            .'&appid=%7BAPI%20key%7D&units=metric&lang=en',
            $timeline->pagination()->previousPageUrl(),
        );
        self::assertSame(
            'https://api.openweathermap.org/data/4.0/onecall/timeline/1h?'
            .'cnt=20&lat=38.7223&lon=-9.1393&start=1785740400'
            .'&appid=%7BAPI%20key%7D&units=metric&lang=en',
            $timeline->pagination()->nextPageUrl(),
        );
    }

    public function testHydratesCapturedHistoricalTimeline(): void
    {
        $timeline = OneHourTimeline::fromArray(
            Fixture::json('one-call/one-hour/history.json'),
        );

        self::assertCount(20, $timeline->periods());
        self::assertSame(1785495600, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame(1785564000, $timeline->periods()[19]->dateTime()?->getTimestamp());
        self::assertNull($timeline->periods()[0]->precipitationProbability());
    }

    public function testHydratesWithConfigurationContextWithoutResolver(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));
        $timeline = OneHourTimeline::fromArray([
            'data' => [['temp' => 72.5]],
            'next' => '/data/4.0/onecall/timeline/1h?start=1785740400',
        ], $context);

        self::assertSame(Unit::FAHRENHEIT, $timeline->periods()[0]->temperatureUnit());
        self::assertTrue($timeline->pagination()->hasNextPage());
        self::assertSame(
            '/data/4.0/onecall/timeline/1h?start=1785740400',
            $timeline->pagination()->nextPageUrl(),
        );
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = OneHourTimeline::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertNull($missing->timezone());
        self::assertSame([], $missing->periods());
        self::assertNull($missing->pagination()->previousPageUrl());
        self::assertNull($missing->pagination()->nextPageUrl());
        self::assertFalse($missing->pagination()->hasPreviousPage());
        self::assertFalse($missing->pagination()->hasNextPage());
        self::assertNull($missing->pagination()->previousPage());
        self::assertNull($missing->pagination()->nextPage());

        $timeline = OneHourTimeline::fromArray([
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

        OneHourTimeline::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'periods' => [
            ['data' => 'invalid'],
            '"data" expected array, string received.',
        ];
        yield 'period member' => [
            ['data' => ['invalid']],
            '"data.0" expected array, string received.',
        ];
        yield 'period field' => [
            ['data' => [['temp' => '25.05']]],
            '"temp" expected int|float, string received.',
        ];
        yield 'previous page URL type' => [
            ['prev' => 1],
            '"prev" expected string, int received.',
        ];
    }
}
