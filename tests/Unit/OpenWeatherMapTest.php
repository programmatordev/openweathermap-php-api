<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit;

use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Http\Method;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
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
            OpenWeatherMap::OPTION_LANGUAGE => 'future_language',
        ]);

        self::assertSame('future_language', $api->config()->get(OpenWeatherMap::OPTION_LANGUAGE));
    }

    public function testConfiguresBaseUrlQueryAuthenticationAndJsonDecoding(): void
    {
        $client = new Client();
        $client->addResponse(new Response(body: '{"ok":true}'));

        $api = new OpenWeatherMap('secret');
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
        yield 'blank language' => ['api-key', [OpenWeatherMap::OPTION_LANGUAGE => '  '], 'The "language" option must not be an empty string.'];
    }
}
