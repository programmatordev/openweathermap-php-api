<?php

namespace ProgrammatorDev\OpenWeatherMap\Request\Stations;

use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Weather
{
    private readonly ?string $precipitation;

    private readonly ?string $descriptor;

    private readonly ?string $intensity;

    private readonly ?string $proximity;

    private readonly ?string $obscuration;

    private readonly ?string $other;

    public function __construct(
        ?string $precipitation = null,
        ?string $descriptor = null,
        ?string $intensity = null,
        ?string $proximity = null,
        ?string $obscuration = null,
        ?string $other = null,
    ) {
        $this->precipitation = Assert::nullableNotBlank(
            $precipitation,
            'weather precipitation',
        );
        $this->descriptor = Assert::nullableNotBlank(
            $descriptor,
            'weather descriptor',
        );
        $this->intensity = Assert::nullableNotBlank(
            $intensity,
            'weather intensity',
        );
        $this->proximity = Assert::nullableNotBlank(
            $proximity,
            'weather proximity',
        );
        $this->obscuration = Assert::nullableNotBlank(
            $obscuration,
            'weather obscuration',
        );
        $this->other = Assert::nullableNotBlank($other, 'other weather value');

        Assert::notEmpty($this->toArray(), 'weather values');
    }

    public function precipitation(): ?string
    {
        return $this->precipitation;
    }

    public function descriptor(): ?string
    {
        return $this->descriptor;
    }

    public function intensity(): ?string
    {
        return $this->intensity;
    }

    public function proximity(): ?string
    {
        return $this->proximity;
    }

    public function obscuration(): ?string
    {
        return $this->obscuration;
    }

    public function other(): ?string
    {
        return $this->other;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return array_filter([
            'precipitation' => $this->precipitation,
            'descriptor' => $this->descriptor,
            'intensity' => $this->intensity,
            'proximity' => $this->proximity,
            'obscuration' => $this->obscuration,
            'other' => $this->other,
        ], static fn(mixed $value): bool => $value !== null);
    }
}
