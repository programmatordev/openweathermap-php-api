<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\Geocoding;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\PostalLocation;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PostalLocationTest extends TestCase
{
    public function testHydratesCapturedPostalLocation(): void
    {
        $data = Fixture::json('geocoding/zip/success.json');
        $location = PostalLocation::fromArray($data);

        self::assertSame('1000-001', $location->postalCode());
        self::assertSame('Lisbon', $location->name());
        self::assertSame(38.7167, $location->coordinates()?->latitude());
        self::assertSame(-9.1333, $location->coordinates()?->longitude());
        self::assertSame('PT', $location->countryCode());
    }

    public function testToleratesMissingNullAndUnknownFields(): void
    {
        $location = PostalLocation::fromArray([
            'zip' => null,
            'name' => null,
            'lat' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($location->postalCode());
        self::assertNull($location->name());
        self::assertNull($location->coordinates());
        self::assertNull($location->countryCode());
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

        PostalLocation::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'postal code' => [['zip' => 1000], 'zip', 'string', 'int'];
        yield 'name' => [['name' => 1], 'name', 'string', 'int'];
        yield 'latitude' => [['lat' => '38.7'], 'lat', 'int|float', 'string'];
        yield 'longitude' => [['lon' => '-9.1'], 'lon', 'int|float', 'string'];
        yield 'country' => [['country' => 1], 'country', 'string', 'int'];
    }
}
