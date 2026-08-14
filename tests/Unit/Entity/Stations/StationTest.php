<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\Stations;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\Station;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class StationTest extends TestCase
{
    public function testHydratesCapturedStation(): void
    {
        $station = Station::fromArray(Fixture::json('stations/retrieve.json'));

        self::assertSame('6a779284adde3b0001343e02', $station->id());
        self::assertSame('2026-08-08T20:33:08+00:00', $station->createdAt()->format(\DateTimeInterface::ATOM));
        self::assertSame('107000', $station->createdAt()->format('u'));
        self::assertSame('UTC', $station->createdAt()->getTimezone()->getName());
        self::assertSame('2026-08-08T20:33:08+00:00', $station->updatedAt()->format(\DateTimeInterface::ATOM));
        self::assertSame('107000', $station->updatedAt()->format('u'));
        self::assertSame('openweathermap-php-api-fixture', $station->externalId());
        self::assertSame('OpenWeatherMap PHP API Fixture', $station->name());
        self::assertSame(38.7223, $station->latitude());
        self::assertSame(-9.1393, $station->longitude());
        self::assertSame(100.0, $station->altitude());
        self::assertSame(10, $station->rank());
        self::assertNull($station->userId());
        self::assertNull($station->sourceType());
    }

    public function testHydratesCapturedRegistrationFields(): void
    {
        $station = Station::fromArray(Fixture::json('stations/register.json'));

        self::assertSame('6a779284adde3b0001343e02', $station->id());
        self::assertSame('user-fixture', $station->userId());
        self::assertSame(5, $station->sourceType());
        self::assertSame('107598', $station->createdAt()->format('u'));
        self::assertSame('107598', $station->updatedAt()->format('u'));
    }

    public function testToleratesNullAndUnknownOptionalFields(): void
    {
        $data = Fixture::json('stations/retrieve.json');
        $data = array_replace($data, [
            'user_id' => null,
            'source_type' => null,
            'unknown' => new \stdClass(),
        ]);
        $station = Station::fromArray($data);

        self::assertNull($station->userId());
        self::assertNull($station->sourceType());
    }

    #[DataProvider('requiredFields')]
    public function testRejectsMissingRequiredFields(
        string $field,
        string $path,
        string $expectedType,
    ): void {
        $data = Fixture::json('stations/retrieve.json');
        unset($data[$field]);

        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" expected %s, null received.',
            $path,
            $expectedType,
        ));

        Station::fromArray($data);
    }

    #[DataProvider('requiredFields')]
    public function testRejectsNullRequiredFields(
        string $field,
        string $path,
        string $expectedType,
    ): void {
        $data = Fixture::json('stations/retrieve.json');
        $data[$field] = null;

        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" expected %s, null received.',
            $path,
            $expectedType,
        ));

        Station::fromArray($data);
    }

    public static function requiredFields(): iterable
    {
        yield 'identifier' => ['id', 'id', 'string'];
        yield 'created date' => ['created_at', 'created_at', 'ISO 8601 date-time string'];
        yield 'updated date' => ['updated_at', 'updated_at', 'ISO 8601 date-time string'];
        yield 'external identifier' => ['external_id', 'external_id', 'string'];
        yield 'name' => ['name', 'name', 'string'];
        yield 'latitude' => ['latitude', 'latitude', 'int|float'];
        yield 'longitude' => ['longitude', 'longitude', 'int|float'];
        yield 'altitude' => ['altitude', 'altitude', 'int|float'];
        yield 'rank' => ['rank', 'rank', 'int'];
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Station::fromArray(array_replace(
            Fixture::json('stations/retrieve.json'),
            $data,
        ));
    }

    public static function invalidFields(): iterable
    {
        yield 'identifier' => [['id' => 1], '"id" expected string, int received.'];
        yield 'registration identifier' => [['ID' => 1], '"ID" expected string, int received.'];
        yield 'created date type' => [['created_at' => 1], '"created_at" expected string, int received.'];
        yield 'created date value' => [['created_at' => 'tomorrow'], '"created_at" expected ISO 8601 date-time string, "tomorrow" received.'];
        yield 'updated date' => [['updated_at' => 1], '"updated_at" expected string, int received.'];
        yield 'external identifier' => [['external_id' => 1], '"external_id" expected string, int received.'];
        yield 'name' => [['name' => 1], '"name" expected string, int received.'];
        yield 'latitude' => [['latitude' => '38.7'], '"latitude" expected int|float, string received.'];
        yield 'longitude' => [['longitude' => '-9.1'], '"longitude" expected int|float, string received.'];
        yield 'altitude' => [['altitude' => '100'], '"altitude" expected int|float, string received.'];
        yield 'rank' => [['rank' => '10'], '"rank" expected int, string received.'];
        yield 'user identifier' => [['user_id' => 1], '"user_id" expected string, int received.'];
        yield 'source type' => [['source_type' => '5'], '"source_type" expected int, string received.'];
    }
}
