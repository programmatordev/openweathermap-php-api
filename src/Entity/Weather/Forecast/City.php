<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class City implements EntityInterface
{
    private function __construct(
        private readonly ?int $id,
        private readonly ?string $name,
        private readonly ?Coordinates $coordinates,
        private readonly ?string $countryCode,
        private readonly ?int $population,
        private readonly ?int $timezoneOffset,
        private readonly ?\DateTimeImmutable $sunriseAt,
        private readonly ?\DateTimeImmutable $sunsetAt,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $coordinates = $reader->nullableArray('coord');

        return new self(
            id: $reader->nullableInt('id'),
            name: $reader->nullableString('name'),
            coordinates: $coordinates === null
                ? null
                : Coordinates::fromArray($coordinates, $context),
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

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
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
