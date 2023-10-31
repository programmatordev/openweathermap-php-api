<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Util;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;

trait TestEndpointSuccessResponseTrait
{
    #[DataProvider('provideEndpointSuccessResponseData')]
    public function testEndpointSuccessResponse(
        string $mockData,
        string $endpointName,
        string $methodName,
        array $methodParams,
        string $callback
    ): void
    {
        $this->mockHttpClient->addResponse(new Response(
            body: $mockData
        ));

        $response = $this->givenApi()->$endpointName()->$methodName(...$methodParams);

        $this->$callback($response);
    }

    public abstract static function provideEndpointSuccessResponseData(): \Generator;
}