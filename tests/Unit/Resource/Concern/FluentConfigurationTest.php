<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource\Concern;

use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithLanguage;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithUnits;

final class FluentConfigurationTest extends TestCase
{
    private ConfigurableResource $resource;

    protected function setUp(): void
    {
        $this->resource = (new TestableOpenWeatherMap('api-key'))->configurableResource();
    }

    public function testConfigurationIsImmutableAndChainable(): void
    {
        $configured = $this->resource
            ->withUnits(Units::IMPERIAL)
            ->withLanguage(Language::PORTUGUESE);

        self::assertNotSame($this->resource, $configured);
        self::assertSame(Units::METRIC, $this->resource->resolvedUnitsValue());
        self::assertSame('en', $this->resource->resolvedLanguageValue());
        self::assertSame(Units::IMPERIAL, $configured->resolvedUnitsValue());
        self::assertSame('pt', $configured->resolvedLanguageValue());
    }

    public function testLaterOverridesDoNotMutateEarlierClones(): void
    {
        $metric = $this->resource->withUnits(Units::METRIC);
        $imperial = $metric->withUnits(Units::IMPERIAL);

        self::assertSame(Units::METRIC, $metric->resolvedUnitsValue());
        self::assertSame(Units::IMPERIAL, $imperial->resolvedUnitsValue());
    }

    public function testItAcceptsAnArbitraryLanguageCode(): void
    {
        $configured = $this->resource->withLanguage('future_language');

        self::assertSame('future_language', $configured->resolvedLanguageValue());
    }

    public function testItRejectsABlankLanguageCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The language must not be an empty string.');

        $this->resource->withLanguage('  ');
    }

    public function testItResolvesApiConfigurationWithoutOverrides(): void
    {
        $resource = (new TestableOpenWeatherMap('api-key', [
            OpenWeatherMap::OPTION_UNITS => Units::STANDARD,
            OpenWeatherMap::OPTION_LANGUAGE => Language::PORTUGUESE,
        ]))->configurableResource();

        self::assertSame(Units::STANDARD, $resource->resolvedUnitsValue());
        self::assertSame('pt', $resource->resolvedLanguageValue());
    }

    public function testOverridesTakePrecedenceWhenResolvingConfiguration(): void
    {
        $configured = $this->resource
            ->withUnits(Units::IMPERIAL)
            ->withLanguage(Language::PORTUGUESE);

        self::assertSame(Units::IMPERIAL, $configured->resolvedUnitsValue());
        self::assertSame('pt', $configured->resolvedLanguageValue());
    }
}

final class TestableOpenWeatherMap extends OpenWeatherMap
{
    public function configurableResource(): ConfigurableResource
    {
        /** @var ConfigurableResource */
        return $this->resource(ConfigurableResource::class);
    }
}

final class ConfigurableResource extends Resource
{
    use WithLanguage;
    use WithUnits;

    public function resolvedLanguageValue(): string
    {
        return $this->resolvedLanguage();
    }

    public function resolvedUnitsValue(): Units
    {
        return $this->resolvedUnits();
    }
}
