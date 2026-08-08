<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Enum;

use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Enum\MapLayer;

final class MapLayerTest extends TestCase
{
    public function testItContainsTheDocumentedWeatherMapLayers(): void
    {
        self::assertSame([
            'CLOUDS' => 'clouds_new',
            'PRECIPITATION' => 'precipitation_new',
            'PRESSURE' => 'pressure_new',
            'WIND' => 'wind_new',
            'TEMPERATURE' => 'temp_new',
        ], array_combine(
            array_column(MapLayer::cases(), 'name'),
            array_column(MapLayer::cases(), 'value'),
        ));
    }
}
