<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidChoiceException;
use ProgrammatorDev\OpenWeatherMap\Language;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateChoiceTrait;

trait WithLanguageTrait
{
    use ValidateChoiceTrait;

    /**
     * @throws InvalidChoiceException
     */
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