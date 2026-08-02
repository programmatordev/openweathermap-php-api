<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Current;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;

final class OneCallTest extends ApiTestCase
{
    public function testGetsCurrentWeatherByCoordinates(): void
    {
        $this->respondWithFixture('one-call/current/success.json');

        $current = $this->api->oneCall()->current(
            latitude: 38.7223,
            longitude: -9.1393,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(Current::class, $current);
        self::assertSame(24.34, $current->temperature());
        self::assertSame(Unit::CELSIUS, $current->temperatureUnit());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/4.0/onecall/current', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testFluentOverridesAreRequestLocal(): void
    {
        $this->client->addResponse(new Response(
            body: '{"data":[{"temp":72.5}]}',
        ));
        $this->respondWithFixture('one-call/current/success.json');

        $oneCall = $this->api->oneCall();
        $overridden = $oneCall
            ->withUnits(Units::IMPERIAL)
            ->withLanguage('pt');

        $imperial = $overridden->current(38.7223, -9.1393);
        $imperialRequest = $this->client->getLastRequest();
        $metric = $oneCall->current(38.7223, -9.1393);
        $metricRequest = $this->client->getLastRequest();

        self::assertSame(Unit::FAHRENHEIT, $imperial->temperatureUnit());
        self::assertSame('72.5 °F', $imperial->temperatureWithUnit());
        self::assertSame(Unit::CELSIUS, $metric->temperatureUnit());
        self::assertSame('imperial', $this->query($imperialRequest)['units']);
        self::assertSame('pt', $this->query($imperialRequest)['lang']);
        self::assertSame('metric', $this->query($metricRequest)['units']);
        self::assertSame('en', $this->query($metricRequest)['lang']);
    }

    public function testGetsMinuteTimelineByCoordinates(): void
    {
        $this->respondWithFixture('one-call/one-minute/success.json');

        $timeline = $this->api->oneCall()->minuteTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(MinuteTimeline::class, $timeline);
        self::assertCount(60, $timeline->periods());
        self::assertSame(1785669780, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/4.0/onecall/timeline/1min', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testMinuteTimelineAcceptsFluentConfiguration(): void
    {
        $this->respondWithFixture('one-call/one-minute/precipitation-alerts.json');

        $timeline = $this->api
            ->oneCall()
            ->withUnits(Units::IMPERIAL)
            ->withLanguage('pt')
            ->minuteTimeline(-38.4, -71.58);
        $request = $this->client->getLastRequest();

        self::assertSame(Unit::MILLIMETERS_PER_HOUR, $timeline->periods()[0]->precipitationUnit());
        self::assertSame([
            'lat' => '-38.4',
            'lon' => '-71.58',
            'units' => 'imperial',
            'lang' => 'pt',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    #[DataProvider('invalidCoordinates')]
    public function testRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->oneCall()->current($latitude, $longitude);
    }

    #[DataProvider('invalidCoordinates')]
    public function testMinuteTimelineRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->oneCall()->minuteTimeline($latitude, $longitude);
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
