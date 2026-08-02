<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Coordinates implements EntityInterface
{
    private function __construct(
        private readonly ?float $latitude,
        private readonly ?float $longitude,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            latitude: $reader->nullableFloat('lat'),
            longitude: $reader->nullableFloat('lon'),
        );
    }

    public function latitude(): ?float
    {
        return $this->latitude;
    }

    public function longitude(): ?float
    {
        return $this->longitude;
    }
}
