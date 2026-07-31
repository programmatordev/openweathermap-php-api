<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Integration\Hydration;

use Nyholm\Psr7\Response as PsrResponse;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Response\Response;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Fixture\Entity\Weather;

final class EntityHydrationTest extends TestCase
{
    public function testPayloadReaderWorksInsideTheSdkEntityContract(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $response = new Response(
            data: [
                'main' => ['temp' => 72],
                'dt' => 1700000000,
                'unknown' => 'ignored',
            ],
            rawResponse: new PsrResponse(),
            context: $context
        );

        $weather = $response->entity(Weather::class);

        self::assertInstanceOf(Weather::class, $weather);
        self::assertSame(72.0, $weather->temperature());
        self::assertSame('2023-11-14T22:13:20+00:00', $weather->observedAt()?->format(\DateTimeInterface::ATOM));
        self::assertSame(Units::IMPERIAL, $weather->units());
    }
}
