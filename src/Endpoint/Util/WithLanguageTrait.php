<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\Language\Language;
use ProgrammatorDev\OpenWeatherMap\Validator\ChoiceValidatorTrait;

trait WithLanguageTrait
{
    use ChoiceValidatorTrait;

    public function withLanguage(string $language): static
    {
        $this->validateChoice('language', $language, Language::getList());

        $clone = clone $this;
        $clone->language = $language;

        return $clone;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}