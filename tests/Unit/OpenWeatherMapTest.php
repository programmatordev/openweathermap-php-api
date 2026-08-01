<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit;

use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Http\Method;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiException;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

class OpenWeatherMapTest extends TestCase
{
    public function testUsesDocumentedConfigurationDefaults(): void
    {
        $api = new OpenWeatherMap('api-key');

        self::assertSame(Units::METRIC, $api->config()->get(OpenWeatherMap::OPTION_UNITS));
        self::assertSame(Language::ENGLISH, $api->config()->get(OpenWeatherMap::OPTION_LANGUAGE));
    }

    public function testAcceptsConfiguredUnitsAndKnownLanguage(): void
    {
        $api = new OpenWeatherMap('api-key', [
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
            OpenWeatherMap::OPTION_LANGUAGE => Language::PORTUGUESE,
        ]);

        self::assertSame(Units::IMPERIAL, $api->config()->get(OpenWeatherMap::OPTION_UNITS));
        self::assertSame(Language::PORTUGUESE, $api->config()->get(OpenWeatherMap::OPTION_LANGUAGE));
    }

    public function testAcceptsAnArbitraryNonEmptyLanguageCode(): void
    {
        $api = new OpenWeatherMap('api-key', [
            OpenWeatherMap::OPTION_LANGUAGE => ' future_language ',
        ]);

        self::assertSame('future_language', $api->config()->get(OpenWeatherMap::OPTION_LANGUAGE));
    }

    public function testConfiguresBaseUrlQueryAuthenticationAndJsonDecoding(): void
    {
        $client = new Client();
        $client->addResponse(new Response(body: '{"ok":true}'));

        $api = new OpenWeatherMap(' secret ');
        $api->setup()->client($client);

        $response = $api->send(Method::GET, '/data/2.5/weather');
        $request = $client->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

        self::assertSame('https://api.openweathermap.org/data/2.5/weather', sprintf(
            '%s://%s%s',
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
            $request->getUri()->getPath()
        ));
        self::assertSame(['appid' => 'secret'], $query);
        self::assertSame(['ok' => true], $response->data());
    }

    #[DataProvider('httpErrors')]
    public function testMapsHttpErrorsToApiException(
        int $statusCode,
        array $data,
        string $expectedClass,
        ?int $expectedApiCode,
        string $expectedMessage
    ): void {
        $client = new Client();
        $client->addResponse(new Response(
            status: $statusCode,
            body: json_encode($data, JSON_THROW_ON_ERROR)
        ));

        $api = new OpenWeatherMap('api-key');
        $api->setup()->client($client);

        try {
            $api->send(Method::GET, '/data/2.5/weather');
        } catch (ApiException $exception) {
            self::assertSame($expectedClass, $exception::class);
            self::assertSame($expectedMessage, $exception->getMessage());
            self::assertSame($statusCode, $exception->statusCode());
            self::assertSame($expectedApiCode, $exception->apiCode());
            self::assertSame($data, $exception->responseData());

            return;
        }

        self::fail(sprintf('Expected %s to be thrown.', ApiException::class));
    }

    /**
     * @return iterable<string, array{
     *     int,
     *     array<string, mixed>,
     *     class-string<ApiException>,
     *     int|null,
     *     string
     * }>
     */
    public static function httpErrors(): iterable
    {
        yield 'bad request using code' => [
            400,
            ['code' => 400000, 'message' => 'Invalid parameter format'],
            BadRequestException::class,
            400000,
            'Invalid parameter format',
        ];
        yield 'unauthorized using numeric string cod' => [
            401,
            ['cod' => '401', 'message' => 'Invalid API key'],
            UnauthorizedException::class,
            401,
            'Invalid API key',
        ];
        yield 'not found' => [
            404,
            ['cod' => 404, 'message' => 'Data not found'],
            NotFoundException::class,
            404,
            'Data not found',
        ];
        yield 'unmapped client error' => [
            418,
            ['code' => 'unexpected', 'message' => 'Unexpected client error'],
            UnexpectedErrorException::class,
            null,
            'Unexpected client error',
        ];
        yield 'too many requests' => [
            429,
            ['cod' => 429, 'message' => 'Too many requests'],
            TooManyRequestsException::class,
            429,
            'Too many requests',
        ];
        yield 'unexpected error' => [
            503,
            ['code' => 503, 'message' => 'Unexpected error'],
            UnexpectedErrorException::class,
            503,
            'Unexpected error',
        ];
    }

    public function testFallsBackToTheHttpErrorWhenPayloadHasNoMessage(): void
    {
        $client = new Client();
        $client->addResponse(new Response(
            status: 503,
            headers: ['Content-Type' => 'application/json'],
            body: '{"unavailable":true}'
        ));

        $api = new OpenWeatherMap('api-key');
        $api->setup()->client($client);

        try {
            $api->send(Method::GET, '/data/2.5/weather');
        } catch (ApiException $exception) {
            self::assertInstanceOf(UnexpectedErrorException::class, $exception);
            self::assertSame(
                'OpenWeather API request failed with HTTP 503 (Service Unavailable).',
                $exception->getMessage()
            );
            self::assertSame(503, $exception->statusCode());
            self::assertNull($exception->apiCode());
            self::assertSame(['unavailable' => true], $exception->responseData());

            return;
        }

        self::fail(sprintf('Expected %s to be thrown.', ApiException::class));
    }

    #[DataProvider('invalidConfigurationProvider')]
    public function testRejectsInvalidConfiguration(string $apiKey, array $options, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        new OpenWeatherMap($apiKey, $options);
    }

    public static function invalidConfigurationProvider(): iterable
    {
        yield 'blank API key' => ['   ', [], 'The API key must be a non-empty string.'];
        yield 'unknown option' => ['api-key', ['unsupported' => true], 'Unknown OpenWeatherMap option: unsupported.'];
        yield 'invalid units' => ['api-key', [OpenWeatherMap::OPTION_UNITS => 'metric'], 'The "units" option must be an instance of'];
        yield 'invalid language type' => ['api-key', [OpenWeatherMap::OPTION_LANGUAGE => 123], 'The "language" option must be an instance of'];
        yield 'blank language' => ['api-key', [OpenWeatherMap::OPTION_LANGUAGE => '  '], 'The "language" option must be a non-empty string.'];
    }
}
