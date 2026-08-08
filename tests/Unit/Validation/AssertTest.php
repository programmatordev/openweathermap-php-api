<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Validation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class AssertTest extends TestCase
{
    #[DataProvider('nonNegativeIntegers')]
    public function testItAcceptsNonNegativeIntegers(int $value): void
    {
        self::assertSame(
            $value,
            Assert::nonNegativeInteger($value, 'tile zoom'),
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
        $this->expectExceptionMessage('The tile zoom must be zero or greater.');

        Assert::nonNegativeInteger(-1, 'tile zoom');
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
            'The tile X coordinate must be between 0 and 511 for zoom 9.',
        ];
        yield 'X above maximum' => [
            512,
            9,
            'x',
            'The tile X coordinate must be between 0 and 511 for zoom 9.',
        ];
        yield 'Y above zoom zero maximum' => [
            1,
            0,
            'y',
            'The tile Y coordinate must be between 0 and 0 for zoom 0.',
        ];
    }

    public function testTileCoordinateRejectsANegativeZoom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The tile zoom must be zero or greater.');

        Assert::tileCoordinate(0, -1, 'x');
    }
}
