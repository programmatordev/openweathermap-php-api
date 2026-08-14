<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource\Concern;

use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

trait WithLanguage
{
    public function withLanguage(Language|string $language): static
    {
        // Raw strings allow new OpenWeather language codes without an enum release.
        if (is_string($language)) {
            $language = Assert::notBlank($language, 'language');
        }

        return $this->withConfig([
            OpenWeatherMap::OPTION_LANGUAGE => $language,
        ]);
    }

    protected function resolvedLanguage(): Language|string
    {
        return $this->runtime->config()->get(OpenWeatherMap::OPTION_LANGUAGE);
    }
}
