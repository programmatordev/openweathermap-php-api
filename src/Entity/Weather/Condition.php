<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Condition implements EntityInterface
{
    private const ICON_URL = 'https://openweathermap.org/payload/api/media/file/%s@2x.png';

    private function __construct(
        private readonly ?int $id,
        private readonly ?string $group,
        private readonly ?string $description,
        private readonly ?string $icon,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            id: $reader->nullableInt('id'),
            group: $reader->nullableString('main'),
            description: $reader->nullableString('description'),
            icon: $reader->nullableString('icon'),
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function group(): ?string
    {
        return $this->group;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    public function iconUrl(): ?string
    {
        if ($this->icon === null) {
            return null;
        }

        return sprintf(self::ICON_URL, rawurlencode($this->icon));
    }
}
