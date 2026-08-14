<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Timezone implements EntityInterface
{
    private function __construct(
        private readonly ?string $identifier,
        private readonly ?int $offsetSeconds,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            identifier: $reader->nullableString('timezone'),
            offsetSeconds: $reader->nullableInt('timezone_offset'),
        );
    }

    public function identifier(): ?string
    {
        return $this->identifier;
    }

    public function offsetSeconds(): ?int
    {
        return $this->offsetSeconds;
    }
}
