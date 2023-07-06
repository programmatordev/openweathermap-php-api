<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

trait WithCacheTtlTrait
{
    public function withCacheTtl(\DateInterval|int|null $time): static
    {
        $clone = clone $this;
        $clone->cacheTtl = $time;

        return $clone;
    }

    public function getCacheTtl(): \DateInterval|int|null
    {
        return $this->cacheTtl;
    }
}