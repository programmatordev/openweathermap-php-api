<?php

namespace ProgrammatorDev\OpenWeatherMap\Response;

final class MapTile
{
    public function __construct(
        private readonly string $contents,
        private readonly string $contentType,
    ) {}

    public function contents(): string
    {
        return $this->contents;
    }

    public function contentType(): string
    {
        return $this->contentType;
    }
}
