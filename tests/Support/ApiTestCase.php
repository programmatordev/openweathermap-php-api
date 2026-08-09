<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Support;

use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Psr\Http\Message\RequestInterface;

abstract class ApiTestCase extends TestCase
{
    protected Client $client;

    protected OpenWeatherMap $api;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Client();
        $this->api = new OpenWeatherMap('api-key');
        $this->api->setup()->client($this->client);
    }

    protected function respondWithFixture(
        string $path,
        int $status = 200,
    ): void
    {
        $this->client->addResponse(new Response(
            status: $status,
            body: Fixture::contents($path),
        ));
    }

    protected function query(RequestInterface $request): array
    {
        parse_str($request->getUri()->getQuery(), $query);

        return $query;
    }
}
