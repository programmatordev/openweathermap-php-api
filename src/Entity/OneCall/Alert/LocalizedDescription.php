<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class LocalizedDescription implements EntityInterface
{
    private function __construct(
        private readonly ?string $languageCode,
        private readonly ?string $text,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            languageCode: $reader->nullableString('language'),
            text: $reader->nullableString('description'),
        );
    }

    public function languageCode(): ?string
    {
        return $this->languageCode;
    }

    public function text(): ?string
    {
        return $this->text;
    }
}
