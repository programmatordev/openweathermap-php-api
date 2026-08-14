<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timezone;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class TimezoneTest extends TestCase
{
    public function testHydratesCapturedTimezone(): void
    {
        $timezone = Timezone::fromArray(Fixture::json('one-call/current/success.json'));

        self::assertSame('Europe/Lisbon', $timezone->identifier());
        self::assertSame(3600, $timezone->offsetSeconds());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Timezone::fromArray([]);

        self::assertNull($missing->identifier());
        self::assertNull($missing->offsetSeconds());

        $timezone = Timezone::fromArray([
            'timezone' => null,
            'timezone_offset' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($timezone->identifier());
        self::assertNull($timezone->offsetSeconds());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Timezone::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'identifier' => [
            ['timezone' => 1],
            '"timezone" expected string, int received.',
        ];
        yield 'offset' => [
            ['timezone_offset' => '3600'],
            '"timezone_offset" expected int, string received.',
        ];
    }
}
