<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\PostalLocation;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class GeocodingTest extends TestCase
{
    public function testLooksUpLocationsByName(): void
    {
        $client = new Client();
        $client->addResponse(new Response(
            body: json_encode(
                Fixture::json('geocoding/direct/success.json'),
                JSON_THROW_ON_ERROR,
            ),
        ));

        $api = new OpenWeatherMap('api-key');
        $api->setup()->client($client);

        $locations = $api->geocoding()->byName('Springfield,US', limit: 5);
        $request = $client->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

        self::assertCount(5, $locations);
        self::assertContainsOnlyInstancesOf(Location::class, $locations);
        self::assertSame('Illinois', $locations[0]->state());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/geo/1.0/direct', $request->getUri()->getPath());
        self::assertSame([
            'q' => 'Springfield,US',
            'limit' => '5',
            'appid' => 'api-key',
        ], $query);
    }

    public function testAllowsAnOmittedLimitAndHydratesAnEmptyResult(): void
    {
        $client = new Client();
        $client->addResponse(new Response(
            body: json_encode(
                Fixture::json('geocoding/direct/empty.json'),
                JSON_THROW_ON_ERROR,
            ),
        ));

        $api = new OpenWeatherMap('api-key');
        $api->setup()->client($client);

        $locations = $api->geocoding()->byName('Unknown location');
        $request = $client->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

        self::assertSame([], $locations);
        self::assertSame([
            'q' => 'Unknown location',
            'appid' => 'api-key',
        ], $query);
    }

    public function testLooksUpALocationByPostalCode(): void
    {
        $client = new Client();
        $client->addResponse(new Response(
            body: json_encode(
                Fixture::json('geocoding/zip/success.json'),
                JSON_THROW_ON_ERROR,
            ),
        ));

        $api = new OpenWeatherMap('api-key');
        $api->setup()->client($client);

        $location = $api->geocoding()->byPostalCode(' 1000-001 ', 'pt');
        $request = $client->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

        self::assertInstanceOf(PostalLocation::class, $location);
        self::assertSame('1000-001', $location->postalCode());
        self::assertSame('Lisbon', $location->name());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/geo/1.0/zip', $request->getUri()->getPath());
        self::assertSame([
            'zip' => '1000-001,PT',
            'appid' => 'api-key',
        ], $query);
    }

    public function testLooksUpLocationsByCoordinates(): void
    {
        $client = new Client();
        $client->addResponse(new Response(
            body: json_encode(
                Fixture::json('geocoding/reverse/success.json'),
                JSON_THROW_ON_ERROR,
            ),
        ));

        $api = new OpenWeatherMap('api-key');
        $api->setup()->client($client);

        $locations = $api->geocoding()->byCoordinates(
            latitude: 40.7128,
            longitude: -74.006,
            limit: 5,
        );
        $request = $client->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

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
        ], $query);
    }

    public function testAllowsAnOmittedReverseLimit(): void
    {
        $client = new Client();
        $client->addResponse(new Response(
            body: json_encode(
                Fixture::json('geocoding/reverse/success.json'),
                JSON_THROW_ON_ERROR,
            ),
        ));

        $api = new OpenWeatherMap('api-key');
        $api->setup()->client($client);

        $api->geocoding()->byCoordinates(
            latitude: 40.7128,
            longitude: -74.006,
        );
        $request = $client->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

        self::assertSame([
            'lat' => '40.7128',
            'lon' => '-74.006',
            'appid' => 'api-key',
        ], $query);
    }

    #[DataProvider('invalidArguments')]
    public function testRejectsInvalidArguments(
        string $name,
        ?int $limit,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        (new OpenWeatherMap('api-key'))->geocoding()->byName($name, $limit);
    }

    public static function invalidArguments(): iterable
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

        (new OpenWeatherMap('api-key'))
            ->geocoding()
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

        (new OpenWeatherMap('api-key'))
            ->geocoding()
            ->byCoordinates(
                latitude: 40.7128,
                longitude: -74.006,
                limit: 0,
            );
    }
}
