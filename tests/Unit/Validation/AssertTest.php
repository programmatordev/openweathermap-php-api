<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Validation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class AssertTest extends TestCase
{
    #[DataProvider('finiteNumbers')]
    public function testItAcceptsFiniteNumbers(float $value): void
    {
        self::assertSame($value, Assert::finiteNumber($value, 'value'));
    }

    public static function finiteNumbers(): iterable
    {
        yield 'negative' => [-10.5];
        yield 'zero' => [0.0];
        yield 'positive' => [10.5];
    }

    #[DataProvider('nonFiniteNumbers')]
    public function testItRejectsNonFiniteNumbers(float $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be a finite number.');

        Assert::finiteNumber($value, 'value');
    }

    public static function nonFiniteNumbers(): iterable
    {
        yield 'negative infinity' => [-INF];
        yield 'positive infinity' => [INF];
        yield 'not a number' => [NAN];
    }

    #[DataProvider('nonNegativeIntegers')]
    public function testItAcceptsNonNegativeIntegers(int $value): void
    {
        self::assertSame(
            $value,
            Assert::nonNegativeInteger($value, 'value'),
        );
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function nonNegativeIntegers(): iterable
    {
        yield 'zero' => [0];
        yield 'positive integer' => [9];
    }

    public function testItRejectsNegativeIntegers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be zero or greater.');

        Assert::nonNegativeInteger(-1, 'value');
    }

    #[DataProvider('validTileCoordinates')]
    public function testItAcceptsCoordinatesWithinTheZoomRange(
        int $coordinate,
        int $zoom,
    ): void {
        self::assertSame(
            $coordinate,
            Assert::tileCoordinate($coordinate, $zoom, 'x'),
        );
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    public static function validTileCoordinates(): iterable
    {
        yield 'zoom zero origin' => [0, 0];
        yield 'zoom nine origin' => [0, 9];
        yield 'zoom nine maximum' => [511, 9];
        yield 'zoom twenty center' => [524288, 20];
        yield 'zoom twenty maximum' => [1048575, 20];
    }

    #[DataProvider('invalidTileCoordinates')]
    public function testItRejectsCoordinatesOutsideTheZoomRange(
        int $coordinate,
        int $zoom,
        string $axis,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        Assert::tileCoordinate($coordinate, $zoom, $axis);
    }

    /**
     * @return iterable<string, array{int, int, string, string}>
     */
    public static function invalidTileCoordinates(): iterable
    {
        yield 'negative X' => [
            -1,
            9,
            'x',
            'At zoom level 9, the tile X coordinate must be between 0 and 511.',
        ];
        yield 'X above maximum' => [
            512,
            9,
            'x',
            'At zoom level 9, the tile X coordinate must be between 0 and 511.',
        ];
        yield 'Y above zoom zero maximum' => [
            1,
            0,
            'y',
            'At zoom level 0, the tile Y coordinate must be 0.',
        ];
    }

    public function testTileCoordinateRejectsANegativeZoom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The tile zoom level must be zero or greater.',
        );

        Assert::tileCoordinate(0, -1, 'x');
    }
}
