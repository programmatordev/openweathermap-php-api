<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\Api\Api;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;

class OpenWeatherMap extends Api
{
    public const OPTION_LANGUAGE = 'language';
    public const OPTION_UNITS = 'units';

    private const BASE_URL = 'https://api.openweathermap.org';

    public function __construct(string $apiKey, array $options = [])
    {
        parent::__construct();

        $this->validateApiKey($apiKey);
        $this->validateOptions($options);

        $this->config($options, defaults: [
            self::OPTION_UNITS => Units::METRIC,
            self::OPTION_LANGUAGE => Language::ENGLISH,
        ]);

        $this->baseUrl(self::BASE_URL);
        $this->auth()->query('appid', $apiKey);
        $this->responses()->json();
    }

    private function validateApiKey(string $apiKey): void
    {
        if (trim($apiKey) === '') {
            throw new \InvalidArgumentException('The API key must be a non-empty string.');
        }
    }

    private function validateOptions(array $options): void
    {
        $unknownOptions = array_diff(
            array_keys($options),
            [self::OPTION_LANGUAGE, self::OPTION_UNITS]
        );

        if ($unknownOptions !== []) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown OpenWeatherMap option%s: %s.',
                count($unknownOptions) === 1 ? '' : 's',
                implode(', ', $unknownOptions)
            ));
        }

        if (array_key_exists(self::OPTION_UNITS, $options)) {
            $this->validateUnits($options[self::OPTION_UNITS]);
        }

        if (array_key_exists(self::OPTION_LANGUAGE, $options)) {
            $this->validateLanguage($options[self::OPTION_LANGUAGE]);
        }
    }

    private function validateUnits(mixed $units): void
    {
        if (!$units instanceof Units) {
            throw new \InvalidArgumentException(sprintf(
                'The "%s" option must be an instance of %s.',
                self::OPTION_UNITS,
                Units::class
            ));
        }
    }

    private function validateLanguage(mixed $language): void
    {
        if (!$language instanceof Language && !is_string($language)) {
            throw new \InvalidArgumentException(sprintf(
                'The "%s" option must be an instance of %s or a string.',
                self::OPTION_LANGUAGE,
                Language::class
            ));
        }

        if (is_string($language) && trim($language) === '') {
            throw new \InvalidArgumentException(sprintf(
                'The "%s" option must not be an empty string.',
                self::OPTION_LANGUAGE
            ));
        }
    }
}
