<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

trait WithCacheInvalidationTrait
{
    public function withCacheInvalidation(): static
    {
        $clone = clone $this;
        $clone->cacheInvalidation = true;

        return $clone;
    }

    public function isCacheInvalidation(): bool
    {
        return $this->cacheInvalidation;
    }
}