<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall\Alert;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert\LocalizedDescription;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;

final class LocalizedDescriptionTest extends TestCase
{
    public function testHydratesLocalizedDescription(): void
    {
        $description = LocalizedDescription::fromArray([
            'language' => 'es-CL',
            'description' => 'Precipitaciones moderadas',
        ]);

        self::assertSame('es-CL', $description->languageCode());
        self::assertSame('Precipitaciones moderadas', $description->text());
    }

    public function testToleratesMissingNullUnknownAndOpenLanguageValues(): void
    {
        $missing = LocalizedDescription::fromArray([]);

        self::assertNull($missing->languageCode());
        self::assertNull($missing->text());

        $description = LocalizedDescription::fromArray([
            'language' => 'x-agency-local',
            'description' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertSame('x-agency-local', $description->languageCode());
        self::assertNull($description->text());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        LocalizedDescription::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'language' => [
            ['language' => 1],
            '"language" expected string, int received.',
        ];
        yield 'text' => [
            ['description' => []],
            '"description" expected string, array received.',
        ];
    }
}
