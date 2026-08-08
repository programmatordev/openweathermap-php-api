<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Current;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\FifteenMinuteTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneDayTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneHourTimeline;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;

final class OneCallTest extends ApiTestCase
{
    public function testGetsAlertById(): void
    {
        $this->respondWithFixture('one-call/alert/chile-rain.json');
        $id = 'urn:oid:2.49.0.0.152.0.2026.7.31.14.20.43:'
            .'f1076d7511a15522d5a6e41917020bc0';

        $alert = $this->api->oneCall()->alert($id);
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(Alert::class, $alert);
        self::assertSame($id, $alert->id());
        self::assertSame('Dirección Meteorológica de Chile', $alert->senderName());
        self::assertSame('GET', $request->getMethod());
        self::assertSame(
            '/data/4.0/onecall/alert/'.rawurlencode($id),
            $request->getUri()->getPath(),
        );
        self::assertSame(['appid' => 'api-key'], $this->query($request));
    }

    public function testEncodesOpaqueAlertIdAsOnePathSegment(): void
    {
        $this->client->addResponse(new Response(body: '{}'));

        $this->api->oneCall()->alert('agency:alert/segment');
        $request = $this->client->getLastRequest();

        self::assertSame(
            '/data/4.0/onecall/alert/agency%3Aalert%2Fsegment',
            $request->getUri()->getPath(),
        );
    }

    public function testRejectsBlankAlertId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The alert ID must be a non-empty string.');

        $this->api->oneCall()->alert('  ');
    }

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

    public function testGetsFifteenMinuteTimelineByCoordinates(): void
    {
        $this->respondWithFixture('one-call/fifteen-minute/success.json');

        $timeline = $this->api->oneCall()->fifteenMinuteTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(FifteenMinuteTimeline::class, $timeline);
        self::assertCount(50, $timeline->periods());
        self::assertSame(1785670200, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertNull($timeline->pagination()->previousPageUrl());
        self::assertStringContainsString(
            'appid=',
            $timeline->pagination()->nextPageUrl() ?? '',
        );
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/4.0/onecall/timeline/15min', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsFifteenMinuteTimelineFromStart(): void
    {
        $this->respondWithFixture('one-call/fifteen-minute/pagination.json');

        $timeline = $this->api->oneCall()->fifteenMinuteTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
            startAt: new \DateTimeImmutable('@1785715200'),
            count: 50,
        );
        $request = $this->client->getLastRequest();

        self::assertSame(1785715200, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1785715200',
            'cnt' => '50',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsNextFifteenMinuteTimelinePage(): void
    {
        $this->respondWithFixture('one-call/fifteen-minute/success.json');
        $this->client->addResponse(new Response(
            body: '{"data":[{"dt":1785715200}]}',
        ));

        $timeline = $this->api->oneCall()->fifteenMinuteTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );

        self::assertCount(1, $this->client->getRequests());

        $nextPage = $timeline->pagination()->nextPage();
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(FifteenMinuteTimeline::class, $nextPage);
        self::assertSame(1785715200, $nextPage->periods()[0]->dateTime()?->getTimestamp());
        self::assertCount(2, $this->client->getRequests());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https', $request->getUri()->getScheme());
        self::assertSame('/data/4.0/onecall/timeline/15min', $request->getUri()->getPath());
        self::assertSame([
            'cnt' => '50',
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1785715200',
            'appid' => 'api-key',
            'units' => 'metric',
            'lang' => 'en',
        ], $this->query($request));
    }

    public function testGetsPreviousFifteenMinuteTimelinePage(): void
    {
        $this->respondWithFixture('one-call/fifteen-minute/pagination.json');
        $this->client->addResponse(new Response(
            body: '{"data":[{"dt":1785670200}]}',
        ));

        $timeline = $this->api->oneCall()->fifteenMinuteTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );

        self::assertCount(1, $this->client->getRequests());

        $previousPage = $timeline->pagination()->previousPage();
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(FifteenMinuteTimeline::class, $previousPage);
        self::assertSame(1785670200, $previousPage->periods()[0]->dateTime()?->getTimestamp());
        self::assertCount(2, $this->client->getRequests());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https', $request->getUri()->getScheme());
        self::assertSame('/data/4.0/onecall/timeline/15min', $request->getUri()->getPath());
        self::assertSame([
            'cnt' => '50',
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1785670200',
            'appid' => 'api-key',
            'units' => 'metric',
            'lang' => 'en',
        ], $this->query($request));
    }

    public function testFifteenMinuteTimelineAcceptsFluentConfiguration(): void
    {
        $this->client->addResponse(new Response(
            body: '{"data":[{"temp":72.5}]}',
        ));

        $timeline = $this->api
            ->oneCall()
            ->withUnits(Units::IMPERIAL)
            ->withLanguage('pt')
            ->fifteenMinuteTimeline(38.7223, -9.1393, count: 3);
        $request = $this->client->getLastRequest();

        self::assertSame(Unit::FAHRENHEIT, $timeline->periods()[0]->temperatureUnit());
        self::assertSame('72.5 °F', $timeline->periods()[0]->temperatureWithUnit());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'cnt' => '3',
            'units' => 'imperial',
            'lang' => 'pt',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsOneHourTimelineByCoordinates(): void
    {
        $this->respondWithFixture('one-call/one-hour/success.json');

        $timeline = $this->api->oneCall()->oneHourTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(OneHourTimeline::class, $timeline);
        self::assertCount(20, $timeline->periods());
        self::assertSame(1785668400, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertStringContainsString(
            'appid=',
            $timeline->pagination()->previousPageUrl() ?? '',
        );
        self::assertStringContainsString(
            'appid=',
            $timeline->pagination()->nextPageUrl() ?? '',
        );
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/4.0/onecall/timeline/1h', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsOneHourTimelineFromStart(): void
    {
        $this->respondWithFixture('one-call/one-hour/history.json');

        $timeline = $this->api->oneCall()->oneHourTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
            startAt: new \DateTimeImmutable('@1785495600'),
        );
        $request = $this->client->getLastRequest();

        self::assertSame(1785495600, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1785495600',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsNextOneHourTimelinePage(): void
    {
        $this->respondWithFixture('one-call/one-hour/success.json');
        $this->client->addResponse(new Response(
            body: '{"data":[{"dt":1785740400}]}',
        ));

        $timeline = $this->api->oneCall()->oneHourTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );

        self::assertCount(1, $this->client->getRequests());

        $nextPage = $timeline->pagination()->nextPage();
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(OneHourTimeline::class, $nextPage);
        self::assertSame(1785740400, $nextPage->periods()[0]->dateTime()?->getTimestamp());
        self::assertCount(2, $this->client->getRequests());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https', $request->getUri()->getScheme());
        self::assertSame('/data/4.0/onecall/timeline/1h', $request->getUri()->getPath());
        self::assertSame([
            'cnt' => '20',
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1785740400',
            'appid' => 'api-key',
            'units' => 'metric',
            'lang' => 'en',
        ], $this->query($request));
    }

    public function testGetsPreviousOneHourTimelinePage(): void
    {
        $this->respondWithFixture('one-call/one-hour/success.json');
        $this->client->addResponse(new Response(
            body: '{"data":[{"dt":1785596400}]}',
        ));

        $timeline = $this->api->oneCall()->oneHourTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );

        self::assertCount(1, $this->client->getRequests());

        $previousPage = $timeline->pagination()->previousPage();
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(OneHourTimeline::class, $previousPage);
        self::assertSame(1785596400, $previousPage->periods()[0]->dateTime()?->getTimestamp());
        self::assertCount(2, $this->client->getRequests());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https', $request->getUri()->getScheme());
        self::assertSame('/data/4.0/onecall/timeline/1h', $request->getUri()->getPath());
        self::assertSame([
            'cnt' => '20',
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1785596400',
            'appid' => 'api-key',
            'units' => 'metric',
            'lang' => 'en',
        ], $this->query($request));
    }

    public function testOneHourTimelineAcceptsFluentConfiguration(): void
    {
        $this->client->addResponse(new Response(
            body: '{"data":[{"temp":72.5}]}',
        ));

        $timeline = $this->api
            ->oneCall()
            ->withUnits(Units::IMPERIAL)
            ->withLanguage('pt')
            ->oneHourTimeline(38.7223, -9.1393, count: 3);
        $request = $this->client->getLastRequest();

        self::assertSame(Unit::FAHRENHEIT, $timeline->periods()[0]->temperatureUnit());
        self::assertSame('72.5 °F', $timeline->periods()[0]->temperatureWithUnit());
        self::assertSame('3', $this->query($request)['cnt']);
        self::assertSame('imperial', $this->query($request)['units']);
        self::assertSame('pt', $this->query($request)['lang']);
    }

    public function testGetsOneDayTimelineByCoordinates(): void
    {
        $this->respondWithFixture('one-call/one-day/success.json');

        $timeline = $this->api->oneCall()->oneDayTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(OneDayTimeline::class, $timeline);
        self::assertCount(10, $timeline->periods());
        self::assertSame(1785628800, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertStringContainsString(
            'appid=',
            $timeline->pagination()->previousPageUrl() ?? '',
        );
        self::assertStringContainsString(
            'appid=',
            $timeline->pagination()->nextPageUrl() ?? '',
        );
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/4.0/onecall/timeline/1day', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsOneDayTimelineFromStart(): void
    {
        $this->respondWithFixture('one-call/one-day/history.json');

        $timeline = $this->api->oneCall()->oneDayTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
            startAt: new \DateTimeImmutable('@1785456000'),
        );
        $request = $this->client->getLastRequest();

        self::assertSame(1785456000, $timeline->periods()[0]->dateTime()?->getTimestamp());
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1785456000',
            'units' => 'metric',
            'lang' => 'en',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsNextOneDayTimelinePage(): void
    {
        $this->respondWithFixture('one-call/one-day/success.json');
        $this->client->addResponse(new Response(
            body: '{"data":[{"dt":1786492800}]}',
        ));

        $timeline = $this->api->oneCall()->oneDayTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );

        self::assertCount(1, $this->client->getRequests());

        $nextPage = $timeline->pagination()->nextPage();
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(OneDayTimeline::class, $nextPage);
        self::assertSame(1786492800, $nextPage->periods()[0]->dateTime()?->getTimestamp());
        self::assertCount(2, $this->client->getRequests());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https', $request->getUri()->getScheme());
        self::assertSame('/data/4.0/onecall/timeline/1day', $request->getUri()->getPath());
        self::assertSame([
            'cnt' => '10',
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1786492800',
            'appid' => 'api-key',
            'units' => 'metric',
            'lang' => 'en',
        ], $this->query($request));
    }

    public function testGetsPreviousOneDayTimelinePage(): void
    {
        $this->respondWithFixture('one-call/one-day/success.json');
        $this->client->addResponse(new Response(
            body: '{"data":[{"dt":1784764800}]}',
        ));

        $timeline = $this->api->oneCall()->oneDayTimeline(
            latitude: 38.7223,
            longitude: -9.1393,
        );

        self::assertCount(1, $this->client->getRequests());

        $previousPage = $timeline->pagination()->previousPage();
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(OneDayTimeline::class, $previousPage);
        self::assertSame(1784764800, $previousPage->periods()[0]->dateTime()?->getTimestamp());
        self::assertCount(2, $this->client->getRequests());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https', $request->getUri()->getScheme());
        self::assertSame('/data/4.0/onecall/timeline/1day', $request->getUri()->getPath());
        self::assertSame([
            'cnt' => '10',
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1784764800',
            'appid' => 'api-key',
            'units' => 'metric',
            'lang' => 'en',
        ], $this->query($request));
    }

    public function testOneDayTimelineAcceptsFluentConfigurationAndCount(): void
    {
        $this->client->addResponse(new Response(
            body: '{"data":[{"temp":{"day":72.5}}]}',
        ));

        $timeline = $this->api
            ->oneCall()
            ->withUnits(Units::IMPERIAL)
            ->withLanguage('pt')
            ->oneDayTimeline(38.7223, -9.1393, count: 3);
        $request = $this->client->getLastRequest();

        self::assertSame(Unit::FAHRENHEIT, $timeline->periods()[0]->temperature()?->dayUnit());
        self::assertSame('72.5 °F', $timeline->periods()[0]->temperature()?->dayWithUnit());
        self::assertSame('3', $this->query($request)['cnt']);
        self::assertSame('imperial', $this->query($request)['units']);
        self::assertSame('pt', $this->query($request)['lang']);
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

    #[DataProvider('invalidCoordinates')]
    public function testFifteenMinuteTimelineRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->oneCall()->fifteenMinuteTimeline($latitude, $longitude);
    }

    #[DataProvider('invalidCoordinates')]
    public function testOneHourTimelineRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->oneCall()->oneHourTimeline($latitude, $longitude);
    }

    #[DataProvider('invalidCoordinates')]
    public function testOneDayTimelineRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->oneCall()->oneDayTimeline($latitude, $longitude);
    }

    public function testFifteenMinuteTimelineRejectsInvalidCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The timeline count must be at least 1.');

        $this->api->oneCall()->fifteenMinuteTimeline(38.7223, -9.1393, count: 0);
    }

    public function testOneHourTimelineRejectsInvalidCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The timeline count must be at least 1.');

        $this->api->oneCall()->oneHourTimeline(38.7223, -9.1393, count: 0);
    }

    public function testOneDayTimelineRejectsInvalidCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The timeline count must be at least 1.');

        $this->api->oneCall()->oneDayTimeline(38.7223, -9.1393, count: 0);
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
