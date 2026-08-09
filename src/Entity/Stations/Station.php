<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Stations;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Station implements EntityInterface
{
    private function __construct(
        private readonly ?string $id,
        private readonly ?\DateTimeImmutable $createdAt,
        private readonly ?\DateTimeImmutable $updatedAt,
        private readonly ?string $externalId,
        private readonly ?string $name,
        private readonly ?Coordinates $coordinates,
        private readonly ?float $altitude,
        private readonly ?int $rank,
        private readonly ?string $userId,
        private readonly ?int $sourceType,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $latitude = $reader->nullableFloat('latitude');
        $longitude = $reader->nullableFloat('longitude');

        // Registration uniquely returns an uppercase ID; list, retrieve, and
        // update responses use the conventional lowercase field.
        $id = $reader->nullableString('id');
        $registrationId = $reader->nullableString('ID');

        $hasCoordinates = array_key_exists('latitude', $data)
            || array_key_exists('longitude', $data);

        return new self(
            id: $id ?? $registrationId,
            createdAt: $reader->nullableDateTime('created_at'),
            updatedAt: $reader->nullableDateTime('updated_at'),
            externalId: $reader->nullableString('external_id'),
            name: $reader->nullableString('name'),
            coordinates: $hasCoordinates
                ? Coordinates::fromArray([
                    'lat' => $latitude,
                    'lon' => $longitude,
                ], $context)
                : null,
            altitude: $reader->nullableFloat('altitude'),
            rank: $reader->nullableInt('rank'),
            userId: $reader->nullableString('user_id'),
            sourceType: $reader->nullableInt('source_type'),
        );
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function createdAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function externalId(): ?string
    {
        return $this->externalId;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    public function altitude(): ?float
    {
        return $this->altitude;
    }

    public function rank(): ?int
    {
        return $this->rank;
    }

    public function userId(): ?string
    {
        return $this->userId;
    }

    public function sourceType(): ?int
    {
        return $this->sourceType;
    }
}
