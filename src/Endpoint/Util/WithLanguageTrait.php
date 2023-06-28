<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidLanguageException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateLanguageTrait;

trait WithLanguageTrait
{
    use ValidateLanguageTrait;

    /**
     * @throws InvalidLanguageException
     */
    public function withLanguage(string $language): static
    {
        $this->validateLanguage($language);

        $clone = clone $this;
        $clone->language = $language;

        return $clone;
    }
}