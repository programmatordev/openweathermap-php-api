<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

trait WithCacheTtlTrait
{
    public function withCacheTtl(?int $time): static
    {
        $clone = clone $this;
        $clone->cacheTtl = $time;

        return $clone;
    }

    public function getCacheTtl(): ?int
    {
        return $this->cacheTtl;
    }
}