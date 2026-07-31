<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Geocoding;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Value\Coordinates;

final class PostalLocation implements EntityInterface
{
    private function __construct(
        private readonly ?string $postalCode,
        private readonly ?string $name,
        private readonly ?Coordinates $coordinates,
        private readonly ?string $countryCode,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $latitude = $reader->nullableFloat('lat');
        $longitude = $reader->nullableFloat('lon');

        return new self(
            postalCode: $reader->nullableString('zip'),
            name: $reader->nullableString('name'),
            coordinates: $latitude === null || $longitude === null
                ? null
                : Coordinates::from($latitude, $longitude),
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

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    public function countryCode(): ?string
    {
        return $this->countryCode;
    }
}
