<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\Geocoding;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class LocationTest extends TestCase
{
    public function testHydratesCapturedDirectLocation(): void
    {
        $data = Fixture::json('geocoding/direct/success.json');
        $location = Location::fromArray($data[0]);

        self::assertSame('Springfield', $location->name());
        self::assertSame('Springfield', $location->localName('en'));
        self::assertSame('ஸ்பிரிங்ஃபீல்ட்', $location->localName('ta'));
        self::assertNull($location->localName('pt'));
        self::assertSame(6, count($location->localNames()));
        self::assertSame(39.7990175, $location->latitude());
        self::assertSame(-89.6439575, $location->longitude());
        self::assertSame('US', $location->countryCode());
        self::assertSame('Illinois', $location->state());
    }

    public function testHydratesCapturedReverseLocation(): void
    {
        $data = Fixture::json('geocoding/reverse/success.json');
        $location = Location::fromArray($data[0]);

        self::assertSame('New York County', $location->name());
        self::assertSame('Nova Iorque', $location->localName('pt'));
        self::assertSame(40.7127281, $location->latitude());
        self::assertSame(-74.0060152, $location->longitude());
        self::assertSame('US', $location->countryCode());
        self::assertSame('New York', $location->state());
    }

    public function testToleratesMissingNullAndUnknownFields(): void
    {
        $location = Location::fromArray([
            'name' => null,
            'local_names' => null,
            'lat' => null,
            'lon' => -9.1,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($location->name());
        self::assertSame([], $location->localNames());
        self::assertNull($location->latitude());
        self::assertSame(-9.1, $location->longitude());
        self::assertNull($location->countryCode());
        self::assertNull($location->state());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFieldTypes(
        array $data,
        string $path,
        string $expectedType,
        string $receivedType,
    ): void {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" expected %s, %s received.',
            $path,
            $expectedType,
            $receivedType,
        ));

        Location::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'name' => [['name' => 1], 'name', 'string', 'int'];
        yield 'local names' => [['local_names' => 'English'], 'local_names', 'array', 'string'];
        yield 'local name' => [['local_names' => ['en' => 1]], 'local_names.en', 'string', 'int'];
        yield 'latitude' => [['lat' => '39.7'], 'lat', 'int|float', 'string'];
        yield 'longitude' => [['lon' => '-89.6'], 'lon', 'int|float', 'string'];
        yield 'country' => [['country' => 1], 'country', 'string', 'int'];
        yield 'state' => [['state' => 1], 'state', 'string', 'int'];
    }
}
