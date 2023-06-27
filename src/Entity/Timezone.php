<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Timezone
{
    private ?string $identifier;

    private int $offset;

    public function __construct(array $data)
    {
        $this->offset = $data['timezone_offset'];

        // Optional data
        $this->identifier = $data['timezone'] ?? null;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}