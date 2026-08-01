<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class City implements EntityInterface
{
    private function __construct(
        private readonly ?int $id,
        private readonly ?string $name,
        private readonly ?float $latitude,
        private readonly ?float $longitude,
        private readonly ?string $countryCode,
        private readonly ?int $population,
        private readonly ?int $timezoneOffset,
        private readonly ?\DateTimeImmutable $sunriseAt,
        private readonly ?\DateTimeImmutable $sunsetAt,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            id: $reader->nullableInt('id'),
            name: $reader->nullableString('name'),
            latitude: $reader->nullableFloat('coord.lat'),
            longitude: $reader->nullableFloat('coord.lon'),
            countryCode: $reader->nullableString('country'),
            population: $reader->nullableInt('population'),
            timezoneOffset: $reader->nullableInt('timezone'),
            sunriseAt: $reader->nullableTimestamp('sunrise'),
            sunsetAt: $reader->nullableTimestamp('sunset'),
        );
    }

    public function id(): ?int
    {
        return $this->id;
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

    public function population(): ?int
    {
        return $this->population;
    }

    public function timezoneOffset(): ?int
    {
        return $this->timezoneOffset;
    }

    public function sunriseAt(): ?\DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    public function sunsetAt(): ?\DateTimeImmutable
    {
        return $this->sunsetAt;
    }
}
