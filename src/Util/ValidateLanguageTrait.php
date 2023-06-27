<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidLanguageException;
use ProgrammatorDev\OpenWeatherMap\Language;

trait ValidateLanguageTrait
{
    /**
     * @throws InvalidLanguageException
     */
    private function validateLanguage(string $language): void
    {
        $options = Language::getList();

        if ( ! in_array($language, $options)) {
            throw new InvalidLanguageException(
                \sprintf(
                    'The value "%s" is invalid. Accepted values are: "%s".',
                    $language,
                    implode('", "', $options)
                )
            );
        }
    }
}