<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;

trait TestEndpointInvalidResponseTrait
{
    #[DataProvider('provideEndpointInvalidResponseData')]
    public function testEndpointInvalidResponse(
        string $endpointName,
        string $methodName,
        array $methodParams
    ): void
    {
        $this->expectException(ValidationException::class);
        $this->givenApi()->$endpointName()->$methodName(...$methodParams);
    }

    public abstract static function provideEndpointInvalidResponseData(): \Generator;
}