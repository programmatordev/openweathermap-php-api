<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Forecast;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirQuality;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Concern\HasAirQuality;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Period implements EntityInterface
{
    use HasAirQuality;

    private function __construct(
        private readonly ?\DateTimeImmutable $dateTime,
        private readonly AirQuality $airQuality,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            dateTime: $reader->nullableTimestamp('dt'),
            airQuality: AirQuality::fromArray($data, $context),
        );
    }

    public function dateTime(): ?\DateTimeImmutable
    {
        return $this->dateTime;
    }
}
