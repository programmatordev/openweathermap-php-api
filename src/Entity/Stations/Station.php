<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Stations;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Station implements EntityInterface
{
    private function __construct(
        private readonly string $id,
        private readonly \DateTimeImmutable $createdAt,
        private readonly \DateTimeImmutable $updatedAt,
        private readonly string $externalId,
        private readonly string $name,
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly float $altitude,
        private readonly int $rank,
        private readonly ?string $userId,
        private readonly ?int $sourceType,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        // Registration uniquely returns an uppercase ID; list, retrieve, and
        // update responses use the conventional lowercase field.
        $id = $reader->nullableString('id');
        $registrationId = $reader->nullableString('ID');

        if ($id === null && $registrationId === null) {
            throw HydrationException::invalidType(
                self::class,
                'id',
                'string',
                null,
            );
        }

        return new self(
            id: $id ?? $registrationId,
            createdAt: $reader->requiredDateTime('created_at'),
            updatedAt: $reader->requiredDateTime('updated_at'),
            externalId: $reader->requiredString('external_id'),
            name: $reader->requiredString('name'),
            latitude: $reader->requiredFloat('latitude'),
            longitude: $reader->requiredFloat('longitude'),
            altitude: $reader->requiredFloat('altitude'),
            rank: $reader->requiredInt('rank'),
            userId: $reader->nullableString('user_id'),
            sourceType: $reader->nullableInt('source_type'),
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function externalId(): string
    {
        return $this->externalId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function altitude(): float
    {
        return $this->altitude;
    }

    public function rank(): int
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
