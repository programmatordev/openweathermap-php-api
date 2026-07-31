<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource\Concern;

use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithLanguage;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithUnits;

final class FluentConfigurationTest extends TestCase
{
    private object $resource;

    protected function setUp(): void
    {
        $this->resource = new class {
            use WithLanguage;
            use WithUnits;

            public function configuredLanguage(): Language|string|null
            {
                return $this->languageOverride();
            }

            public function configuredUnits(): ?Units
            {
                return $this->unitsOverride();
            }
        };
    }

    public function testConfigurationIsImmutableAndChainable(): void
    {
        $configured = $this->resource
            ->withUnits(Units::IMPERIAL)
            ->withLanguage(Language::PORTUGUESE);

        self::assertNotSame($this->resource, $configured);
        self::assertNull($this->resource->configuredUnits());
        self::assertNull($this->resource->configuredLanguage());
        self::assertSame(Units::IMPERIAL, $configured->configuredUnits());
        self::assertSame(Language::PORTUGUESE, $configured->configuredLanguage());
    }

    public function testLaterOverridesDoNotMutateEarlierClones(): void
    {
        $metric = $this->resource->withUnits(Units::METRIC);
        $imperial = $metric->withUnits(Units::IMPERIAL);

        self::assertSame(Units::METRIC, $metric->configuredUnits());
        self::assertSame(Units::IMPERIAL, $imperial->configuredUnits());
    }

    public function testItAcceptsAnArbitraryLanguageCode(): void
    {
        $configured = $this->resource->withLanguage('future_language');

        self::assertSame('future_language', $configured->configuredLanguage());
    }

    public function testItRejectsABlankLanguageCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The language must not be an empty string.');

        $this->resource->withLanguage('  ');
    }
}
