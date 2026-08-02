<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall\OneDayTimeline;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneDayTimeline\FeelsLikeTemperature;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

final class FeelsLikeTemperatureTest extends TestCase
{
    public function testHydratesDailyFeelsLikeTemperatures(): void
    {
        $temperature = FeelsLikeTemperature::fromArray([
            'day' => 25.53,
            'night' => 19.4,
            'eve' => 24.82,
            'morn' => 18.74,
        ]);

        self::assertSame(25.53, $temperature->day());
        self::assertSame(Unit::CELSIUS, $temperature->dayUnit());
        self::assertSame('25.53 °C', $temperature->dayWithUnit());
        self::assertSame(19.4, $temperature->night());
        self::assertSame('19.4 °C', $temperature->nightWithUnit());
        self::assertSame(24.82, $temperature->evening());
        self::assertSame('24.82 °C', $temperature->eveningWithUnit());
        self::assertSame(18.74, $temperature->morning());
        self::assertSame('18.74 °C', $temperature->morningWithUnit());
    }

    public function testRetainsUnitsFromHydrationContext(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $temperature = FeelsLikeTemperature::fromArray(['night' => 68], $context);

        self::assertSame(Unit::FAHRENHEIT, $temperature->nightUnit());
        self::assertSame('68 °F', $temperature->nightWithUnit());
    }

    public function testToleratesMissingNullAndUnknownFields(): void
    {
        $missing = FeelsLikeTemperature::fromArray([]);

        self::assertNull($missing->day());
        self::assertNull($missing->night());
        self::assertNull($missing->evening());
        self::assertNull($missing->morning());

        $temperature = FeelsLikeTemperature::fromArray([
            'day' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($temperature->day());
        self::assertNull($temperature->dayWithUnit());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(string $field): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" expected int|float, string received.',
            $field,
        ));

        FeelsLikeTemperature::fromArray([$field => 'invalid']);
    }

    public static function invalidFields(): iterable
    {
        yield 'day' => ['day'];
        yield 'night' => ['night'];
        yield 'evening' => ['eve'];
        yield 'morning' => ['morn'];
    }
}
