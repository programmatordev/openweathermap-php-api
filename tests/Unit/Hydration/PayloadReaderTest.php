<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Hydration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class PayloadReaderTest extends TestCase
{
    public function testItReadsSupportedNullableValues(): void
    {
        $reader = PayloadReader::from([
            'name' => 'Lisbon',
            'timezone' => 3600,
            'temperature' => 20,
            'cloudiness' => 12.5,
            'daylight' => true,
            'rain' => ['1h' => 0.4],
        ], 'Weather');

        self::assertSame('Lisbon', $reader->nullableString('name'));
        self::assertSame(3600, $reader->nullableInt('timezone'));
        self::assertSame(20.0, $reader->nullableFloat('temperature'));
        self::assertSame(12.5, $reader->nullableFloat('cloudiness'));
        self::assertTrue($reader->nullableBool('daylight'));
        self::assertSame(['1h' => 0.4], $reader->nullableArray('rain'));
    }

    public function testMissingAndNullValuesAreTolerated(): void
    {
        $reader = PayloadReader::from(['name' => null], 'Weather');

        self::assertNull($reader->nullableString('missing'));
        self::assertNull($reader->nullableString('name'));
        self::assertNull($reader->nullableTimestamp('observed_at'));
    }

    public function testUnknownFieldsAreIgnored(): void
    {
        $reader = PayloadReader::from([
            'name' => 'Lisbon',
            'undocumented' => new \stdClass(),
        ], 'Weather');

        self::assertSame('Lisbon', $reader->nullableString('name'));
    }

    public function testItReadsNestedFieldPaths(): void
    {
        $reader = PayloadReader::from([
            'main' => [
                'temp' => 30,
            ],
        ], 'Weather');

        self::assertSame(30.0, $reader->nullableFloat('main.temp'));
    }

    public function testItRequiresIntermediateValuesToBeArrays(): void
    {
        $reader = PayloadReader::from(['main' => 'unexpected'], 'Weather');

        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(
            'Cannot hydrate Weather: "main" expected array, string received.'
        );

        $reader->nullableFloat('main.temp');
    }

    public function testItHydratesTimestampsAsImmutableUtcValues(): void
    {
        $reader = PayloadReader::from(['observed_at' => 1700000000], 'Weather');

        $timestamp = $reader->nullableTimestamp('observed_at');

        self::assertInstanceOf(\DateTimeImmutable::class, $timestamp);
        self::assertSame('UTC', $timestamp->getTimezone()->getName());
        self::assertSame('2023-11-14T22:13:20+00:00', $timestamp->format(\DateTimeInterface::ATOM));
    }

    #[DataProvider('invalidValueProvider')]
    public function testItRejectsKnownFieldsWithInvalidTypes(
        string $method,
        mixed $value,
        string $expectedType,
        string $receivedType
    ): void {
        $reader = PayloadReader::from([
            'main' => [
                'value' => $value,
            ],
        ], 'Weather');

        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(sprintf(
            'Cannot hydrate Weather: "main.value" expected %s, %s received.',
            $expectedType,
            $receivedType
        ));

        $reader->{$method}('main.value');
    }

    /**
     * @return iterable<string, array{string, mixed, string, string}>
     */
    public static function invalidValueProvider(): iterable
    {
        yield 'string' => ['nullableString', 1, 'string', 'int'];
        yield 'integer' => ['nullableInt', 1.5, 'int', 'float'];
        yield 'float' => ['nullableFloat', '20.5', 'int|float', 'string'];
        yield 'boolean' => ['nullableBool', 1, 'bool', 'int'];
        yield 'array' => ['nullableArray', new \stdClass(), 'array', 'stdClass'];
        yield 'timestamp' => ['nullableTimestamp', '1700000000', 'int', 'string'];
    }
}
