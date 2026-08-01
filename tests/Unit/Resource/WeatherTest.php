<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\CurrentWeather;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;

final class WeatherTest extends ApiTestCase
{
    public function testGetsCurrentWeatherByCoordinates(): void
    {
        $this->respondWithFixture('weather/current/success.json');

        $weather = $this->api->weather()->current(
            latitude: 38.7223,
            longitude: -9.1393,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(CurrentWeather::class, $weather);
        self::assertSame('Socorro', $weather->name());
        self::assertSame(Unit::CELSIUS, $weather->temperatureUnit());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/2.5/weather', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testUsesApiConfigurationForTheRequestAndEntity(): void
    {
        $this->api = new OpenWeatherMap('api-key', [
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
            OpenWeatherMap::OPTION_LANGUAGE => Language::PORTUGUESE,
        ]);
        $this->api->setup()->client($this->client);
        $this->client->addResponse(new Response(
            body: '{"main":{"temp":72.5},"wind":{"speed":10}}',
        ));

        $weather = $this->api->weather()->current(38.7223, -9.1393);
        $request = $this->client->getLastRequest();

        self::assertSame(Unit::FAHRENHEIT, $weather->temperatureUnit());
        self::assertSame('72.5 °F', $weather->temperatureWithUnit());
        self::assertSame(Unit::MILES_PER_HOUR, $weather->wind()?->speedUnit());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'units' => 'imperial',
            'lang' => 'pt',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testFluentOverridesAreRequestLocal(): void
    {
        $this->client->addResponse(new Response(
            body: '{"main":{"temp":72.5}}',
        ));
        $this->respondWithFixture('weather/current/success.json');

        $weather = $this->api->weather();
        $overridden = $weather
            ->withUnits(Units::IMPERIAL)
            ->withLanguage('pt');

        $imperial = $overridden->current(38.7223, -9.1393);
        $imperialRequest = $this->client->getLastRequest();
        $metric = $weather->current(38.7223, -9.1393);
        $metricRequest = $this->client->getLastRequest();

        self::assertSame(Unit::FAHRENHEIT, $imperial->temperatureUnit());
        self::assertSame(Unit::CELSIUS, $metric->temperatureUnit());
        self::assertSame('imperial', $this->query($imperialRequest)['units']);
        self::assertSame('pt', $this->query($imperialRequest)['lang']);
        self::assertSame('metric', $this->query($metricRequest)['units']);
        self::assertSame('en', $this->query($metricRequest)['lang']);
    }

    #[DataProvider('invalidCoordinates')]
    public function testRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->weather()->current($latitude, $longitude);
    }

    public static function invalidCoordinates(): iterable
    {
        yield 'invalid latitude' => [
            90.0001,
            0,
            'Latitude must be a finite number between -90 and 90.',
        ];
        yield 'invalid longitude' => [
            0,
            180.0001,
            'Longitude must be a finite number between -180 and 180.',
        ];
    }
}
