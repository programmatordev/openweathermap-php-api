<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource\Concern;

use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

trait WithLanguage
{
    // Null means this resource inherits the API-wide language.
    private Language|string|null $languageOverride = null;

    public function withLanguage(Language|string $language): static
    {
        // Raw strings allow new OpenWeather language codes without an enum release.
        if (is_string($language) && trim($language) === '') {
            throw new \InvalidArgumentException('The language must not be an empty string.');
        }

        $clone = clone $this;
        $clone->languageOverride = $language;

        return $clone;
    }

    protected function resolvedLanguage(): string
    {
        $language = $this->languageOverride ?? $this->runtime->config()->get(OpenWeatherMap::OPTION_LANGUAGE);

        return $language instanceof Language ? $language->value : $language;
    }
}
