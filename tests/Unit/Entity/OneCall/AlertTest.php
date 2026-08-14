<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert\LocalizedDescription;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class AlertTest extends TestCase
{
    public function testHydratesCapturedAlert(): void
    {
        $alert = Alert::fromArray(Fixture::json('one-call/alert/chile-rain.json'));

        self::assertSame(
            'urn:oid:2.49.0.0.152.0.2026.7.31.14.20.43:f1076d7511a15522d5a6e41917020bc0',
            $alert->id(),
        );
        self::assertSame('Dirección Meteorológica de Chile', $alert->senderName());
        self::assertSame('', $alert->event());
        self::assertSame(1785578400, $alert->startsAt()?->getTimestamp());
        self::assertSame('UTC', $alert->startsAt()?->getTimezone()->getName());
        self::assertSame(1785708000, $alert->endsAt()?->getTimestamp());
        self::assertCount(1, $alert->descriptions());
        self::assertContainsOnlyInstancesOf(
            LocalizedDescription::class,
            $alert->descriptions(),
        );
        self::assertSame('es-CL', $alert->descriptions()[0]->languageCode());
        self::assertSame(
            'Precipitaciones Normales a Moderadas en zonas de las regiones de '
            .'La Araucanía, Los Ríos y Los Lagos',
            $alert->descriptions()[0]->text(),
        );
        self::assertSame(
            $alert->descriptions()[0]->text(),
            $alert->description('es-CL'),
        );
        self::assertNull($alert->description('en-US'));
        self::assertSame(['Rain'], $alert->tags());
    }

    #[DataProvider('capturedAlerts')]
    public function testHydratesOtherCapturedLocalizedAlerts(
        string $fixture,
        string $senderName,
        string $language,
        string $tag,
    ): void {
        $alert = Alert::fromArray(Fixture::json($fixture));

        self::assertSame($senderName, $alert->senderName());
        self::assertSame('', $alert->event());
        self::assertSame($language, $alert->descriptions()[0]->languageCode());
        self::assertNotSame('', $alert->descriptions()[0]->text());
        self::assertSame([$tag], $alert->tags());
    }

    public function testReturnsFirstDescriptionMatchingLanguage(): void
    {
        $alert = Alert::fromArray([
            'description' => [
                ['language' => 'en-US', 'description' => 'First description'],
                ['language' => 'pt-PT', 'description' => 'Descrição'],
                ['language' => 'en-US', 'description' => 'Second description'],
            ],
        ]);

        self::assertSame('First description', $alert->description('en-US'));
        self::assertSame('Descrição', $alert->description('pt-PT'));
        self::assertNull($alert->description('es-ES'));
    }

    public static function capturedAlerts(): iterable
    {
        yield 'Houston air quality' => [
            'one-call/alert/houston-air-quality.json',
            'NWS Houston/Galveston TX',
            'en-US',
            'Air quality',
        ];
        yield 'Phoenix extreme heat' => [
            'one-call/alert/phoenix-extreme-heat.json',
            'NWS Phoenix AZ',
            'en-US',
            'Extreme high temperature',
        ];
        yield 'Tokyo thunderstorm' => [
            'one-call/alert/tokyo-thunderstorm.json',
            'JMA',
            'ja-JP',
            'Thunderstorm',
        ];
    }

    public function testNormalizesDocumentedStringDescription(): void
    {
        $alert = Alert::fromArray([
            'description' => 'Documented alert description',
        ]);

        self::assertCount(1, $alert->descriptions());
        self::assertNull($alert->descriptions()[0]->languageCode());
        self::assertSame('Documented alert description', $alert->descriptions()[0]->text());
        self::assertNull($alert->description('en-US'));
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Alert::fromArray([]);

        self::assertNull($missing->id());
        self::assertNull($missing->senderName());
        self::assertNull($missing->event());
        self::assertNull($missing->startsAt());
        self::assertNull($missing->endsAt());
        self::assertSame([], $missing->descriptions());
        self::assertSame([], $missing->tags());

        $alert = Alert::fromArray([
            'id' => null,
            'sender_name' => null,
            'event' => '',
            'start' => null,
            'description' => [
                ['language' => null, 'description' => null, 'unknown' => true],
            ],
            'tags' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($alert->id());
        self::assertSame('', $alert->event());
        self::assertCount(1, $alert->descriptions());
        self::assertNull($alert->descriptions()[0]->languageCode());
        self::assertNull($alert->descriptions()[0]->text());
        self::assertSame([], $alert->tags());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Alert::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'ID' => [
            ['id' => 1],
            '"id" expected string, int received.',
        ];
        yield 'sender name' => [
            ['sender_name' => []],
            '"sender_name" expected string, array received.',
        ];
        yield 'event' => [
            ['event' => 1],
            '"event" expected string, int received.',
        ];
        yield 'start' => [
            ['start' => '1785578400'],
            '"start" expected int, string received.',
        ];
        yield 'end' => [
            ['end' => 1785708000.5],
            '"end" expected int, float received.',
        ];
        yield 'description shape' => [
            ['description' => 1],
            '"description" expected string|array, int received.',
        ];
        yield 'description member' => [
            ['description' => ['invalid']],
            '"description.0" expected array, string received.',
        ];
        yield 'description language' => [
            ['description' => [['language' => 1]]],
            '"language" expected string, int received.',
        ];
        yield 'description text' => [
            ['description' => [['description' => []]]],
            '"description" expected string, array received.',
        ];
        yield 'tags' => [
            ['tags' => 'Rain'],
            '"tags" expected array, string received.',
        ];
        yield 'tag member' => [
            ['tags' => [1]],
            '"tags.0" expected string, int received.',
        ];
    }
}
