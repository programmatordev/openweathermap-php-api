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
use ProgrammatorDev\OpenWeatherMap\Resource\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Resource\Geocoding;
use ProgrammatorDev\OpenWeatherMap\Resource\Maps;
use ProgrammatorDev\OpenWeatherMap\Resource\OneCall;
use ProgrammatorDev\OpenWeatherMap\Resource\Weather;
use ProgrammatorDev\OpenWeatherMap\Response\PayloadDecoder;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

class OpenWeatherMap extends Api
{
    public const AUTHENTICATION_KEY = 'appid';
    public const OPTION_LANGUAGE = 'language';
    public const OPTION_UNITS = 'units';

    private const BASE_URL = 'https://api.openweathermap.org';

    private readonly string $apiKey;

    public function __construct(
        #[\SensitiveParameter] string $apiKey,
        array $options = [],
    )
    {
        parent::__construct();

        $this->apiKey = $this->validateApiKey($apiKey);
        $options = $this->validateOptions($options);

        $this->config($options, defaults: [
            self::OPTION_UNITS => Units::METRIC,
            self::OPTION_LANGUAGE => Language::ENGLISH,
        ]);

        $this->baseUrl(self::BASE_URL);
        $this->auth()->query(self::AUTHENTICATION_KEY, $this->apiKey);
        $this->responses()->custom(new PayloadDecoder());

        $this->errors()->when(static fn (ErrorContext $context): ?ApiException => match (true) {
            $context->statusCode() === 400 => BadRequestException::fromContext($context),
            $context->statusCode() === 401 => UnauthorizedException::fromContext($context),
            $context->statusCode() === 404 => NotFoundException::fromContext($context),
            $context->statusCode() === 429 => TooManyRequestsException::fromContext($context),
            $context->statusCode() >= 400 && $context->statusCode() <= 599 => UnexpectedErrorException::fromContext($context),
            default => null,
        });
    }

    public function airPollution(): AirPollution
    {
        return $this->resource(AirPollution::class);
    }

    public function geocoding(): Geocoding
    {
        return $this->resource(Geocoding::class);
    }

    public function maps(): Maps
    {
        return $this->resourceWith(
            Maps::class,
            apiKey: $this->apiKey,
        );
    }

    public function oneCall(): OneCall
    {
        return $this->resource(OneCall::class);
    }

    public function weather(): Weather
    {
        return $this->resource(Weather::class);
    }

    private function validateApiKey(
        #[\SensitiveParameter] string $apiKey,
    ): string
    {
        return Assert::notBlank($apiKey, 'API key');
    }

    private function validateOptions(array $options): array
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
            $options[self::OPTION_LANGUAGE] = $this->validateLanguage(
                $options[self::OPTION_LANGUAGE],
            );
        }

        return $options;
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

    private function validateLanguage(mixed $language): Language|string
    {
        if (!$language instanceof Language && !is_string($language)) {
            throw new \InvalidArgumentException(sprintf(
                'The "%s" option must be an instance of %s or a string.',
                self::OPTION_LANGUAGE,
                Language::class
            ));
        }

        return is_string($language)
            ? Assert::notBlank($language, sprintf('"%s" option', self::OPTION_LANGUAGE))
            : $language;
    }
}
