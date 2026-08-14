<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Geocoding;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Location implements EntityInterface
{
    /**
     * @param array<string, string> $localNames
     */
    private function __construct(
        private readonly ?string $name,
        private readonly array $localNames,
        private readonly ?float $latitude,
        private readonly ?float $longitude,
        private readonly ?string $countryCode,
        private readonly ?string $state,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $localNames = $reader->nullableArray('local_names') ?? [];

        foreach ($localNames as $language => $name) {
            if (!is_string($name)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('local_names.%s', $language),
                    'string',
                    $name,
                );
            }
        }

        return new self(
            name: $reader->nullableString('name'),
            localNames: $localNames,
            latitude: $reader->nullableFloat('lat'),
            longitude: $reader->nullableFloat('lon'),
            countryCode: $reader->nullableString('country'),
            state: $reader->nullableString('state'),
        );
    }

    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * @return array<string, string>
     */
    public function localNames(): array
    {
        return $this->localNames;
    }

    public function localName(string $languageCode): ?string
    {
        return $this->localNames[$languageCode] ?? null;
    }

    public function latitude(): ?float
    {
        return $this->latitude;
    }

    public function longitude(): ?float
    {
        return $this->longitude;
    }

    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    public function state(): ?string
    {
        return $this->state;
    }
}
