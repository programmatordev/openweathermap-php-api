<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Timezone
{
    private ?string $identifier;

    private int $offset;

    public function __construct(array $data)
    {
        $this->identifier = $data['timezone'] ?? null;
        $this->offset = $data['timezone_offset'];
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