<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\ApiErrorException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\UnexpectedErrorException;

class ApiErrorTest extends AbstractTest
{
    #[DataProvider('provideApiErrorData')]
    public function testApiError(int $statusCode, string $expectedException)
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: $statusCode,
                body: MockResponse::API_ERROR
            )
        );

        $this->assertInstanceOf(ApiErrorException::class, new $expectedException('Error'));

        $this->expectException($expectedException);
        $this->getApi()->getWeather()->getCurrent(38.7077507, -9.1365919);
    }

    public static function provideApiErrorData(): \Generator
    {
        yield 'bad request exception' => [400, BadRequestException::class];
        yield 'unauthorized exception' => [401, UnauthorizedException::class];
        yield 'not found exception' => [404, NotFoundException::class];
        yield 'too many requests exception' => [429, TooManyRequestsException::class];
        yield 'unexpected error exception' => [500, UnexpectedErrorException::class];
    }
}