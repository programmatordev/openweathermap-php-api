<?php

namespace ProgrammatorDev\OpenWeatherMap\Request\Stations;

use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class CloudLayer
{
    private readonly ?float $distance;

    private readonly ?string $condition;

    private readonly ?string $cumulus;

    public function __construct(
        ?float $distance = null,
        ?string $condition = null,
        ?string $cumulus = null,
    ) {
        $this->distance = Assert::nullableFiniteNumber(
            $distance,
            'cloud layer distance',
        );
        $this->condition = Assert::nullableNotBlank(
            $condition,
            'cloud layer condition',
        );
        $this->cumulus = Assert::nullableNotBlank(
            $cumulus,
            'cloud layer cumulus type',
        );

        Assert::notEmpty($this->toArray(), 'cloud layer values');
    }

    public function distance(): ?float
    {
        return $this->distance;
    }

    public function condition(): ?string
    {
        return $this->condition;
    }

    public function cumulus(): ?string
    {
        return $this->cumulus;
    }

    /**
     * @return array<string, float|string>
     */
    public function toArray(): array
    {
        return array_filter([
            'distance' => $this->distance,
            'condition' => $this->condition,
            'cumulus' => $this->cumulus,
        ], static fn(mixed $value): bool => $value !== null);
    }
}
