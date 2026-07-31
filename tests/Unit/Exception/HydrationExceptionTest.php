<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Exception;

use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;

final class HydrationExceptionTest extends TestCase
{
    public function testItDescribesTheInvalidPayloadValue(): void
    {
        $exception = HydrationException::invalidType(
            entity: 'CurrentWeather',
            path: 'main.temp',
            expectedType: 'int|float',
            value: 'warm'
        );

        self::assertSame(
            'Cannot hydrate CurrentWeather: "main.temp" expected int|float, string received.',
            $exception->getMessage()
        );
    }
}
