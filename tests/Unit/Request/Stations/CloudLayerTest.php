<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Request\Stations;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\CloudLayer;

final class CloudLayerTest extends TestCase
{
    public function testMapsAvailableCloudLayerValues(): void
    {
        $cloud = new CloudLayer(
            distance: 1200,
            condition: ' BKN ',
            cumulus: ' CB ',
        );

        self::assertSame(1200.0, $cloud->distance());
        self::assertSame('BKN', $cloud->condition());
        self::assertSame('CB', $cloud->cumulus());
        self::assertSame([
            'distance' => 1200.0,
            'condition' => 'BKN',
            'cumulus' => 'CB',
        ], $cloud->toArray());
    }

    public function testOmitsUnavailableCloudLayerValues(): void
    {
        $cloud = new CloudLayer(condition: 'NSC');

        self::assertNull($cloud->distance());
        self::assertSame('NSC', $cloud->condition());
        self::assertNull($cloud->cumulus());
        self::assertSame(['condition' => 'NSC'], $cloud->toArray());
    }

    public function testRejectsACloudLayerWithoutValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The cloud layer values must not be empty.',
        );

        new CloudLayer();
    }

    public function testRejectsANonFiniteDistance(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The cloud layer distance must be a finite number.',
        );

        new CloudLayer(distance: INF);
    }

    #[DataProvider('blankStringValues')]
    public function testRejectsABlankStringValue(
        ?string $condition,
        ?string $cumulus,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        new CloudLayer(condition: $condition, cumulus: $cumulus);
    }

    public static function blankStringValues(): iterable
    {
        yield 'condition' => [
            '   ',
            null,
            'The cloud layer condition must be a non-empty string.',
        ];
        yield 'cumulus' => [
            null,
            '   ',
            'The cloud layer cumulus type must be a non-empty string.',
        ];
    }
}
