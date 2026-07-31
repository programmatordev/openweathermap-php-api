<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\PostalLocation;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;

final class GeocodingTest extends ApiTestCase
{
    public function testLooksUpLocationsByName(): void
    {
        $this->respondWithFixture('geocoding/direct/success.json');

        $locations = $this->api->geocoding()->byName(' Springfield,US ', limit: 5);
        $request = $this->client->getLastRequest();

        self::assertCount(5, $locations);
        self::assertContainsOnlyInstancesOf(Location::class, $locations);
        self::assertSame('Illinois', $locations[0]->state());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/geo/1.0/direct', $request->getUri()->getPath());
        self::assertSame([
            'q' => 'Springfield,US',
            'limit' => '5',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testAllowsAnOmittedLimitAndHydratesAnEmptyResult(): void
    {
        $this->respondWithFixture('geocoding/direct/empty.json');

        $locations = $this->api->geocoding()->byName('Unknown location');
        $request = $this->client->getLastRequest();

        self::assertSame([], $locations);
        self::assertSame([
            'q' => 'Unknown location',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testLooksUpALocationByPostalCode(): void
    {
        $this->respondWithFixture('geocoding/zip/success.json');

        $location = $this->api->geocoding()->byPostalCode(' 1000-001 ', 'pt');
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(PostalLocation::class, $location);
        self::assertSame('1000-001', $location->postalCode());
        self::assertSame('Lisbon', $location->name());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/geo/1.0/zip', $request->getUri()->getPath());
        self::assertSame([
            'zip' => '1000-001,PT',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testLooksUpLocationsByCoordinates(): void
    {
        $this->respondWithFixture('geocoding/reverse/success.json');

        $locations = $this->api->geocoding()->byCoordinates(
            latitude: 40.7128,
            longitude: -74.006,
            limit: 5,
        );
        $request = $this->client->getLastRequest();

        self::assertCount(1, $locations);
        self::assertContainsOnlyInstancesOf(Location::class, $locations);
        self::assertSame('New York County', $locations[0]->name());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/geo/1.0/reverse', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '40.7128',
            'lon' => '-74.006',
            'limit' => '5',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testAllowsAnOmittedReverseLimit(): void
    {
        $this->respondWithFixture('geocoding/reverse/success.json');

        $this->api->geocoding()->byCoordinates(
            latitude: 40.7128,
            longitude: -74.006,
        );
        $request = $this->client->getLastRequest();

        self::assertSame([
            'lat' => '40.7128',
            'lon' => '-74.006',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    #[DataProvider('invalidNameArguments')]
    public function testRejectsInvalidNameArguments(
        string $name,
        ?int $limit,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->geocoding()->byName($name, $limit);
    }

    public static function invalidNameArguments(): iterable
    {
        yield 'blank name' => ['   ', null, 'The location name must be a non-empty string.'];
        yield 'limit below minimum' => ['Lisbon', 0, 'The result limit must be between 1 and 5.'];
        yield 'limit above maximum' => ['Lisbon', 6, 'The result limit must be between 1 and 5.'];
    }

    #[DataProvider('invalidPostalCodeArguments')]
    public function testRejectsInvalidPostalCodeArguments(
        string $postalCode,
        string $countryCode,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->geocoding()
            ->byPostalCode($postalCode, $countryCode);
    }

    public static function invalidPostalCodeArguments(): iterable
    {
        yield 'blank postal code' => [
            '   ',
            'PT',
            'The postal code must be a non-empty string.',
        ];
        yield 'blank country code' => [
            '1000-001',
            '  ',
            'The country code must contain exactly two ASCII letters.',
        ];
        yield 'short country code' => [
            '1000-001',
            'P',
            'The country code must contain exactly two ASCII letters.',
        ];
        yield 'non-letter country code' => [
            '1000-001',
            'P1',
            'The country code must contain exactly two ASCII letters.',
        ];
    }

    public function testRejectsAReverseLimitBelowOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The result limit must be at least 1.');

        $this->api->geocoding()
            ->byCoordinates(
                latitude: 40.7128,
                longitude: -74.006,
                limit: 0,
            );
    }
}
