<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Geocoding;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class PostalLocation implements EntityInterface
{
    private function __construct(
        private readonly ?string $postalCode,
        private readonly ?string $name,
        private readonly ?float $latitude,
        private readonly ?float $longitude,
        private readonly ?string $countryCode,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        return new self(
            postalCode: $reader->nullableString('zip'),
            name: $reader->nullableString('name'),
            latitude: $reader->nullableFloat('lat'),
            longitude: $reader->nullableFloat('lon'),
            countryCode: $reader->nullableString('country'),
        );
    }

    public function postalCode(): ?string
    {
        return $this->postalCode;
    }

    public function name(): ?string
    {
        return $this->name;
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
}
