<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\Api\Api;
use ProgrammatorDev\Api\Context\ErrorContext;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiException;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Resource\Geocoding;

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

        // Exact status handlers run first.
        // SDK conditional handlers run for every response,
        // so null explicitly means that no API error matched.
        $this->errors()->statuses([
            400 => static fn (ErrorContext $context): BadRequestException => BadRequestException::fromContext($context),
            401 => static fn (ErrorContext $context): UnauthorizedException => UnauthorizedException::fromContext($context),
            404 => static fn (ErrorContext $context): NotFoundException => NotFoundException::fromContext($context),
            429 => static fn (ErrorContext $context): TooManyRequestsException => TooManyRequestsException::fromContext($context),
        ])->when(static fn (ErrorContext $context): ?ApiException => match (true) {
            $context->statusCode() >= 400 && $context->statusCode() <= 599 => UnexpectedErrorException::fromContext($context),
            default => null,
        });
    }

    public function geocoding(): Geocoding
    {
        return $this->resource(Geocoding::class);
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
