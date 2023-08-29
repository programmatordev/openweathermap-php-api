<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\Language\Language;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

trait LanguageTrait
{
    /**
     * @throws ValidationException
     */
    public function withLanguage(string $language): static
    {
        Validator::choice(Language::getList())->assert($language, 'language');

        $clone = clone $this;
        $clone->language = $language;

        return $clone;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}