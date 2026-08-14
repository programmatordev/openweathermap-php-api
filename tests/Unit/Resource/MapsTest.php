<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Enum\MapLayer;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Resource\Maps;
use ProgrammatorDev\OpenWeatherMap\Response\MapTile;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class MapsTest extends ApiTestCase
{
    public function testMarksTheApiKeyAsSensitive(): void
    {
        $constructor = new \ReflectionMethod(Maps::class, '__construct');

        self::assertCount(
            1,
            $constructor->getParameters()[1]->getAttributes(\SensitiveParameter::class),
        );
    }

    public function testGeneratesAnAuthenticatedTileUrlWithoutSendingARequest(): void
    {
        $api = new OpenWeatherMap('api key&value');
        $api->setup()->client($this->client);

        $url = $api->maps()->tileUrl(
            layer: MapLayer::PRECIPITATION,
            zoom: 6,
            x: 31,
            y: 20,
        );
        $expected = 'https://tile.openweathermap.org/map/precipitation_new/6/31/20.png'
            . '?appid=api%20key%26value';

        self::assertSame($expected, $url);
        self::assertSame([], $this->client->getRequests());
    }

    public function testValidatesGeneratedTileUrlAddresses(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'At zoom level 1, the tile X coordinate must be between 0 and 1.',
        );

        $this->api->maps()->tileUrl(MapLayer::CLOUDS, 1, 2, 0);
    }

    public function testGeneratesAnAuthenticatedTileUrlTemplateWithoutSendingARequest(): void
    {
        $template = $this->api->maps()->tileUrlTemplate(MapLayer::WIND);

        self::assertSame(
            'https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=api-key',
            $template,
        );
        self::assertSame([], $this->client->getRequests());
    }

    #[DataProvider('layers')]
    public function testGetsWeatherMapTiles(
        MapLayer $layer,
        string $path,
        string $fixture,
    ): void {
        $contents = Fixture::contents($fixture);
        $this->client->addResponse(new Response(
            headers: ['Content-Type' => 'image/png'],
            body: $contents,
        ));

        $tile = $this->api->maps()->tile(
            layer: $layer,
            zoom: 1,
            x: 1,
            y: 1,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(MapTile::class, $tile);
        self::assertSame($contents, $tile->contents());
        self::assertSame('image/png', $tile->contentType());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https', $request->getUri()->getScheme());
        self::assertSame('tile.openweathermap.org', $request->getUri()->getHost());
        self::assertSame($path, $request->getUri()->getPath());
        self::assertSame(['appid' => 'api-key'], $this->query($request));
    }

    /**
     * @return iterable<string, array{MapLayer, string, string}>
     */
    public static function layers(): iterable
    {
        yield 'clouds' => [
            MapLayer::CLOUDS,
            '/map/clouds_new/1/1/1.png',
            'maps/tile/clouds-new.png',
        ];
        yield 'precipitation' => [
            MapLayer::PRECIPITATION,
            '/map/precipitation_new/1/1/1.png',
            'maps/tile/precipitation-new.png',
        ];
        yield 'pressure' => [
            MapLayer::PRESSURE,
            '/map/pressure_new/1/1/1.png',
            'maps/tile/pressure-new.png',
        ];
        yield 'wind' => [
            MapLayer::WIND,
            '/map/wind_new/1/1/1.png',
            'maps/tile/wind-new.png',
        ];
        yield 'temperature' => [
            MapLayer::TEMPERATURE,
            '/map/temp_new/1/1/1.png',
            'maps/tile/temp-new.png',
        ];
    }

    #[DataProvider('invalidAddresses')]
    public function testRejectsInvalidTileAddresses(
        int $zoom,
        int $x,
        int $y,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->maps()->tile(MapLayer::CLOUDS, $zoom, $x, $y);
    }

    /**
     * @return iterable<string, array{int, int, int, string}>
     */
    public static function invalidAddresses(): iterable
    {
        yield 'negative zoom' => [
            -1,
            0,
            0,
            'The tile zoom level must be zero or greater.',
        ];
        yield 'negative X' => [
            9,
            -1,
            0,
            'At zoom level 9, the tile X coordinate must be between 0 and 511.',
        ];
        yield 'X above maximum' => [
            9,
            512,
            0,
            'At zoom level 9, the tile X coordinate must be between 0 and 511.',
        ];
        yield 'Y above maximum' => [
            9,
            0,
            512,
            'At zoom level 9, the tile Y coordinate must be between 0 and 511.',
        ];
        yield 'negative Y' => [
            9,
            0,
            -1,
            'At zoom level 9, the tile Y coordinate must be between 0 and 511.',
        ];
    }

    public function testRejectsDecodedResponseData(): void
    {
        $this->client->addResponse(new Response(
            headers: ['Content-Type' => 'image/png'],
            body: '{}',
        ));

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'Map tile response body must contain raw image data.',
        );

        $this->api->maps()->tile(MapLayer::CLOUDS, 1, 1, 1);
    }

    public function testRejectsUnexpectedImageContentTypes(): void
    {
        $this->client->addResponse(new Response(
            headers: ['Content-Type' => 'image/jpeg'],
            body: 'image bytes',
        ));

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'Map tile response must use the "image/png" content type.',
        );

        $this->api->maps()->tile(MapLayer::CLOUDS, 1, 1, 1);
    }
}
