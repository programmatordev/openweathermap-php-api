<?php

namespace ProgrammatorDev\OpenWeatherMap;

use Http\Message\Authentication\QueryParam;
use ProgrammatorDev\Api\Api;
use ProgrammatorDev\Api\Event\PostRequestEvent;
use ProgrammatorDev\Api\Event\ResponseContentsEvent;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Language\Language;
use ProgrammatorDev\OpenWeatherMap\Resource\AirPollutionResource;
use ProgrammatorDev\OpenWeatherMap\Resource\GeocodingResource;
use ProgrammatorDev\OpenWeatherMap\Resource\OneCallResource;
use ProgrammatorDev\OpenWeatherMap\Resource\WeatherResource;
use ProgrammatorDev\OpenWeatherMap\UnitSystem\UnitSystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenWeatherMap extends Api
{
    private array $options;

    private OptionsResolver $optionsResolver;

    public function __construct(
        #[\SensitiveParameter] private readonly string $apiKey,
        array $options = []
    )
    {
        parent::__construct();

        $this->optionsResolver = new OptionsResolver();

        $this->options = $this->configureOptions($options);
        $this->configureApi();
    }

    public function oneCall(): OneCallResource
    {
        return new OneCallResource($this);
    }

    public function weather(): WeatherResource
    {
        return new WeatherResource($this);
    }

    public function airPollution(): AirPollutionResource
    {
        return new AirPollutionResource($this);
    }

    public function geocoding(): GeocodingResource
    {
        return new GeocodingResource($this);
    }

    private function configureOptions(array $options): array
    {
        $this->optionsResolver->setDefault('unitSystem', UnitSystem::METRIC);
        $this->optionsResolver->setDefault('language', Language::ENGLISH);

        $this->optionsResolver->setAllowedTypes('unitSystem', 'string');
        $this->optionsResolver->setAllowedTypes('language', 'string');

        $this->optionsResolver->setAllowedValues('unitSystem', UnitSystem::getOptions());

        return $this->optionsResolver->resolve($options);
    }

    private function configureApi(): void
    {
        $this->setBaseUrl('https://api.openweathermap.org');

        $this->setAuthentication(new QueryParam(['appid' => $this->apiKey]));

        $this->addQueryDefault('units', $this->options['unitSystem']);
        $this->addQueryDefault('lang', $this->options['language']);

        $this->addPostRequestListener(function(PostRequestEvent $event) {
            $response = $event->getResponse();
            $statusCode = $response->getStatusCode();

            // if there was a response with an error status code
            if ($statusCode >= 400) {
                $error = json_decode($response->getBody()->getContents(), true);

                match ($statusCode) {
                    400 => throw new BadRequestException($error),
                    401 => throw new UnauthorizedException($error),
                    404 => throw new NotFoundException($error),
                    429 => throw new TooManyRequestsException($error),
                    default => throw new UnexpectedErrorException($error)
                };
            }
        });

        $this->addResponseContentsListener(function(ResponseContentsEvent $event) {
            // decode json string response into an array
            $contents = $event->getContents();
            $contents = json_decode($contents, true);

            $event->setContents($contents);
        });
    }
}